<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BinhLuan;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function index(Request $request)
    {
        $query = BinhLuan::with(['khachHang', 'sach']);

        if ($request->filled('status')) {
            $query->where('TrangThai', $request->status);
        }

        $comments = $query->orderBy('MaBL', 'desc')->paginate(15)->withQueryString();

        return view('admin.comments.index', compact('comments'));
    }

    public function approve($id)
    {
        $comment = BinhLuan::findOrFail($id);
        $comment->update(['TrangThai' => 'approved']);

        return back()->with('success', 'Đã duyệt bình luận thành công! Bình luận hiện đã hiển thị công khai.');
    }

    public function reject($id)
    {
        $comment = BinhLuan::findOrFail($id);
        $comment->update(['TrangThai' => 'rejected']);

        return back()->with('info', 'Đã từ chối bình luận.');
    }

    public function destroy($id)
    {
        $comment = BinhLuan::findOrFail($id);
        $comment->delete();

        return back()->with('success', 'Đã xóa bình luận.');
    }
}
