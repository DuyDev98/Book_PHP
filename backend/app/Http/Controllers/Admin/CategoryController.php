<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DanhMuc;
use App\Models\Sach;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = DanhMuc::with(['parent'])
            ->withCount('sachs')
            ->orderBy('MaDanhMuc', 'desc')
            ->paginate(15);

        $parentCategories = DanhMuc::whereNull('ParentID')->get();

        return view('admin.categories.index', compact('categories', 'parentCategories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'ten_danh_muc' => 'required|string|max:100|unique:danhmuc,TenDanhMuc',
            'parent_id' => 'nullable|exists:danhmuc,MaDanhMuc',
        ], [
            'ten_danh_muc.required' => 'Tên danh mục không được để trống.',
            'ten_danh_muc.unique' => 'Tên danh mục này đã tồn tại.',
        ]);

        DanhMuc::create([
            'TenDanhMuc' => trim($request->ten_danh_muc),
            'ParentID' => $request->parent_id ?: null,
        ]);

        return back()->with('success', 'Thêm danh mục mới thành công!');
    }

    public function update(Request $request, $id)
    {
        $category = DanhMuc::findOrFail($id);

        $request->validate([
            'ten_danh_muc' => 'required|string|max:100|unique:danhmuc,TenDanhMuc,' . $id . ',MaDanhMuc',
            'parent_id' => 'nullable|exists:danhmuc,MaDanhMuc',
        ]);

        if ($request->parent_id == $id) {
            return back()->with('error', 'Một danh mục không thể làm danh mục cha của chính nó.');
        }

        $category->update([
            'TenDanhMuc' => trim($request->ten_danh_muc),
            'ParentID' => $request->parent_id ?: null,
        ]);

        return back()->with('success', 'Cập nhật danh mục thành công!');
    }

    public function destroy($id)
    {
        $category = DanhMuc::findOrFail($id);

        if (Sach::where('MaDanhMuc', $id)->exists()) {
            return back()->with('error', 'Không thể xóa danh mục này vì đang có sách thuộc danh mục.');
        }

        if (DanhMuc::where('ParentID', $id)->exists()) {
            return back()->with('error', 'Không thể xóa danh mục này vì đang có các danh mục con trực thuộc.');
        }

        $category->delete();

        return back()->with('success', 'Đã xóa danh mục thành công.');
    }
}
