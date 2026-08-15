<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DanhMuc;
use App\Models\LoaiSach;
use App\Models\NhaXuatBan;
use App\Models\TacGia;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    // ================= DANH MỤC =================
    public function getCategories()
    {
        // Lấy danh mục gốc kèm danh mục con (Cây danh mục đa cấp) và đếm số sách
        $categories = DanhMuc::with('children')->withCount('sachs')->whereNull('ParentID')->get();
        $allCategories = DanhMuc::withCount('sachs')->get();

        return response()->json([
            'success' => true,
            'data' => [
                'tree' => $categories,
                'list' => $allCategories,
            ],
        ]);
    }

    public function storeCategory(Request $request)
    {
        $request->validate([
            'TenDanhMuc' => 'required|string|max:100',
            'ParentID' => 'nullable|exists:danhmuc,MaDanhMuc',
        ]);

        $category = DanhMuc::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Tạo danh mục mới thành công!',
            'data' => $category,
        ], 201);
    }

    public function updateCategory(Request $request, $id)
    {
        $category = DanhMuc::findOrFail($id);
        $request->validate([
            'TenDanhMuc' => 'required|string|max:100',
            'ParentID' => 'nullable|exists:danhmuc,MaDanhMuc',
        ]);

        $category->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật danh mục thành công!',
            'data' => $category,
        ]);
    }

    public function destroyCategory($id)
    {
        $category = DanhMuc::findOrFail($id);
        $category->delete();

        return response()->json([
            'success' => true,
            'message' => 'Đã xóa danh mục!',
        ]);
    }

    // ================= TÁC GIẢ =================
    public function getAuthors()
    {
        $authors = TacGia::withCount('sachs')->get();
        return response()->json(['success' => true, 'data' => $authors]);
    }

    public function storeAuthor(Request $request)
    {
        $request->validate([
            'TenTG' => 'required|string|max:100',
            'NamSinh' => 'nullable|integer',
            'QueQuan' => 'nullable|string|max:100',
            'NamMat' => 'nullable|integer',
        ]);

        $author = TacGia::create($request->all());
        return response()->json(['success' => true, 'message' => 'Thêm tác giả thành công!', 'data' => $author], 201);
    }

    public function updateAuthor(Request $request, $id)
    {
        $author = TacGia::findOrFail($id);
        $author->update($request->all());
        return response()->json(['success' => true, 'message' => 'Cập nhật tác giả thành công!', 'data' => $author]);
    }

    public function destroyAuthor($id)
    {
        TacGia::findOrFail($id)->delete();
        return response()->json(['success' => true, 'message' => 'Đã xóa tác giả!']);
    }

    // ================= NHÀ XUẤT BẢN =================
    public function getPublishers()
    {
        $publishers = NhaXuatBan::withCount('sachs')->get();
        return response()->json(['success' => true, 'data' => $publishers]);
    }

    public function storePublisher(Request $request)
    {
        $request->validate([
            'TenNXB' => 'required|string|max:100',
            'NamThanhLap' => 'nullable|integer',
            'DiaChi' => 'nullable|string|max:100',
        ]);

        $publisher = NhaXuatBan::create($request->all());
        return response()->json(['success' => true, 'message' => 'Thêm NXB thành công!', 'data' => $publisher], 201);
    }

    public function updatePublisher(Request $request, $id)
    {
        $publisher = NhaXuatBan::findOrFail($id);
        $publisher->update($request->all());
        return response()->json(['success' => true, 'message' => 'Cập nhật NXB thành công!', 'data' => $publisher]);
    }

    public function destroyPublisher($id)
    {
        NhaXuatBan::findOrFail($id)->delete();
        return response()->json(['success' => true, 'message' => 'Đã xóa NXB!']);
    }

    // ================= LOẠI SÁCH =================
    public function getBookTypes()
    {
        $types = LoaiSach::withCount('sachs')->get();
        return response()->json(['success' => true, 'data' => $types]);
    }

    public function storeBookType(Request $request)
    {
        $request->validate(['TenLoaiSach' => 'required|string|max:100']);
        $type = LoaiSach::create($request->all());
        return response()->json(['success' => true, 'message' => 'Thêm loại sách thành công!', 'data' => $type], 201);
    }

    public function updateBookType(Request $request, $id)
    {
        $type = LoaiSach::findOrFail($id);
        $type->update($request->all());
        return response()->json(['success' => true, 'message' => 'Cập nhật loại sách thành công!', 'data' => $type]);
    }

    public function destroyBookType($id)
    {
        LoaiSach::findOrFail($id)->delete();
        return response()->json(['success' => true, 'message' => 'Đã xóa loại sách!']);
    }
}
