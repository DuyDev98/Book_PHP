<?php

namespace App\Http\Controllers;

use App\Models\ChiTietDonHang;
use App\Models\ChiTietGioHang;
use App\Models\DonHang;
use App\Models\GioHang;
use App\Models\KhachHang;
use App\Models\Sach;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Màn hình thanh toán (Checkout)
     */
    public function checkout()
    {
        $user = Auth::user();
        $khachHang = $user->khachHang;

        if (!$khachHang) {
            $khachHang = KhachHang::create([
                'Username' => $user->Username,
                'HoTen' => $user->Username,
            ]);
        }

        $gioHang = GioHang::where('MaKH', $khachHang->MaKH)->first();
        if (!$gioHang) {
            return redirect()->route('cart.index')->with('error', 'Giỏ hàng của bạn đang trống!');
        }

        $items = ChiTietGioHang::with('sach')
            ->where('MaGioHang', $gioHang->MaGioHang)
            ->get();

        if ($items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Giỏ hàng của bạn đang trống!');
        }

        $subtotal = 0;
        foreach ($items as $item) {
            if ($item->sach) {
                if ($item->sach->SoLuongTon < $item->SoLuong) {
                    return redirect()->route('cart.index')->with('error', "Cuốn '{$item->sach->TenSach}' trong kho không còn đủ số lượng ({$item->sach->SoLuongTon} cuốn). Vui lòng cập nhật giỏ hàng!");
                }
                $subtotal += $item->sach->gia_hien_hanh * $item->SoLuong;
            }
        }

        $shippingFee = ($subtotal >= 300000) ? 0 : 30000;
        $total = $subtotal + $shippingFee;

        return view('client.checkout.index', compact('khachHang', 'items', 'subtotal', 'shippingFee', 'total'));
    }

    /**
     * Xử lý Đặt hàng (ACID Transaction, trừ tồn kho)
     */
    public function store(Request $request)
    {
        $request->validate([
            'ho_ten' => 'required|string|max:100',
            'so_dien_thoai' => 'required|regex:/^[0-9]{10,11}$/',
            'dia_chi' => 'required|string|max:255',
            'ghi_chu' => 'nullable|string|max:500',
        ], [
            'ho_ten.required' => 'Họ và tên người nhận là bắt buộc.',
            'so_dien_thoai.required' => 'Số điện thoại nhận hàng là bắt buộc.',
            'so_dien_thoai.regex' => 'Số điện thoại phải gồm 10 hoặc 11 chữ số hợp lệ.',
            'dia_chi.required' => 'Địa chỉ giao hàng là bắt buộc.',
        ]);

        $user = Auth::user();
        $khachHang = $user->khachHang;

        if (!$khachHang) {
            $khachHang = KhachHang::create([
                'Username' => $user->Username,
                'HoTen' => $request->ho_ten,
                'SDienThoai' => $request->so_dien_thoai,
                'DiaChi' => $request->dia_chi,
            ]);
        }

        $gioHang = GioHang::where('MaKH', $khachHang->MaKH)->first();
        if (!$gioHang) {
            return redirect()->route('cart.index')->with('error', 'Giỏ hàng của bạn đang trống!');
        }

        $items = ChiTietGioHang::where('MaGioHang', $gioHang->MaGioHang)->get();
        if ($items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Giỏ hàng của bạn đang trống!');
        }

        try {
            $donHang = DB::transaction(function () use ($items, $khachHang, $request, $gioHang) {
                $subtotal = 0;
                $orderItemsData = [];

                foreach ($items as $item) {
                    // Lock dòng dữ liệu để tránh Race Condition bán âm kho
                    $sach = Sach::where('MaSach', $item->MaSach)->lockForUpdate()->first();

                    if (!$sach) {
                        throw new \Exception("Có sản phẩm trong giỏ không còn tồn tại trên hệ thống.");
                    }

                    if ($sach->SoLuongTon < $item->SoLuong) {
                        throw new \Exception("Sách '{$sach->TenSach}' chỉ còn {$sach->SoLuongTon} cuốn trong kho, không đủ đặt hàng.");
                    }

                    $donGia = $sach->gia_hien_hanh;
                    $subtotal += $donGia * $item->SoLuong;

                    $orderItemsData[] = [
                        'sach' => $sach,
                        'soLuong' => $item->SoLuong,
                        'donGia' => $donGia,
                    ];
                }

                $shippingFee = ($subtotal >= 300000) ? 0 : 30000;
                $total = $subtotal + $shippingFee;

                $diaChiChiTiet = $request->dia_chi . " (Người nhận: {$request->ho_ten} - SĐT: {$request->so_dien_thoai})";

                // 1. Tạo Đơn hàng
                $donHang = DonHang::create([
                    'MaKH' => $khachHang->MaKH,
                    'NgayDat' => now(),
                    'TongTien' => $total,
                    'PhiVanChuyen' => $shippingFee,
                    'DiaChiGiaoHang' => $diaChiChiTiet,
                    'GhiChuGiaoHang' => $request->ghi_chu,
                    'TrangThai' => 'ChoDuyet',
                ]);

                // 2. Tạo Chi tiết đơn hàng và Trừ tồn kho
                foreach ($orderItemsData as $orderItem) {
                    ChiTietDonHang::create([
                        'MaDH' => $donHang->MaDH,
                        'MaSach' => $orderItem['sach']->MaSach,
                        'SoLuong' => $orderItem['soLuong'],
                        'DonGia' => $orderItem['donGia'],
                    ]);

                    // Trừ tồn kho ngay lập tức
                    $orderItem['sach']->decrement('SoLuongTon', $orderItem['soLuong']);
                }

                // 3. Xóa sạch giỏ hàng của khách sau khi đã đặt hàng thành công
                ChiTietGioHang::where('MaGioHang', $gioHang->MaGioHang)->delete();

                return $donHang;
            });

            return redirect()->route('orders.show', $donHang->MaDH)
                ->with('success', "Đặt hàng thành công! Mã đơn hàng của bạn là #DH-{$donHang->MaDH}. Chúng tôi sẽ sớm liên hệ giao hàng.");

        } catch (\Exception $e) {
            return redirect()->route('cart.index')->with('error', $e->getMessage());
        }
    }

    /**
     * Lịch sử đơn hàng của khách hàng
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $khachHang = $user->khachHang;

        $orders = collect();
        if ($khachHang) {
            $query = DonHang::where('MaKH', $khachHang->MaKH)->with(['chiTiets.sach']);

            if ($request->filled('status')) {
                $query->where('TrangThai', $request->status);
            }

            $orders = $query->orderBy('MaDH', 'desc')->paginate(8)->withQueryString();
        }

        return view('client.orders.index', compact('orders'));
    }

    /**
     * Xem chi tiết 1 đơn hàng
     */
    public function show($id)
    {
        $user = Auth::user();
        $donHang = DonHang::with(['chiTiets.sach.tacGia', 'khachHang'])->findOrFail($id);

        // Bảo mật phân quyền: Khách chỉ được xem đơn hàng của chính mình (Admin thì xem được tất cả)
        if (!$user->isAdmin()) {
            if (!$user->khachHang || $donHang->MaKH !== $user->khachHang->MaKH) {
                abort(403, 'Bạn không có quyền xem thông tin đơn hàng này.');
            }
        }

        return view('client.orders.show', compact('donHang'));
    }

    /**
     * Khách hàng hủy đơn hàng (khi còn ở trạng thái ChoDuyet) và Hoàn kho tự động
     */
    public function cancel($id)
    {
        $user = Auth::user();
        $donHang = DonHang::findOrFail($id);

        if (!$user->isAdmin()) {
            if (!$user->khachHang || $donHang->MaKH !== $user->khachHang->MaKH) {
                abort(403, 'Bạn không có quyền thao tác trên đơn hàng này.');
            }
        }

        if ($donHang->TrangThai !== 'ChoDuyet') {
            return back()->with('error', 'Chỉ có thể hủy đơn hàng khi đơn đang ở trạng thái Chờ Duyệt.');
        }

        $result = $donHang->huyDonHang();

        if ($result) {
            return back()->with('success', 'Đơn hàng đã được hủy thành công. Số lượng sản phẩm đã được tự động hoàn lại kho.');
        }

        return back()->with('error', 'Không thể hủy đơn hàng này.');
    }
}
