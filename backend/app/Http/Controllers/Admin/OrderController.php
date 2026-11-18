<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChiTietDonHang;
use App\Models\DonHang;
use App\Models\Sach;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Danh sách đơn hàng trong hệ thống
     */
    public function index(Request $request)
    {
        $query = DonHang::with(['khachHang', 'chiTiets.sach']);

        if ($request->filled('status')) {
            $query->where('TrangThai', $request->status);
        }

        if ($request->filled('keyword')) {
            $keyword = trim($request->keyword);
            $query->where(function ($q) use ($keyword) {
                $q->where('MaDH', 'like', "%{$keyword}%")
                  ->orWhereHas('khachHang', function ($qKh) use ($keyword) {
                      $qKh->where('HoTen', 'like', "%{$keyword}%")
                          ->orWhere('SDienThoai', 'like', "%{$keyword}%");
                  });
            });
        }

        $orders = $query->orderBy('MaDH', 'desc')->paginate(12)->withQueryString();

        return view('admin.orders.index', compact('orders'));
    }

    /**
     * Chi tiết đơn hàng
     */
    public function show($id)
    {
        $donHang = DonHang::with(['khachHang', 'chiTiets.sach.tacGia'])->findOrFail($id);

        return view('admin.orders.show', compact('donHang'));
    }

    /**
     * Cập nhật trạng thái đơn hàng (Duyệt, Giao, Hoàn thành, Hủy kèm hoàn kho)
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'trang_thai' => 'required|in:ChoDuyet,DangXuLy,DangGiao,Đã giao,DaGiao,Đã hủy,DaHuy',
        ]);

        $donHang = DonHang::with('chiTiets')->findOrFail($id);
        $trangThaiCu = $donHang->TrangThai;
        $trangThaiMoi = $request->trang_thai;

        if ($trangThaiCu === $trangThaiMoi) {
            return back()->with('info', 'Trạng thái đơn hàng không thay đổi.');
        }

        DB::transaction(function () use ($donHang, $trangThaiCu, $trangThaiMoi) {
            // Nếu chuyển sang Đã hủy: Tự động hoàn kho cho toàn bộ sản phẩm
            if (in_array($trangThaiMoi, ['Đã hủy', 'DaHuy']) && !in_array($trangThaiCu, ['Đã hủy', 'DaHuy'])) {
                foreach ($donHang->chiTiets as $ct) {
                    Sach::where('MaSach', $ct->MaSach)->increment('SoLuongTon', $ct->SoLuong);
                }
            }

            // Nếu đơn hàng từ Đã hủy chuyển sang trạng thái khác (Khôi phục đơn): Cần trừ lại tồn kho
            if (in_array($trangThaiCu, ['Đã hủy', 'DaHuy']) && !in_array($trangThaiMoi, ['Đã hủy', 'DaHuy'])) {
                foreach ($donHang->chiTiets as $ct) {
                    Sach::where('MaSach', $ct->MaSach)->decrement('SoLuongTon', $ct->SoLuong);
                }
            }

            $donHang->TrangThai = $trangThaiMoi;
            $donHang->save();
        });

        return back()->with('success', "Cập nhật trạng thái đơn #DH-{$donHang->MaDH} thành '{$trangThaiMoi}' thành công!");
    }

    /**
     * Admin xóa món lẻ trong đơn: hoàn tồn kho món đó và tính lại tổng tiền
     */
    public function removeItem($orderId, $itemId)
    {
        $donHang = DonHang::with('chiTiets')->findOrFail($orderId);

        if (in_array($donHang->TrangThai, ['Đã giao', 'DaGiao', 'Đã hủy', 'DaHuy'])) {
            return back()->with('error', 'Không thể chỉnh sửa sản phẩm của đơn hàng đã hoàn tất hoặc đã hủy.');
        }

        $chiTiet = ChiTietDonHang::where('MaDH', $orderId)->where('MaSach', $itemId)->first();
        if (!$chiTiet) {
            return back()->with('error', 'Không tìm thấy sản phẩm trong đơn hàng.');
        }

        DB::transaction(function () use ($donHang, $chiTiet, $orderId, $itemId) {
            // 1. Hoàn lại tồn kho cho sản phẩm bị xóa
            Sach::where('MaSach', $itemId)->increment('SoLuongTon', $chiTiet->SoLuong);

            // 2. Xóa chi tiết
            ChiTietDonHang::where('MaDH', $orderId)->where('MaSach', $itemId)->delete();

            // 3. Tính lại tổng tiền đơn hàng
            $conLai = ChiTietDonHang::where('MaDH', $orderId)->get();
            if ($conLai->isEmpty()) {
                $donHang->TongTien = 0;
                $donHang->TrangThai = 'Đã hủy';
            } else {
                $subtotal = 0;
                foreach ($conLai as $item) {
                    $subtotal += $item->SoLuong * $item->DonGia;
                }
                $donHang->TongTien = $subtotal + $donHang->PhiVanChuyen;
            }
            $donHang->save();
        });

        return back()->with('success', 'Đã xóa món hàng khỏi đơn và tự động hoàn trả số lượng về kho.');
    }
}
