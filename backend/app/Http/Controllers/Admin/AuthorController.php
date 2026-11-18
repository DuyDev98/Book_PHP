<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Sach;
use App\Models\TacGia;
use Illuminate\Http\Request;

class AuthorController extends Controller
{
    public function index(Request $request)
    {
        $query = TacGia::withCount('sachs');

        if ($request->filled('keyword')) {
            $query->where('TenTG', 'like', '%' . trim($request->keyword) . '%');
        }

        $authors = $query->orderBy('MaTG', 'desc')->paginate(15)->withQueryString();

        return view('admin.authors.index', compact('authors'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'ten_tg' => 'required|string|max:100',
            'nam_sinh' => 'nullable|integer',
            'nam_mat' => 'nullable|integer',
            'que_quan' => 'nullable|string|max:100',
        ], [
            'ten_tg.required' => 'Tên tác giả không được để trống.',
        ]);

        TacGia::create([
            'TenTG' => trim($request->ten_tg),
            'NamSinh' => $request->nam_sinh,
            'NamMat' => $request->nam_mat,
            'QueQuan' => $request->que_quan,
        ]);

        return back()->with('success', 'Thêm tác giả thành công!');
    }

    public function update(Request $request, $id)
    {
        $author = TacGia::findOrFail($id);

        $request->validate([
            'ten_tg' => 'required|string|max:100',
            'nam_sinh' => 'nullable|integer',
            'nam_mat' => 'nullable|integer',
            'que_quan' => 'nullable|string|max:100',
        ]);

        $author->update([
            'TenTG' => trim($request->ten_tg),
            'NamSinh' => $request->nam_sinh,
            'NamMat' => $request->nam_mat,
            'QueQuan' => $request->que_quan,
        ]);

        return back()->with('success', 'Cập nhật tác giả thành công!');
    }

    public function destroy($id)
    {
        $author = TacGia::findOrFail($id);

        if (Sach::where('MaTG', $id)->exists()) {
            return back()->with('error', 'Không thể xóa tác giả này vì đã có sách liên kết.');
        }

        $author->delete();

        return back()->with('success', 'Đã xóa tác giả thành công.');
    }
}
