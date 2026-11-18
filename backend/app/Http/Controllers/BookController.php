<?php

namespace App\Http\Controllers;

use App\Models\BinhLuan;
use App\Models\DanhMuc;
use App\Models\LoaiSach;
use App\Models\NhaXuatBan;
use App\Models\Sach;
use App\Models\TacGia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookController extends Controller
{
    /**
     * Danh sách sách, tìm kiếm, lọc đa tiêu chí, phân trang
     */
    public function index(Request $request)
    {
        $query = Sach::with(['tacGia', 'danhMuc', 'loaiSach', 'nhaXuatBan']);

        // 1. Tìm kiếm theo từ khóa (Tên sách hoặc tên tác giả)
        if ($request->filled('keyword')) {
            $keyword = trim($request->keyword);
            $query->where(function ($q) use ($keyword) {
                $q->where('TenSach', 'like', "%{$keyword}%")
                  ->orWhereHas('tacGia', function ($qTg) use ($keyword) {
                      $qTg->where('TenTG', 'like', "%{$keyword}%");
                  });
            });
        }

        // 2. Lọc theo danh mục
        if ($request->filled('category_id')) {
            $query->where('MaDanhMuc', $request->category_id);
        }

        // 3. Lọc theo tác giả
        if ($request->filled('author_id')) {
            $query->where('MaTG', $request->author_id);
        }

        // 4. Lọc theo nhà xuất bản
        if ($request->filled('publisher_id')) {
            $query->where('MaNXB', $request->publisher_id);
        }

        // 5. Lọc theo thể loại/loại sách
        if ($request->filled('type_id')) {
            $query->where('MaLoaiSach', $request->type_id);
        }

        // 6. Lọc theo sách khuyến mãi
        if ($request->filled('is_sale') && $request->is_sale == 1) {
            $query->where('IsSale', true);
        }

        // 7. Lọc theo khoảng giá
        if ($request->filled('min_price')) {
            $query->where('GiaBan', '>=', (float) $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('GiaBan', '<=', (float) $request->max_price);
        }

        // 8. Sắp xếp
        $sort = $request->get('sort', 'newest');
        switch ($sort) {
            case 'price_asc':
                $query->orderBy('GiaBan', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('GiaBan', 'desc');
                break;
            case 'name_asc':
                $query->orderBy('TenSach', 'asc');
                break;
            case 'newest':
            default:
                $query->orderBy('MaSach', 'desc');
                break;
        }

        // 9. Phân trang 12 sản phẩm mỗi trang, kèm giữ nguyên query parameters
        $books = $query->paginate(12)->withQueryString();

        // Dữ liệu cho bộ lọc
        $categories = DanhMuc::withCount('sachs')->get();
        $authors = TacGia::withCount('sachs')->get();
        $publishers = NhaXuatBan::all();
        $types = LoaiSach::all();

        return view('client.books.index', compact('books', 'categories', 'authors', 'publishers', 'types'));
    }

    /**
     * Chi tiết sách & bình luận
     */
    public function show($id)
    {
        $book = Sach::with(['tacGia', 'nhaXuatBan', 'loaiSach', 'danhMuc'])->find($id);

        if (!$book) {
            abort(404, 'Không tìm thấy sách yêu cầu hoặc sách đã ngừng kinh doanh.');
        }

        // Chỉ hiển thị các bình luận đã được Quản trị viên duyệt (approved)
        $comments = BinhLuan::with('khachHang')
            ->where('MaSach', $id)
            ->where('TrangThai', 'approved')
            ->orderBy('NgayBinhLuan', 'desc')
            ->get();

        // Sách tương tự cùng danh mục
        $relatedBooks = Sach::where('MaDanhMuc', $book->MaDanhMuc)
            ->where('MaSach', '!=', $book->MaSach)
            ->take(4)
            ->get();

        return view('client.books.show', compact('book', 'comments', 'relatedBooks'));
    }

    /**
     * Gửi bình luận, đánh giá sách
     */
    public function storeComment(Request $request, $id)
    {
        $book = Sach::findOrFail($id);

        $request->validate([
            'noi_dung' => 'required|string|min:5|max:1000',
        ], [
            'noi_dung.required' => 'Vui lòng nhập nội dung đánh giá của bạn.',
            'noi_dung.min' => 'Nội dung đánh giá phải có ít nhất 5 ký tự.',
            'noi_dung.max' => 'Nội dung đánh giá không được vượt quá 1000 ký tự.',
        ]);

        $user = Auth::user();
        $khachHang = $user->khachHang;

        if (!$khachHang) {
            $khachHang = \App\Models\KhachHang::create([
                'Username' => $user->Username,
                'HoTen' => $user->Username,
            ]);
        }

        BinhLuan::create([
            'MaKH' => $khachHang->MaKH,
            'MaSach' => $book->MaSach,
            'NoiDung' => $request->noi_dung,
            'NgayBinhLuan' => now(),
            'TrangThai' => 'pending', // Chờ admin duyệt theo quy tắc bảo mật
        ]);

        return back()->with('success', 'Cảm ơn bạn đã gửi đánh giá! Bình luận sẽ hiển thị sau khi Ban Quản Trị kiểm duyệt.');
    }
}
