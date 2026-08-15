@extends('layouts.admin')

@section('title', 'Kiểm Duyệt Bình Luận - Bookstore Admin')
@section('page_title', 'Kiểm Duyệt Đánh Giá & Bình Luận')

@section('content')
<div class="card border-0 shadow-sm rounded-4 p-4 bg-white mb-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <h5 class="fw-bold text-dark mb-0">
            <i class="fa-solid fa-comments text-primary me-2"></i>Danh Sách Bình Luận Độc Giả
        </h5>

        <!-- Lọc theo trạng thái -->
        <div class="d-flex align-items-center gap-2">
            <label class="small text-muted text-nowrap fw-semibold">Trạng thái:</label>
            <select class="form-select form-select-sm" style="width: 170px;" onchange="location = this.value;">
                <option value="{{ route('admin.comments.index') }}" {{ !request('status') ? 'selected' : '' }}>Tất cả</option>
                <option value="{{ route('admin.comments.index', ['status' => 'pending']) }}" {{ request('status') == 'pending' ? 'selected' : '' }}>Chờ duyệt (Pending)</option>
                <option value="{{ route('admin.comments.index', ['status' => 'approved']) }}" {{ request('status') == 'approved' ? 'selected' : '' }}>Đã duyệt (Approved)</option>
                <option value="{{ route('admin.comments.index', ['status' => 'rejected']) }}" {{ request('status') == 'rejected' ? 'selected' : '' }}>Đã từ chối (Rejected)</option>
            </select>
        </div>
    </div>

    @if($comments->isEmpty())
        <div class="text-center py-5 text-muted">
            <i class="fa-regular fa-comments fs-1 mb-2"></i>
            <p>Không có bình luận nào cần xử lý.</p>
        </div>
    @else
        <div class="table-responsive">
            <table class="table align-middle table-hover">
                <thead class="table-light text-muted small">
                    <tr>
                        <th style="width: 60px;">Mã</th>
                        <th>Độc giả</th>
                        <th>Sách đánh giá</th>
                        <th style="max-width: 350px;">Nội dung bình luận</th>
                        <th>Thời gian</th>
                        <th class="text-center">Trạng thái</th>
                        <th class="text-end" style="width: 180px;">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($comments as $c)
                        <tr>
                            <td class="text-muted fw-semibold">#{{ $c->MaBL }}</td>
                            <td>
                                <strong class="text-dark d-block">{{ $c->khachHang->HoTen ?? 'Bạn đọc' }}</strong>
                                <small class="text-muted">{{ $c->khachHang->Username ?? '' }}</small>
                            </td>
                            <td>
                                @if($c->sach)
                                    <a href="{{ route('books.show', $c->sach->MaSach) }}" target="_blank" class="fw-semibold text-primary text-decoration-none" title="{{ $c->sach->TenSach }}">
                                        {{ Str::limit($c->sach->TenSach, 30) }}
                                    </a>
                                @else
                                    <span class="text-muted">Sách đã xóa</span>
                                @endif
                            </td>
                            <td style="max-width: 350px;">
                                <div class="text-secondary small">{{ $c->NoiDung }}</div>
                            </td>
                            <td>
                                <small class="text-muted">{{ \Carbon\Carbon::parse($c->NgayBinhLuan)->format('d/m/Y H:i') }}</small>
                            </td>
                            <td class="text-center">
                                @if($c->TrangThai === 'pending')
                                    <span class="badge bg-warning text-dark"><i class="fa-solid fa-clock me-1"></i>Chờ duyệt</span>
                                @elseif($c->TrangThai === 'approved')
                                    <span class="badge bg-success"><i class="fa-solid fa-check me-1"></i>Đã duyệt</span>
                                @elseif($c->TrangThai === 'rejected')
                                    <span class="badge bg-danger"><i class="fa-solid fa-xmark me-1"></i>Từ chối</span>
                                @else
                                    <span class="badge bg-secondary">{{ $c->TrangThai }}</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <div class="d-inline-flex gap-1">
                                    @if($c->TrangThai !== 'approved')
                                        <form action="{{ route('admin.comments.approve', $c->MaBL) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-outline-success btn-sm py-1 px-2" title="Duyệt cho phép hiển thị">
                                                <i class="fa-solid fa-check"></i>
                                            </button>
                                        </form>
                                    @endif

                                    @if($c->TrangThai !== 'rejected')
                                        <form action="{{ route('admin.comments.reject', $c->MaBL) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-outline-warning btn-sm py-1 px-2" title="Từ chối / Ẩn bình luận">
                                                <i class="fa-solid fa-eye-slash"></i>
                                            </button>
                                        </form>
                                    @endif

                                    <form action="{{ route('admin.comments.destroy', $c->MaBL) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc muốn xóa vĩnh viễn bình luận này?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger btn-sm py-1 px-2" title="Xóa">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-center mt-4">
            {{ $comments->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>
@endsection
