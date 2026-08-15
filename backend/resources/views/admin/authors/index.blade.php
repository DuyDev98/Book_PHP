@extends('layouts.admin')

@section('title', 'Quản Lý Tác Giả - Bookstore Admin')
@section('page_title', 'Quản Lý Tác Giả')

@section('content')
<div class="row g-4">
    <!-- Cột Trái: Thêm tác giả -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm rounded-4 p-4 bg-white sticky-top" style="top: 20px;">
            <h5 class="fw-bold text-primary mb-3">
                <i class="fa-solid fa-user-plus me-2 text-warning"></i>Thêm Tác Giả Mới
            </h5>

            <form action="{{ route('admin.authors.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label fw-semibold">Tên tác giả <span class="text-danger">*</span></label>
                    <input type="text" name="ten_tg" class="form-control @error('ten_tg') is-invalid @enderror" placeholder="Ví dụ: Nguyễn Nhật Ánh" value="{{ old('ten_tg') }}" required>
                    @error('ten_tg')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="form-label small fw-semibold">Năm sinh</label>
                        <input type="number" name="nam_sinh" class="form-control form-control-sm" placeholder="1955" value="{{ old('nam_sinh') }}">
                    </div>
                    <div class="col-6">
                        <label class="form-label small fw-semibold">Năm mất</label>
                        <input type="number" name="nam_mat" class="form-control form-control-sm" placeholder="Nếu có" value="{{ old('nam_mat') }}">
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-semibold">Quê quán / Quốc gia</label>
                    <input type="text" name="que_quan" class="form-control" placeholder="Ví dụ: Quảng Nam, Việt Nam" value="{{ old('que_quan') }}">
                </div>

                <div class="d-grid">
                    <button type="submit" class="btn btn-primary rounded-pill fw-bold">
                        <i class="fa-solid fa-plus me-1"></i>Lưu Tác Giả
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Cột Phải: Danh sách tác giả -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold text-dark mb-0">
                    <i class="fa-solid fa-feather-pointed me-2 text-primary"></i>Danh Sách Tác Giả
                </h5>

                <form action="{{ route('admin.authors.index') }}" method="GET" class="d-flex gap-2">
                    <input type="text" name="keyword" class="form-control form-control-sm" placeholder="Tìm tác giả..." value="{{ request('keyword') }}">
                    <button type="submit" class="btn btn-outline-primary btn-sm rounded-pill">Tìm</button>
                </form>
            </div>

            <div class="table-responsive">
                <table class="table align-middle table-hover">
                    <thead class="table-light text-muted small">
                        <tr>
                            <th>Mã</th>
                            <th>Họ và tên</th>
                            <th>Năm sinh - mất</th>
                            <th>Quê quán</th>
                            <th class="text-center">Số tác phẩm</th>
                            <th class="text-end">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($authors as $author)
                            <tr>
                                <td class="text-muted fw-semibold">#{{ $author->MaTG }}</td>
                                <td class="fw-bold text-dark">{{ $author->TenTG }}</td>
                                <td>
                                    {{ $author->NamSinh ?? '?' }} - {{ $author->NamMat ?? 'Nay' }}
                                </td>
                                <td>{{ $author->QueQuan ?? 'Chưa rõ' }}</td>
                                <td class="text-center">
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1">
                                        {{ $author->sachs_count }} cuốn
                                    </span>
                                </td>
                                <td class="text-end">
                                    <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#editAuthorModal{{ $author->MaTG }}" title="Sửa">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </button>

                                    <form action="{{ route('admin.authors.destroy', $author->MaTG) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc muốn xóa tác giả này?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger btn-sm" title="Xóa">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    </form>

                                    <!-- Modal Sửa Tác Giả -->
                                    <div class="modal fade" id="editAuthorModal{{ $author->MaTG }}" tabindex="-1">
                                        <div class="modal-dialog modal-dialog-centered text-start">
                                            <div class="modal-content">
                                                <form action="{{ route('admin.authors.update', $author->MaTG) }}" method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="modal-header">
                                                        <h6 class="modal-title fw-bold">Chỉnh Sửa Tác Giả: {{ $author->TenTG }}</h6>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="mb-3">
                                                            <label class="form-label small fw-semibold">Tên tác giả <span class="text-danger">*</span></label>
                                                            <input type="text" name="ten_tg" class="form-control" value="{{ $author->TenTG }}" required>
                                                        </div>
                                                        <div class="row g-2 mb-3">
                                                            <div class="col-6">
                                                                <label class="form-label small fw-semibold">Năm sinh</label>
                                                                <input type="number" name="nam_sinh" class="form-control" value="{{ $author->NamSinh }}">
                                                            </div>
                                                            <div class="col-6">
                                                                <label class="form-label small fw-semibold">Năm mất</label>
                                                                <input type="number" name="nam_mat" class="form-control" value="{{ $author->NamMat }}">
                                                            </div>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label small fw-semibold">Quê quán</label>
                                                            <input type="text" name="que_quan" class="form-control" value="{{ $author->QueQuan }}">
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
                {{ $authors->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>
@endsection
