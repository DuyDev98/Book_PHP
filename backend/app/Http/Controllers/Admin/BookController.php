<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChiTietDonHang;
use App\Models\DanhMuc;
use App\Models\LoaiSach;
use App\Models\NhaXuatBan;
use App\Models\Sach;
use App\Models\TacGia;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BookController extends Controller
{
    /**
     * Danh sách sách quản trị
     */
    public function index(Request $request)
    {
        $query = Sach::with(['tacGia', 'danhMuc', 'loaiSach', 'nhaXuatBan']);

        if ($request->filled('keyword')) {
            $keyword = trim($request->keyword);
            $query->where('TenSach', 'like', "%{$keyword}%");
        }

        if ($request->filled('category_id')) {
            $query->where('MaDanhMuc', $request->category_id);
        }

        if ($request->filled('stock_alert') && $request->stock_alert == 1) {
            $query->where('SoLuongTon', '<', 10);
        }

        $books = $query->orderBy('MaSach', 'desc')->paginate(10)->withQueryString();

        $categories = DanhMuc::all();

        return view('admin.books.index', compact('books', 'categories'));
    }

    /**
     * Màn hình thêm sách mới
     */
    public function create()
    {
        $categories = DanhMuc::all();
        $authors = TacGia::all();
        $publishers = NhaXuatBan::all();
        $types = LoaiSach::all();

        return view('admin.books.create', compact('categories', 'authors', 'publishers', 'types'));
    }

    /**
     * Lưu sách mới
     */
    public function store(Request $request)
    {
        $request->validate([
            'ten_sach' => 'required|string|max:200',
            'gia_ban' => 'required|numeric|min:0',
            'so_luong_ton' => 'required|integer|min:0',
            'phan_tram_giam_gia' => 'nullable|integer|min:0|max:100',
            'ma_danhmuc' => 'nullable|exists:danhmuc,MaDanhMuc',
            'ma_tg' => 'nullable|exists:tacgia,MaTG',
            'ma_nxb' => 'nullable|exists:nhaxuatban,MaNXB',
            'ma_loaisach' => 'nullable|exists:loaisach,MaLoaiSach',
            'nam_xuat_ban' => 'nullable|integer|min:1900|max:' . (date('Y') + 1),
            'lan_tai_ban' => 'nullable|integer|min:1',
            'mo_ta' => 'nullable|string',
            'anh_bia_file' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'anh_bia_url' => 'nullable|url|max:255',
        ], [
            'ten_sach.required' => 'Tên sách không được để trống.',
            'gia_ban.required' => 'Giá bán không được để trống.',
            'gia_ban.numeric' => 'Giá bán phải là số hợp lệ.',
            'gia_ban.min' => 'Giá bán không được nhỏ hơn 0.',
            'so_luong_ton.required' => 'Số lượng tồn kho không được để trống.',
            'so_luong_ton.min' => 'Số lượng tồn kho phải lớn hơn hoặc bằng 0.',
            'anh_bia_file.image' => 'File tải lên phải là hình ảnh hợp lệ (jpg, png, webp).',
            'anh_bia_file.max' => 'Dung lượng ảnh tối đa là 2MB.',
        ]);

        $anhBia = $request->anh_bia_url;

        // Xử lý upload ảnh nếu có file
        if ($request->hasFile('anh_bia_file')) {
            $file = $request->file('anh_bia_file');
            $fileName = time() . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/books'), $fileName);
            $anhBia = '/uploads/books/' . $fileName;
        }

        $sach = new Sach([
            'TenSach' => $request->ten_sach,
            'GiaBan' => $request->gia_ban,
            'SoLuongTon' => $request->so_luong_ton,
            'PhanTramGiamGia' => $request->phan_tram_giam_gia ?? 0,
            'MaDanhMuc' => $request->ma_danhmuc,
            'MaTG' => $request->ma_tg,
            'MaNXB' => $request->ma_nxb,
            'MaLoaiSach' => $request->ma_loaisach,
            'NamXuatBan' => $request->nam_xuat_ban,
            'LanTaiBan' => $request->lan_tai_ban,
            'MoTa' => $request->mo_ta,
            'AnhBia' => $anhBia,
        ]);

        $sach->tinhGiaSale();
        $sach->save();

        return redirect()->route('admin.books.index')->with('success', "Thêm sách '{$sach->TenSach}' thành công!");
    }

    /**
     * Màn hình sửa sách
     */
    public function edit($id)
    {
        $book = Sach::findOrFail($id);
        $categories = DanhMuc::all();
        $authors = TacGia::all();
        $publishers = NhaXuatBan::all();
        $types = LoaiSach::all();

        return view('admin.books.edit', compact('book', 'categories', 'authors', 'publishers', 'types'));
    }

    /**
     * Cập nhật sách
     */
    public function update(Request $request, $id)
    {
        $sach = Sach::findOrFail($id);

        $request->validate([
            'ten_sach' => 'required|string|max:200',
            'gia_ban' => 'required|numeric|min:0',
            'so_luong_ton' => 'required|integer|min:0',
            'phan_tram_giam_gia' => 'nullable|integer|min:0|max:100',
            'ma_danhmuc' => 'nullable|exists:danhmuc,MaDanhMuc',
            'ma_tg' => 'nullable|exists:tacgia,MaTG',
            'ma_nxb' => 'nullable|exists:nhaxuatban,MaNXB',
            'ma_loaisach' => 'nullable|exists:loaisach,MaLoaiSach',
            'nam_xuat_ban' => 'nullable|integer|min:1900|max:' . (date('Y') + 1),
            'lan_tai_ban' => 'nullable|integer|min:1',
            'mo_ta' => 'nullable|string',
            'anh_bia_file' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'anh_bia_url' => 'nullable|url|max:255',
        ]);

        $anhBia = $sach->AnhBia;

        if ($request->hasFile('anh_bia_file')) {
            $file = $request->file('anh_bia_file');
            $fileName = time() . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/books'), $fileName);
            $anhBia = '/uploads/books/' . $fileName;
        } elseif ($request->filled('anh_bia_url')) {
            $anhBia = $request->anh_bia_url;
        }

        $sach->fill([
            'TenSach' => $request->ten_sach,
            'GiaBan' => $request->gia_ban,
            'SoLuongTon' => $request->so_luong_ton,
            'PhanTramGiamGia' => $request->phan_tram_giam_gia ?? 0,
            'MaDanhMuc' => $request->ma_danhmuc,
            'MaTG' => $request->ma_tg,
            'MaNXB' => $request->ma_nxb,
            'MaLoaiSach' => $request->ma_loaisach,
            'NamXuatBan' => $request->nam_xuat_ban,
            'LanTaiBan' => $request->lan_tai_ban,
            'MoTa' => $request->mo_ta,
            'AnhBia' => $anhBia,
        ]);

        $sach->tinhGiaSale();
        $sach->save();

        return redirect()->route('admin.books.index')->with('success', "Cập nhật sách '{$sach->TenSach}' thành công!");
    }

    /**
     * Xóa sách (kiểm tra toàn vẹn dữ liệu)
     */
    public function destroy($id)
    {
        $sach = Sach::findOrFail($id);

        $hasOrders = ChiTietDonHang::where('MaSach', $id)->exists();
        if ($hasOrders) {
            return back()->with('error', "Không thể xóa cuốn sách này vì đã phát sinh giao dịch trong đơn hàng. Để đảm bảo lịch sử báo cáo, bạn có thể chỉnh số lượng tồn kho về 0 thay vì xóa.");
        }

        $ten = $sach->TenSach;
        $sach->delete();

        return redirect()->route('admin.books.index')->with('success', "Đã xóa sách '{$ten}' thành công!");
    }

    /**
     * Nhập thêm số lượng sách vào kho
     */
    public function restock(Request $request, $id)
    {
        $request->validate([
            'so_luong_them' => 'required|integer|min:1',
        ], [
            'so_luong_them.required' => 'Vui lòng nhập số lượng nhập kho.',
            'so_luong_them.min' => 'Số lượng nhập thêm phải tối thiểu là 1 cuốn.',
        ]);

        $sach = Sach::findOrFail($id);
        $sach->increment('SoLuongTon', (int) $request->so_luong_them);

        return back()->with('success', "Đã nhập thêm {$request->so_luong_them} cuốn cho sách '{$sach->TenSach}'. Tồn kho hiện tại: {$sach->SoLuongTon} cuốn.");
    }
}
