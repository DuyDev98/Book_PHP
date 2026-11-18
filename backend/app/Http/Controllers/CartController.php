<?php

namespace App\Http\Controllers;

use App\Models\ChiTietGioHang;
use App\Models\GioHang;
use App\Models\KhachHang;
use App\Models\Sach;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    private function getOrCreateCart()
    {
        $user = Auth::user();
        $khachHang = $user->khachHang;

        if (!$khachHang) {
            $khachHang = KhachHang::create([
                'Username' => $user->Username,
                'HoTen' => $user->Username,
            ]);
        }

        return GioHang::firstOrCreate(
            ['MaKH' => $khachHang->MaKH],
            ['NgayTao' => now()]
        );
    }

    /**
     * Xem giỏ hàng
     */
    public function index()
    {
        $gioHang = $this->getOrCreateCart();
        $items = ChiTietGioHang::with(['sach.tacGia'])
            ->where('MaGioHang', $gioHang->MaGioHang)
            ->get();

        $subtotal = 0;
        foreach ($items as $item) {
            if ($item->sach) {
                $subtotal += $item->sach->gia_hien_hanh * $item->SoLuong;
            }
        }

        $shippingFee = ($subtotal >= 300000 || $subtotal == 0) ? 0 : 30000;
        $total = $subtotal + $shippingFee;

        return view('client.cart.index', compact('gioHang', 'items', 'subtotal', 'shippingFee', 'total'));
    }

    /**
     * Thêm sách vào giỏ
     */
    public function add(Request $request)
    {
        $request->validate([
            'ma_sach' => 'required|exists:sach,MaSach',
            'so_luong' => 'required|integer|min:1',
        ], [
            'ma_sach.required' => 'Mã sách không hợp lệ.',
            'ma_sach.exists' => 'Sách không tồn tại trong hệ thống.',
            'so_luong.required' => 'Vui lòng chọn số lượng cần mua.',
            'so_luong.integer' => 'Số lượng sách phải là số nguyên.',
            'so_luong.min' => 'Số lượng mua tối thiểu là 1 cuốn.',
        ]);

        $sach = Sach::findOrFail($request->ma_sach);
        $soLuongThem = (int) $request->so_luong;

        if ($sach->SoLuongTon < $soLuongThem) {
            return back()->with('error', "Rất tiếc! Số lượng sách trong kho chỉ còn {$sach->SoLuongTon} cuốn, không đủ đáp ứng.");
        }

        $gioHang = $this->getOrCreateCart();

        $chiTiet = ChiTietGioHang::where('MaGioHang', $gioHang->MaGioHang)
            ->where('MaSach', $sach->MaSach)
            ->first();

        if ($chiTiet) {
            $tongMoi = $chiTiet->SoLuong + $soLuongThem;
            if ($sach->SoLuongTon < $tongMoi) {
                return back()->with('error', "Bạn đã có {$chiTiet->SoLuong} cuốn trong giỏ. Kho chỉ còn {$sach->SoLuongTon} cuốn!");
            }
            $chiTiet->SoLuong = $tongMoi;
            $chiTiet->save();
        } else {
            ChiTietGioHang::create([
                'MaGioHang' => $gioHang->MaGioHang,
                'MaSach' => $sach->MaSach,
                'SoLuong' => $soLuongThem,
            ]);
        }

        return redirect()->route('cart.index')->with('success', "Đã thêm thành công '{$sach->TenSach}' vào giỏ hàng!");
    }

    /**
     * Cập nhật số lượng các món trong giỏ
     */
    public function update(Request $request)
    {
        $request->validate([
            'quantities' => 'required|array',
            'quantities.*' => 'required|integer|min:1',
        ], [
            'quantities.required' => 'Dữ liệu cập nhật giỏ hàng không hợp lệ.',
            'quantities.*.min' => 'Số lượng sản phẩm tối thiểu phải là 1.',
        ]);

        $gioHang = $this->getOrCreateCart();

        foreach ($request->quantities as $maSach => $qty) {
            $sach = Sach::find($maSach);
            if ($sach) {
                $qtyInt = max(1, (int) $qty);
                if ($sach->SoLuongTon < $qtyInt) {
                    return back()->with('error', "Cuốn '{$sach->TenSach}' chỉ còn {$sach->SoLuongTon} cuốn trong kho!");
                }

                ChiTietGioHang::where('MaGioHang', $gioHang->MaGioHang)
                    ->where('MaSach', $maSach)
                    ->update(['SoLuong' => $qtyInt]);
            }
        }

        return redirect()->route('cart.index')->with('success', 'Giỏ hàng đã được cập nhật thành công!');
    }

    /**
     * Xóa 1 cuốn khỏi giỏ
     */
    public function remove($id)
    {
        $gioHang = $this->getOrCreateCart();

        ChiTietGioHang::where('MaGioHang', $gioHang->MaGioHang)
            ->where('MaSach', $id)
            ->delete();

        return redirect()->route('cart.index')->with('success', 'Đã xóa sản phẩm khỏi giỏ hàng.');
    }

    /**
     * Làm trống giỏ hàng
     */
    public function clear()
    {
        $gioHang = $this->getOrCreateCart();

        ChiTietGioHang::where('MaGioHang', $gioHang->MaGioHang)->delete();

        return redirect()->route('cart.index')->with('success', 'Giỏ hàng của bạn đã được dọn sạch.');
    }
}
