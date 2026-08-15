@extends('layouts.admin')

@section('title', 'Quản Lý Danh Mục - Bookstore Admin')
@section('page_title', 'Quản Lý Danh Mục Sách')

@section('content')
<div class="row g-4">
    <!-- Cột Trái: Thêm danh mục mới -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm rounded-4 p-4 bg-white sticky-top" style="top: 20px;">
            <h5 class="fw-bold text-primary mb-3">
                <i class="fa-solid fa-folder-plus me-2 text-warning"></i>Thêm Danh Mục Mới
            </h5>

            <form action="{{ route('admin.categories.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label fw-semibold">Tên danh mục <span class="text-danger">*</span></label>
                    <input type="text" name="ten_danh_muc" class="form-control @error('ten_danh_muc') is-invalid @enderror" placeholder="Ví dụ: Khoa Học Viễn Tưởng" value="{{ old('ten_danh_muc') }}" required>
                    @error('ten_danh_muc')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <label class="form-label fw-semibold">Danh mục cha (nếu có)</label>
                    <select name="parent_id" class="form-select">
                        <option value="">-- Là danh mục gốc (Cấp 1) --</option>
                        @foreach($parentCategories as $parent)
                            <option value="{{ $parent->MaDanhMuc }}" {{ old('parent_id') == $parent->MaDanhMuc ? 'selected' : '' }}>
                                {{ $parent->TenDanhMuc }}
                            </option>
                        @endforeach
                    </select>
                    <small class="text-muted">Hỗ trợ phân cấp cây danh mục đa tầng</small>
                </div>

                <div class="d-grid">
                    <button type="submit" class="btn btn-primary rounded-pill fw-bold">
                        <i class="fa-solid fa-plus me-1"></i>Tạo Danh Mục
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Cột Phải: Bảng danh mục hiện có -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
            <h5 class="fw-bold text-dark mb-3">
                <i class="fa-solid fa-list-check me-2 text-primary"></i>Danh Sách Danh Mục
            </h5>

            <div class="table-responsive">
                <table class="table align-middle table-hover">
                    <thead class="table-light text-muted small">
                        <tr>
                            <th style="width: 60px;">Mã</th>
                            <th>Tên danh mục</th>
                            <th>Danh mục cha</th>
                            <th class="text-center">Số đầu sách</th>
                            <th class="text-end" style="width: 140px;">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($categories as $cat)
                            <tr>
                                <td class="text-muted fw-semibold">#{{ $cat->MaDanhMuc }}</td>
                                <td class="fw-bold text-dark">{{ $cat->TenDanhMuc }}</td>
                                <td>
                                    @if($cat->parent)
                                        <span class="badge bg-light text-primary border">{{ $cat->parent->TenDanhMuc }}</span>
                                    @else
                                        <span class="badge bg-secondary-subtle text-secondary">Danh mục gốc</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1">
                                        {{ $cat->sachs_count }} cuốn
                                    </span>
                                </td>
                                <td class="text-end">
                                    <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#editModal{{ $cat->MaDanhMuc }}" title="Sửa">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </button>

                                    <form action="{{ route('admin.categories.destroy', $cat->MaDanhMuc) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc muốn xóa danh mục này?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger btn-sm" title="Xóa">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    </form>

                                    <!-- Modal Sửa Danh Mục -->
                                    <div class="modal fade" id="editModal{{ $cat->MaDanhMuc }}" tabindex="-1">
                                        <div class="modal-dialog modal-dialog-centered text-start">
                                            <div class="modal-content">
                                                <form action="{{ route('admin.categories.update', $cat->MaDanhMuc) }}" method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="modal-header">
                                                        <h6 class="modal-title fw-bold">Chỉnh Sửa Danh Mục: {{ $cat->TenDanhMuc }}</h6>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="mb-3">
                                                            <label class="form-label small fw-semibold">Tên danh mục <span class="text-danger">*</span></label>
                                                            <input type="text" name="ten_danh_muc" class="form-control" value="{{ $cat->TenDanhMuc }}" required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label small fw-semibold">Danh mục cha</label>
                                                            <select name="parent_id" class="form-select form-select-sm">
                                                                <option value="">-- Là danh mục gốc (Cấp 1) --</option>
                                                                @foreach($parentCategories as $parent)
                                                                    @if($parent->MaDanhMuc != $cat->MaDanhMuc)
                                                                        <option value="{{ $parent->MaDanhMuc }}" {{ $cat->ParentID == $parent->MaDanhMuc ? 'selected' : '' }}>
                                                                            {{ $parent->TenDanhMuc }}
                                                                        </option>
                                                                    @endif
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Đóng</button>
                                                        <button type="submit" class="btn btn-sm btn-primary">Lưu Thay Đổi</button>
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

            <div class="d-flex justify-content-center mt-3">
                {{ $categories->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>
@endsection
