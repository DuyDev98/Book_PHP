<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BinhLuan;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    /**
     * Lấy danh sách bình luận đã duyệt của cuốn sách (Public)
     */
    public function getByBook($maSach)
    {
        $comments = BinhLuan::with('khachHang')
            ->where('MaSach', $maSach)
            ->where('TrangThai', 'approved')
            ->orderBy('NgayBinhLuan', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $comments,
        ]);
    }

    /**
     * Đăng bình luận mới (Khách hàng - mặc định trạng thái 'pending')
     */
    public function store(Request $request)
    {
        $request->validate([
            'MaSach' => 'required|exists:sach,MaSach',
            'NoiDung' => 'required|string|min:3|max:1000',
        ], [
            'NoiDung.required' => 'Vui lòng nhập nội dung đánh giá/bình luận.',
            'NoiDung.min' => 'Nội dung bình luận quá ngắn.',
        ]);

        $khachHang = $request->user()->khachHang;
        if (!$khachHang) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn cần có tài khoản khách hàng để bình luận.',
            ], 400);
        }

        $comment = BinhLuan::create([
            'MaKH' => $khachHang->MaKH,
            'MaSach' => $request->MaSach,
            'NoiDung' => $request->NoiDung,
            'NgayBinhLuan' => now(),
            'TrangThai' => 'pending', // Chờ duyệt theo thiết kế đồ án
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Gửi bình luận thành công! Bình luận của bạn đang được kiểm duyệt trước khi hiển thị.',
            'data' => $comment->load('khachHang'),
        ], 201);
    }

    /**
     * Admin xem danh sách bình luận (có filter trạng thái)
     */
    public function adminIndex(Request $request)
    {
        $query = BinhLuan::with(['khachHang', 'sach'])
            ->orderBy('NgayBinhLuan', 'desc');

        if ($request->filled('status')) {
            $query->where('TrangThai', $request->status);
        }

        $comments = $query->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $comments->items(),
            'pagination' => [
                'current_page' => $comments->currentPage(),
                'last_page' => $comments->lastPage(),
                'per_page' => $comments->perPage(),
                'total' => $comments->total(),
            ],
        ]);
    }

    /**
     * Admin duyệt hoặc từ chối bình luận
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'TrangThai' => 'required|in:approved,rejected,pending',
        ]);

        $comment = BinhLuan::findOrFail($id);
        $comment->TrangThai = $request->TrangThai;
        $comment->save();

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật trạng thái bình luận thành công!',
            'data' => $comment,
        ]);
    }

    /**
     * Admin xóa bình luận
     */
    public function destroy($id)
    {
        $comment = BinhLuan::findOrFail($id);
        $comment->delete();

        return response()->json([
            'success' => true,
            'message' => 'Đã xóa bình luận thành công.',
        ]);
    }
}
