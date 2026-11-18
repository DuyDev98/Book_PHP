<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Sach;
use App\Services\CloudinaryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BookController extends Controller
{
    protected $cloudinaryService;

    public function __construct(CloudinaryService $cloudinaryService)
    {
        $this->cloudinaryService = $cloudinaryService;
    }

    /**
     * Danh sách sách kèm tìm kiếm & bộ lọc đa dạng
     */
    public function index(Request $request)
    {
        $query = Sach::with(['tacGia', 'nhaXuatBan', 'loaiSach', 'danhMuc']);

        // Tìm kiếm theo từ khóa
        if ($request->filled('q')) {
            $keyword = $request->q;
            $query->where(function ($q) use ($keyword) {
                $q->where('TenSach', 'like', "%{$keyword}%")
                  ->orWhereHas('tacGia', function ($qTG) use ($keyword) {
                      $qTG->where('TenTG', 'like', "%{$keyword}%");
                  });
            });
        }

        // Lọc theo Danh mục
        if ($request->filled('category_id')) {
            $catId = $request->category_id;
            $query->where(function ($q) use ($catId) {
                $q->where('MaDanhMuc', $catId)
                  ->orWhereHas('danhMuc', function ($subQ) use ($catId) {
                      $subQ->where('ParentID', $catId);
                  });
            });
        }

        // Lọc theo Tác giả
        if ($request->filled('author_id')) {
            $query->where('MaTG', $request->author_id);
        }

        // Lọc theo Nhà xuất bản
        if ($request->filled('publisher_id')) {
            $query->where('MaNXB', $request->publisher_id);
        }

        // Lọc theo Loại sách
        if ($request->filled('type_id')) {
            $query->where('MaLoaiSach', $request->type_id);
        }

        // Lọc sách đang giảm giá (Flash sale / IsSale)
        if ($request->boolean('is_sale')) {
            $query->where('IsSale', true);
        }

        // Lọc theo khoảng giá
        if ($request->filled('min_price')) {
            $query->where('GiaBan', '>=', $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('GiaBan', '<=', $request->max_price);
        }

        // Sắp xếp
        $sortBy = $request->get('sort', 'newest');
        switch ($sortBy) {
            case 'price_asc':
                $query->orderBy('GiaBan', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('GiaBan', 'desc');
                break;
            case 'discount':
                $query->orderBy('PhanTramGiamGia', 'desc');
                break;
            case 'newest':
            default:
                $query->orderBy('MaSach', 'desc');
                break;
        }

        $perPage = $request->get('per_page', 12);
        $books = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $books->items(),
            'pagination' => [
                'current_page' => $books->currentPage(),
                'last_page' => $books->lastPage(),
                'per_page' => $books->perPage(),
                'total' => $books->total(),
            ],
        ]);
    }

    /**
     * Chi tiết cuốn sách
     */
    public function show($id)
    {
        $book = Sach::with([
            'tacGia',
            'nhaXuatBan',
            'loaiSach',
            'danhMuc',
            'binhLuans' => function ($q) {
                $q->where('TrangThai', 'approved')
                  ->with('khachHang')
                  ->orderBy('NgayBinhLuan', 'desc');
            }
        ])->find($id);

        if (!$book) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy sách yêu cầu.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $book,
        ]);
    }

    /**
     * Thêm sách mới (Admin)
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'TenSach' => 'required|string|max:200',
            'GiaBan' => 'required|numeric|min:0',
            'SoLuongTon' => 'required|integer|min:0',
            'PhanTramGiamGia' => 'nullable|integer|min:0|max:100',
            'AnhBia' => 'nullable|image|max:5120', // Giới hạn 5MB theo tài liệu
            'MaTG' => 'nullable|exists:tacgia,MaTG',
            'MaNXB' => 'nullable|exists:nhaxuatban,MaNXB',
            'MaLoaiSach' => 'nullable|exists:loaisach,MaLoaiSach',
            'MaDanhMuc' => 'nullable|exists:danhmuc,MaDanhMuc',
            'NamXuatBan' => 'nullable|integer',
            'LanTaiBan' => 'nullable|integer',
            'MoTa' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $request->except(['AnhBia']);

        // Xử lý upload ảnh bìa (Cloudinary hoặc Local fallback)
        if ($request->hasFile('AnhBia')) {
            $data['AnhBia'] = $this->cloudinaryService->uploadImage($request->file('AnhBia'));
        }

        $book = new Sach($data);
        $book->tinhGiaSale();
        $book->save();

        return response()->json([
            'success' => true,
            'message' => 'Thêm sách mới thành công!',
            'data' => $book->load(['tacGia', 'nhaXuatBan', 'loaiSach', 'danhMuc']),
        ], 201);
    }

    /**
     * Cập nhật thông tin sách (Admin)
     */
    public function update(Request $request, $id)
    {
        $book = Sach::find($id);
        if (!$book) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy sách cần cập nhật.',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'TenSach' => 'sometimes|required|string|max:200',
            'GiaBan' => 'sometimes|required|numeric|min:0',
            'SoLuongTon' => 'sometimes|required|integer|min:0',
            'PhanTramGiamGia' => 'nullable|integer|min:0|max:100',
            'AnhBia' => 'nullable|image|max:5120',
            'MaTG' => 'nullable|exists:tacgia,MaTG',
            'MaNXB' => 'nullable|exists:nhaxuatban,MaNXB',
            'MaLoaiSach' => 'nullable|exists:loaisach,MaLoaiSach',
            'MaDanhMuc' => 'nullable|exists:danhmuc,MaDanhMuc',
            'NamXuatBan' => 'nullable|integer',
            'LanTaiBan' => 'nullable|integer',
            'MoTa' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $request->except(['AnhBia']);

        if ($request->hasFile('AnhBia')) {
            $data['AnhBia'] = $this->cloudinaryService->uploadImage($request->file('AnhBia'));
        }

        $book->fill($data);
        $book->tinhGiaSale();
        $book->save();

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật sách thành công!',
            'data' => $book->fresh(['tacGia', 'nhaXuatBan', 'loaiSach', 'danhMuc']),
        ]);
    }

    /**
     * Xóa sách (Admin)
     */
    public function destroy($id)
    {
        $book = Sach::find($id);
        if (!$book) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy sách cần xóa.',
            ], 404);
        }

        $book->delete();

        return response()->json([
            'success' => true,
            'message' => 'Đã xóa cuốn sách khỏi hệ thống.',
        ]);
    }

    /**
     * Nhập hàng nhanh vào kho (Admin)
     */
    public function nhapHang(Request $request, $id)
    {
        $request->validate([
            'soLuong' => 'required|integer|min:1',
        ]);

        $book = Sach::find($id);
        if (!$book) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy sách.',
            ], 404);
        }

        $book->increment('SoLuongTon', $request->soLuong);

        return response()->json([
            'success' => true,
            'message' => "Đã nhập thêm {$request->soLuong} cuốn sách vào kho!",
            'data' => $book->fresh(),
        ]);
    }
}
