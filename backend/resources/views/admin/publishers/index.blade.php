@extends('layouts.admin')

@section('title', 'Nhà Xuất Bản - Bookstore Admin')
@section('page_title', 'Quản Lý Nhà Xuất Bản')

@section('content')
<div class="row g-4">
    <!-- Cột Trái: Thêm NXB -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm rounded-4 p-4 bg-white sticky-top" style="top: 20px;">
            <h5 class="fw-bold text-primary mb-3">
                <i class="fa-solid fa-building-circle-check me-2 text-warning"></i>Thêm Nhà Xuất Bản
            </h5>

            <form action="{{ route('admin.publishers.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label fw-semibold">Tên nhà xuất bản <span class="text-danger">*</span></label>
                    <input type="text" name="ten_nxb" class="form-control @error('ten_nxb') is-invalid @enderror" placeholder="Ví dụ: NXB Kim Đồng" value="{{ old('ten_nxb') }}" required>
                    @error('ten_nxb')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Năm thành lập</label>
                    <input type="number" name="nam_thanh_lap" class="form-control" placeholder="1957" value="{{ old('nam_thanh_lap') }}">
                </div>

                <div class="mb-4">
                    <label class="form-label fw-semibold">Địa chỉ trụ sở</label>
                    <input type="text" name="dia_chi" class="form-control" placeholder="Hà Nội, TP.HCM..." value="{{ old('dia_chi') }}">
                </div>

                <div class="d-grid">
                    <button type="submit" class="btn btn-primary rounded-pill fw-bold">
                        <i class="fa-solid fa-plus me-1"></i>Lưu Nhà Xuất Bản
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Cột Phải: Danh sách NXB -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold text-dark mb-0">
                    <i class="fa-solid fa-building me-2 text-primary"></i>Danh Sách Nhà Xuất Bản
                </h5>

                <form action="{{ route('admin.publishers.index') }}" method="GET" class="d-flex gap-2">
                    <input type="text" name="keyword" class="form-control form-control-sm" placeholder="Tìm tên NXB..." value="{{ request('keyword') }}">
                    <button type="submit" class="btn btn-outline-primary btn-sm rounded-pill">Tìm</button>
                </form>
            </div>

            <div class="table-responsive">
                <table class="table align-middle table-hover">
                    <thead class="table-light text-muted small">
                        <tr>
                            <th>Mã</th>
                            <th>Tên NXB</th>
                            <th>Năm thành lập</th>
                            <th>Địa chỉ</th>
                            <th class="text-center">Số đầu sách</th>
                            <th class="text-end">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($publishers as $pub)
                            <tr>
                                <td class="text-muted fw-semibold">#{{ $pub->MaNXB }}</td>
                                <td class="fw-bold text-dark">{{ $pub->TenNXB }}</td>
                                <td>{{ $pub->NamThanhLap ?? 'N/A' }}</td>
                                <td>{{ $pub->DiaChi ?? 'Chưa rõ' }}</td>
                                <td class="text-center">
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1">
                                        {{ $pub->sachs_count }} cuốn
                                    </span>
                                </td>
                                <td class="text-end">
                                    <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#editPubModal{{ $pub->MaNXB }}" title="Sửa">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </button>

                                    <form action="{{ route('admin.publishers.destroy', $pub->MaNXB) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc muốn xóa nhà xuất bản này?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger btn-sm" title="Xóa">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    </form>

                                    <!-- Modal Sửa NXB -->
                                    <div class="modal fade" id="editPubModal{{ $pub->MaNXB }}" tabindex="-1">
                                        <div class="modal-dialog modal-dialog-centered text-start">
                                            <div class="modal-content">
                                                <form action="{{ route('admin.publishers.update', $pub->MaNXB) }}" method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="modal-header">
                                                        <h6 class="modal-title fw-bold">Chỉnh Sửa NXB: {{ $pub->TenNXB }}</h6>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="mb-3">
                                                            <label class="form-label small fw-semibold">Tên NXB <span class="text-danger">*</span></label>
                                                            <input type="text" name="ten_nxb" class="form-control" value="{{ $pub->TenNXB }}" required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label small fw-semibold">Năm thành lập</label>
                                                            <input type="number" name="nam_thanh_lap" class="form-control" value="{{ $pub->NamThanhLap }}">
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label small fw-semibold">Địa chỉ</label>
                                                            <input type="text" name="dia_chi" class="form-control" value="{{ $pub->DiaChi }}">
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
                {{ $publishers->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>
@endsection
