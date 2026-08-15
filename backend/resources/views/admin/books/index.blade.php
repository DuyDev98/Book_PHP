@extends('layouts.admin')

@section('title', 'Quản Lý Sách - Bookstore Admin')
@section('page_title', 'Danh Sách Sách Trong Hệ Thống')

@section('content')
<div class="card border-0 shadow-sm rounded-4 p-4 bg-white mb-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <!-- Bộ lọc & Tìm kiếm -->
        <form action="{{ route('admin.books.index') }}" method="GET" class="d-flex flex-wrap align-items-center gap-2">
            <input type="text" name="keyword" class="form-control form-control-sm" placeholder="Tìm tên sách..." value="{{ request('keyword') }}" style="width: 220px;">

            <select name="category_id" class="form-select form-select-sm" style="width: 180px;">
                <option value="">-- Tất cả danh mục --</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->MaDanhMuc }}" {{ request('category_id') == $cat->MaDanhMuc ? 'selected' : '' }}>
                        {{ $cat->TenDanhMuc }}
                    </option>
                @endforeach
            </select>

            <div class="form-check form-check-inline small mb-0">
                <input class="form-check-input" type="checkbox" name="stock_alert" value="1" id="stockAlert" {{ request('stock_alert') == 1 ? 'checked' : '' }}>
                <label class="form-check-label text-danger fw-semibold" for="stockAlert">Chỉ sách sắp hết (<10)</label>
            </div>

            <button type="submit" class="btn btn-outline-primary btn-sm rounded-pill">
                <i class="fa-solid fa-magnifying-glass me-1"></i>Lọc
            </button>
            <a href="{{ route('admin.books.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill">Reset</a>
        </form>

        <a href="{{ route('admin.books.create') }}" class="btn btn-primary btn-sm rounded-pill px-3 shadow-sm">
            <i class="fa-solid fa-plus me-1"></i>Thêm Sách Mới
        </a>
    </div>

    @if($books->isEmpty())
        <div class="text-center py-5 text-muted">
            <i class="fa-solid fa-book-open fs-1 mb-2"></i>
            <p>Không có cuốn sách nào phù hợp với điều kiện tìm kiếm.</p>
        </div>
    @else
        <div class="table-responsive">
            <table class="table align-middle table-hover">
                <thead class="table-light text-muted small">
                    <tr>
                        <th style="width: 60px;">Mã</th>
                        <th>Hình ảnh</th>
                        <th>Tên sách</th>
                        <th>Danh mục / Tác giả</th>
                        <th>Giá bán</th>
                        <th class="text-center">Tồn kho</th>
                        <th class="text-end" style="width: 200px;">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($books as $b)
                        <tr>
                            <td class="text-muted fw-semibold">#{{ $b->MaSach }}</td>
                            <td>
                                <img src="{{ $b->anh_bia_url }}" alt="{{ $b->TenSach }}" class="rounded shadow-sm" style="width: 45px; height: 60px; object-fit: cover;">
                            </td>
                            <td>
                                <strong class="text-dark d-block" title="{{ $b->TenSach }}">{{ $b->TenSach }}</strong>
                                @if($b->IsSale && $b->PhanTramGiamGia > 0)
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle">Giảm {{ $b->PhanTramGiamGia }}%</span>
                                @endif
                            </td>
                            <td>
                                <div class="small fw-semibold text-primary">{{ $b->danhMuc->TenDanhMuc ?? 'Chưa phân loại' }}</div>
                                <small class="text-muted">{{ $b->tacGia->TenTG ?? 'Chưa rõ tác giả' }}</small>
                            </td>
                            <td>
                                <div class="fw-bold text-danger">{{ number_format($b->gia_hien_hanh, 0, ',', '.') }} đ</div>
                                @if($b->IsSale && $b->GiaSale)
                                    <del class="text-muted small">{{ number_format($b->GiaBan, 0, ',', '.') }} đ</del>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($b->SoLuongTon < 10)
                                    <span class="badge bg-danger px-2 py-1">{{ $b->SoLuongTon }} (Sắp hết)</span>
                                @else
                                    <span class="badge bg-success-subtle text-success px-2 py-1">{{ $b->SoLuongTon }}</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <div class="d-inline-flex gap-1">
                                    <button type="button" class="btn btn-outline-success btn-sm" title="Nhập thêm hàng" data-bs-toggle="modal" data-bs-target="#restockModal{{ $b->MaSach }}">
                                        <i class="fa-solid fa-boxes-packing"></i>
                                    </button>

                                    <a href="{{ route('admin.books.edit', $b->MaSach) }}" class="btn btn-outline-primary btn-sm" title="Sửa thông tin">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>

                                    <form action="{{ route('admin.books.destroy', $b->MaSach) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc muốn xóa cuốn sách này?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger btn-sm" title="Xóa sách">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    </form>
                                </div>

                                <!-- Modal Nhập kho -->
                                <div class="modal fade" id="restockModal{{ $b->MaSach }}" tabindex="-1">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content text-start">
                                            <form action="{{ route('admin.books.restock', $b->MaSach) }}" method="POST">
                                                @csrf
                                                <div class="modal-header">
                                                    <h6 class="modal-title fw-bold">Nhập Thêm Hàng Vào Kho</h6>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <p class="mb-1 text-dark fw-bold">{{ $b->TenSach }}</p>
                                                    <p class="small text-muted mb-3">Tồn kho hiện tại: <span class="badge bg-secondary">{{ $b->SoLuongTon }} cuốn</span></p>
                                                    <div class="mb-3">
                                                        <label class="form-label small fw-semibold">Số lượng sách nhập thêm:</label>
                                                        <input type="number" name="so_luong_them" class="form-control" value="10" min="1" required>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Đóng</button>
                                                    <button type="submit" class="btn btn-sm btn-primary">Xác Nhận Nhập Hàng</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-center mt-4">
            {{ $books->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>
@endsection
