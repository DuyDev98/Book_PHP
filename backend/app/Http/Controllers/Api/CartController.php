<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ChiTietGioHang;
use App\Models\GioHang;
use App\Models\Sach;
use Illuminate\Http\Request;

class CartController extends Controller
{
    /**
     * Lấy hoặc tạo mới giỏ hàng của khách hàng hiện tại
     */
    private function getOrCreateCart($user)
    {
        $khachHang = $user->khachHang;
        if (!$khachHang) {
            return null;
        }

        $cart = GioHang::firstOrCreate(
            ['MaKH' => $khachHang->MaKH],
            ['NgayTao' => now()]
        );

        return $cart;
    }

    /**
     * Xem giỏ hàng của user
     */
    public function index(Request $request)
    {
        $cart = $this->getOrCreateCart($request->user());
        if (!$cart) {
            return response()->json([
                'success' => true,
                'data' => [
                    'MaGioHang' => null,
                    'items' => [],
                    'total_items' => 0,
                    'total_amount' => 0,
                ],
            ]);
        }

        $items = ChiTietGioHang::where('MaGioHang', $cart->MaGioHang)
            ->with(['sach.tacGia'])
            ->get();

        $totalAmount = 0;
        $totalItems = 0;

        $formattedItems = $items->map(function ($item) use (&$totalAmount, &$totalItems) {
            $sach = $item->sach;
            $price = $sach ? ($sach->IsSale && $sach->GiaSale ? (float) $sach->GiaSale : (float) $sach->GiaBan) : 0;
            $subtotal = $price * $item->SoLuong;

            $totalAmount += $subtotal;
            $totalItems += $item->SoLuong;

            return [
                'MaSach' => $item->MaSach,
                'TenSach' => $sach ? $sach->TenSach : '',
                'AnhBia' => $sach ? $sach->AnhBia : null,
                'GiaBan' => $sach ? (float) $sach->GiaBan : 0,
                'GiaSale' => $sach ? (float) $sach->GiaSale : null,
                'IsSale' => $sach ? (bool) $sach->IsSale : false,
                'DonGia' => $price,
                'SoLuong' => $item->SoLuong,
                'SoLuongTon' => $sach ? $sach->SoLuongTon : 0,
                'ThanhTien' => $subtotal,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => [
                'MaGioHang' => $cart->MaGioHang,
                'items' => $formattedItems,
                'total_items' => $totalItems,
                'total_amount' => $totalAmount,
            ],
        ]);
    }

    /**
     * Thêm sách vào giỏ hàng
     */
    public function addToCart(Request $request)
    {
        $request->validate([
            'MaSach' => 'required|exists:sach,MaSach',
            'SoLuong' => 'nullable|integer|min:1',
        ]);

        $sach = Sach::findOrFail($request->MaSach);
        $soLuong = $request->get('SoLuong', 1);

        if ($sach->SoLuongTon < $soLuong) {
            return response()->json([
                'success' => false,
                'message' => 'Số lượng tồn kho không đủ (Hiện có ' . $sach->SoLuongTon . ' cuốn).',
            ], 400);
        }

        $cart = $this->getOrCreateCart($request->user());
        if (!$cart) {
            return response()->json([
                'success' => false,
                'message' => 'Tài khoản quản trị viên hoặc tài khoản chưa liên kết khách hàng không thể mua hàng.',
            ], 400);
        }

        $existing = ChiTietGioHang::where('MaGioHang', $cart->MaGioHang)
            ->where('MaSach', $sach->MaSach)
            ->first();

        if ($existing) {
            $newQuantity = $existing->SoLuong + $soLuong;
            if ($sach->SoLuongTon < $newQuantity) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tổng số lượng trong giỏ vượt quá tồn kho (Còn lại: ' . $sach->SoLuongTon . ').',
                ], 400);
            }
            ChiTietGioHang::where('MaGioHang', $cart->MaGioHang)
                ->where('MaSach', $sach->MaSach)
                ->update(['SoLuong' => $newQuantity]);
        } else {
            ChiTietGioHang::create([
                'MaGioHang' => $cart->MaGioHang,
                'MaSach' => $sach->MaSach,
                'SoLuong' => $soLuong,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Đã thêm sách vào giỏ hàng!',
        ]);
    }

    /**
     * Cập nhật số lượng của 1 món trong giỏ
     */
    public function updateQuantity(Request $request)
    {
        $request->validate([
            'MaSach' => 'required|exists:sach,MaSach',
            'SoLuong' => 'required|integer|min:1',
        ]);

        $cart = $this->getOrCreateCart($request->user());
        if (!$cart) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy giỏ hàng cho tài khoản này.',
            ], 400);
        }

        $sach = Sach::findOrFail($request->MaSach);

        if ($sach->SoLuongTon < $request->SoLuong) {
            return response()->json([
                'success' => false,
                'message' => 'Số lượng tồn kho không đủ để đặt (Còn: ' . $sach->SoLuongTon . ').',
            ], 400);
        }

        $item = ChiTietGioHang::where('MaGioHang', $cart->MaGioHang)
            ->where('MaSach', $request->MaSach)
            ->firstOrFail();

        ChiTietGioHang::where('MaGioHang', $cart->MaGioHang)
            ->where('MaSach', $request->MaSach)
            ->update(['SoLuong' => $request->SoLuong]);

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật số lượng thành công!',
        ]);
    }

    /**
     * Xóa 1 món khỏi giỏ
     */
    public function removeItem(Request $request, $maSach)
    {
        $cart = $this->getOrCreateCart($request->user());

        ChiTietGioHang::where('MaGioHang', $cart->MaGioHang)
            ->where('MaSach', $maSach)
            ->delete();

        return response()->json([
            'success' => true,
            'message' => 'Đã xóa sản phẩm khỏi giỏ hàng.',
        ]);
    }

    /**
     * Xóa toàn bộ giỏ hàng
     */
    public function clear(Request $request)
    {
        $cart = $this->getOrCreateCart($request->user());
        ChiTietGioHang::where('MaGioHang', $cart->MaGioHang)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Đã làm trống giỏ hàng.',
        ]);
    }
}
