<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NhaXuatBan;
use App\Models\Sach;
use Illuminate\Http\Request;

class PublisherController extends Controller
{
    public function index(Request $request)
    {
        $query = NhaXuatBan::withCount('sachs');

        if ($request->filled('keyword')) {
            $query->where('TenNXB', 'like', '%' . trim($request->keyword) . '%');
        }

        $publishers = $query->orderBy('MaNXB', 'desc')->paginate(15)->withQueryString();

        return view('admin.publishers.index', compact('publishers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'ten_nxb' => 'required|string|max:100',
            'nam_thanh_lap' => 'nullable|integer',
            'dia_chi' => 'nullable|string|max:100',
        ], [
            'ten_nxb.required' => 'Tên nhà xuất bản không được để trống.',
        ]);

        NhaXuatBan::create([
            'TenNXB' => trim($request->ten_nxb),
            'NamThanhLap' => $request->nam_thanh_lap,
            'DiaChi' => $request->dia_chi,
        ]);

        return back()->with('success', 'Thêm nhà xuất bản thành công!');
    }

    public function update(Request $request, $id)
    {
        $publisher = NhaXuatBan::findOrFail($id);

        $request->validate([
            'ten_nxb' => 'required|string|max:100',
            'nam_thanh_lap' => 'nullable|integer',
            'dia_chi' => 'nullable|string|max:100',
        ]);

        $publisher->update([
            'TenNXB' => trim($request->ten_nxb),
            'NamThanhLap' => $request->nam_thanh_lap,
            'DiaChi' => $request->dia_chi,
        ]);

        return back()->with('success', 'Cập nhật nhà xuất bản thành công!');
    }

    public function destroy($id)
    {
        $publisher = NhaXuatBan::findOrFail($id);

        if (Sach::where('MaNXB', $id)->exists()) {
            return back()->with('error', 'Không thể xóa nhà xuất bản này vì đã có sách liên kết.');
        }

        $publisher->delete();

        return back()->with('success', 'Đã xóa nhà xuất bản thành công.');
    }
}
