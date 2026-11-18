<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ChiTietDonHang;
use App\Models\ChiTietGioHang;
use App\Models\DonHang;
use App\Models\GioHang;
use App\Models\Sach;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    /**
     * Khách hàng đặt hàng (Checkout từ Giỏ hàng hoặc Mua ngay)
     */
    public function create(Request $request)
    {
        $user = $request->user();
        $khachHang = $user->khachHang;

        if (!$khachHang) {
            return response()->json([
                'success' => false,
                'message' => 'Hồ sơ khách hàng không tồn tại.',
            ], 400);
        }

        $validator = Validator::make($request->all(), [
            'DiaChiGiaoHang' => 'required|string|max:255',
            'GhiChuGiaoHang' => 'nullable|string',
            'PhiVanChuyen' => 'nullable|numeric|min:0',
            // Danh sách items mua: nếu không gửi, lấy toàn bộ từ giỏ hàng
            'items' => 'nullable|array',
            'items.*.MaSach' => 'required_with:items|exists:sach,MaSach',
            'items.*.SoLuong' => 'required_with:items|integer|min:1',
        ], [
            'DiaChiGiaoHang.required' => 'Vui lòng cung cấp địa chỉ giao hàng.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ.',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            return DB::transaction(function () use ($request, $khachHang) {
                $orderItems = [];

                if ($request->has('items') && count($request->items) > 0) {
                    $orderItems = $request->items;
                } else {
                    // Lấy từ giỏ hàng hiện tại
                    $cart = GioHang::where('MaKH', $khachHang->MaKH)->first();
                    if (!$cart) {
                        throw new \Exception('Giỏ hàng của bạn đang trống.');
                    }

                    $cartDetails = ChiTietGioHang::where('MaGioHang', $cart->MaGioHang)->get();
                    if ($cartDetails->isEmpty()) {
                        throw new \Exception('Giỏ hàng của bạn đang trống.');
                    }

                    foreach ($cartDetails as $detail) {
                        $orderItems[] = [
                            'MaSach' => $detail->MaSach,
                            'SoLuong' => $detail->SoLuong,
                        ];
                    }
                }

                $tongTienHang = 0;
                $validatedItems = [];

                // 1. Kiểm tra tồn kho và tính toán giá
                foreach ($orderItems as $item) {
                    // Khóa dòng để tránh race condition khi nhiều người mua cùng lúc
                    $sach = Sach::where('MaSach', $item['MaSach'])->lockForUpdate()->first();

                    if (!$sach) {
                        throw new \Exception("Không tìm thấy thông tin cuốn sách ID: {$item['MaSach']}.");
                    }

                    if ($sach->SoLuongTon < $item['SoLuong']) {
                        throw new \Exception("Sách '{$sach->TenSach}' không đủ số lượng trong kho (Hiện còn: {$sach->SoLuongTon}).");
                    }

                    // Giá bán thực tế (GiaSale nếu đang khuyến mãi, hoặc GiaBan)
                    $donGia = ($sach->IsSale && $sach->GiaSale) ? (float) $sach->GiaSale : (float) $sach->GiaBan;
                    $thanhTien = $donGia * $item['SoLuong'];
                    $tongTienHang += $thanhTien;

                    $validatedItems[] = [
                        'sach' => $sach,
                        'MaSach' => $sach->MaSach,
                        'SoLuong' => $item['SoLuong'],
                        'DonGia' => $donGia,
                    ];
                }

                $phiVanChuyen = (float) $request->get('PhiVanChuyen', 30000); // Mặc định phí ship 30.000đ
                $tongThanhToan = $tongTienHang + $phiVanChuyen;

                // 2. Tạo Đơn hàng
                $donHang = DonHang::create([
                    'MaKH' => $khachHang->MaKH,
                    'NgayDat' => now(),
                    'TongTien' => $tongThanhToan,
                    'PhiVanChuyen' => $phiVanChuyen,
                    'DiaChiGiaoHang' => $request->DiaChiGiaoHang,
                    'GhiChuGiaoHang' => $request->GhiChuGiaoHang,
                    'TrangThai' => 'ChoDuyet',
                ]);

                // 3. Tạo Chi tiết Đơn hàng & Trừ tồn kho
                $cart = GioHang::where('MaKH', $khachHang->MaKH)->first();

                foreach ($validatedItems as $vItem) {
                    ChiTietDonHang::create([
                        'MaDH' => $donHang->MaDH,
                        'MaSach' => $vItem['MaSach'],
                        'SoLuong' => $vItem['SoLuong'],
                        'DonGia' => $vItem['DonGia'],
                    ]);

                    // Trừ tồn kho
                    $vItem['sach']->decrement('SoLuongTon', $vItem['SoLuong']);

                    // Xóa món đã mua khỏi giỏ hàng
                    if ($cart) {
                        ChiTietGioHang::where('MaGioHang', $cart->MaGioHang)
                            ->where('MaSach', $vItem['MaSach'])
                            ->delete();
                    }
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Đặt hàng thành công! Đơn hàng đang được chờ duyệt.',
                    'data' => $donHang->load('chiTiets.sach'),
                ], 201);
            });
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể tạo đơn hàng: ' . $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Khách hàng xem danh sách đơn hàng của mình
     */
    public function myOrders(Request $request)
    {
        $khachHang = $request->user()->khachHang;
        if (!$khachHang) {
            return response()->json(['success' => true, 'data' => []]);
        }

        $orders = DonHang::with(['chiTiets.sach.tacGia'])
            ->where('MaKH', $khachHang->MaKH)
            ->orderBy('NgayDat', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $orders,
        ]);
    }

    /**
     * Xem chi tiết 1 đơn hàng
     */
    public function show(Request $request, $id)
    {
        $user = $request->user();
        $query = DonHang::with(['chiTiets.sach.tacGia', 'khachHang']);

        if (!$user->isAdmin()) {
            $khachHang = $user->khachHang;
            if (!$khachHang) {
                return response()->json(['success' => false, 'message' => 'Không có quyền truy cập.'], 403);
            }
            $query->where('MaKH', $khachHang->MaKH);
        }

        $order = $query->find($id);

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy đơn hàng yêu cầu.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $order,
        ]);
    }

    /**
     * Khách hàng hủy đơn (Tự động hoàn tồn kho)
     */
    public function cancel(Request $request, $id)
    {
        $user = $request->user();
        $order = DonHang::with('chiTiets')->find($id);

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy đơn hàng.',
            ], 404);
        }

        // Kiểm tra quyền hủy
        if (!$user->isAdmin()) {
            $khachHang = $user->khachHang;
            if (!$khachHang || $order->MaKH !== $khachHang->MaKH) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn không có quyền hủy đơn hàng này.',
                ], 403);
            }

            if (!in_array($order->TrangThai, ['ChoDuyet', 'Chờ xác nhận', 'Đang xử lý'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Đơn hàng đang giao hoặc đã hoàn thành, không thể hủy.',
                ], 400);
            }
        }

        if ($order->huyDonHang()) {
            return response()->json([
                'success' => true,
                'message' => 'Đã hủy đơn hàng thành công và tự động hoàn lại sách vào kho!',
                'data' => $order->fresh(),
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Không thể hủy đơn hàng này.',
        ], 400);
    }

    // ================= DÀNH CHO ADMIN =================

    /**
     * Admin xem toàn bộ danh sách đơn hàng
     */
    public function adminIndex(Request $request)
    {
        $query = DonHang::with(['khachHang', 'chiTiets.sach'])
            ->orderBy('NgayDat', 'desc');

        if ($request->filled('status')) {
            $query->where('TrangThai', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('MaDH', $search)
                  ->orWhereHas('khachHang', function ($qKH) use ($search) {
                      $qKH->where('HoTen', 'like', "%{$search}%")
                          ->orWhere('SDienThoai', 'like', "%{$search}%");
                  });
            });
        }

        $orders = $query->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $orders->items(),
            'pagination' => [
                'current_page' => $orders->currentPage(),
                'last_page' => $orders->lastPage(),
                'per_page' => $orders->perPage(),
                'total' => $orders->total(),
            ],
        ]);
    }

    /**
     * Admin cập nhật trạng thái đơn hàng (Duyệt, Đang giao, Đã giao, Hủy)
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'TrangThai' => 'required|string|in:ChoDuyet,Đang xử lý,Đang giao,Đã giao,Đã hủy',
        ]);

        $order = DonHang::with('chiTiets')->findOrFail($id);
        $newStatus = $request->TrangThai;

        if ($newStatus === 'Đã hủy') {
            $order->huyDonHang();
        } else {
            $order->TrangThai = $newStatus;
            $order->save();
        }

        return response()->json([
            'success' => true,
            'message' => "Cập nhật trạng thái đơn hàng #{$order->MaDH} thành '{$order->TrangThai}'!",
            'data' => $order->fresh(),
        ]);
    }

    /**
     * Admin xóa 1 món lẻ trong đơn hàng (Tự động hoàn kho món đó & tính lại tổng tiền)
     */
    public function deleteItem(Request $request, $id, $bookId)
    {
        $order = DonHang::with('chiTiets')->findOrFail($id);

        if (in_array($order->TrangThai, ['Đã giao', 'Đã hủy'])) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể xóa món trong đơn đã giao hoặc đã hủy.',
            ], 400);
        }

        $chiTiet = ChiTietDonHang::where('MaDH', $id)->where('MaSach', $bookId)->firstOrFail();

        DB::transaction(function () use ($order, $chiTiet, $bookId) {
            // Hoàn lại kho sách
            Sach::where('MaSach', $bookId)->increment('SoLuongTon', $chiTiet->SoLuong);

            // Xóa món
            $amountToDeduct = $chiTiet->SoLuong * $chiTiet->DonGia;
            ChiTietDonHang::where('MaDH', $order->MaDH)->where('MaSach', $bookId)->delete();

            // Tính lại tổng tiền
            $order->TongTien = max(0, $order->TongTien - $amountToDeduct);
            $order->save();
        });

        return response()->json([
            'success' => true,
            'message' => 'Đã xóa món khỏi đơn hàng, hoàn trả vào kho và tính lại tổng tiền.',
            'data' => $order->fresh(['chiTiets.sach']),
        ]);
    }
}
