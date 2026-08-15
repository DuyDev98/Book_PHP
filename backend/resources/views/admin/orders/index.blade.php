@extends('layouts.admin')

@section('title', 'Quản Lý Đơn Hàng - Bookstore Admin')
@section('page_title', 'Quản Lý Đơn Hàng & Vận Chuyển')

@section('content')
<div class="card border-0 shadow-sm rounded-4 p-4 bg-white mb-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <!-- Bộ lọc trạng thái & tìm kiếm -->
        <form action="{{ route('admin.orders.index') }}" method="GET" class="d-flex flex-wrap align-items-center gap-2">
            <input type="text" name="keyword" class="form-control form-control-sm" placeholder="Mã đơn, tên, SĐT khách..." value="{{ request('keyword') }}" style="width: 240px;">

            <select name="status" class="form-select form-select-sm" style="width: 180px;">
                <option value="">-- Tất cả trạng thái --</option>
                <option value="ChoDuyet" {{ request('status') == 'ChoDuyet' ? 'selected' : '' }}>Chờ duyệt</option>
                <option value="DangXuLy" {{ request('status') == 'DangXuLy' ? 'selected' : '' }}>Đang xử lý</option>
                <option value="DangGiao" {{ request('status') == 'DangGiao' ? 'selected' : '' }}>Đang giao hàng</option>
                <option value="Đã giao" {{ request('status') == 'Đã giao' ? 'selected' : '' }}>Đã giao thành công</option>
                <option value="Đã hủy" {{ request('status') == 'Đã hủy' ? 'selected' : '' }}>Đã hủy (Đã hoàn kho)</option>
            </select>

            <button type="submit" class="btn btn-outline-primary btn-sm rounded-pill">
                <i class="fa-solid fa-filter me-1"></i>Lọc Đơn
            </button>
            <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill">Reset</a>
        </form>
    </div>

    @if($orders->isEmpty())
        <div class="text-center py-5 text-muted">
            <i class="fa-solid fa-box-open fs-1 mb-2"></i>
            <p>Không tìm thấy đơn hàng nào.</p>
        </div>
    @else
        <div class="table-responsive">
            <table class="table align-middle table-hover">
                <thead class="table-light text-muted small">
                    <tr>
                        <th>Mã đơn</th>
                        <th>Khách hàng</th>
                        <th>Ngày đặt</th>
                        <th>Tổng thanh toán</th>
                        <th>Trạng thái hiện tại</th>
                        <th>Cập nhật trạng thái nhanh</th>
                        <th class="text-end">Chi tiết</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($orders as $o)
                        <tr>
                            <td class="fw-bold text-primary">#DH-{{ $o->MaDH }}</td>
                            <td>
                                <strong class="text-dark d-block">{{ $o->khachHang->HoTen ?? 'Khách vãng lai' }}</strong>
                                <small class="text-muted"><i class="fa-solid fa-phone me-1"></i>{{ $o->khachHang->SDienThoai ?? 'Chưa có' }}</small>
                            </td>
                            <td>{{ \Carbon\Carbon::parse($o->NgayDat)->format('d/m/Y H:i') }}</td>
                            <td class="fw-bold text-danger">{{ number_format($o->TongTien, 0, ',', '.') }} đ</td>
                            <td>
                                @if(in_array($o->TrangThai, ['ChoDuyet']))
                                    <span class="badge bg-warning text-dark"><i class="fa-solid fa-hourglass-half me-1"></i>Chờ duyệt</span>
                                @elseif(in_array($o->TrangThai, ['DangXuLy', 'DangGiao']))
                                    <span class="badge bg-primary"><i class="fa-solid fa-truck me-1"></i>Đang giao</span>
                                @elseif(in_array($o->TrangThai, ['Đã giao', 'DaGiao']))
                                    <span class="badge bg-success"><i class="fa-solid fa-check me-1"></i>Đã giao</span>
                                @elseif(in_array($o->TrangThai, ['Đã hủy', 'DaHuy']))
                                    <span class="badge bg-danger"><i class="fa-solid fa-xmark me-1"></i>Đã hủy</span>
                                @else
                                    <span class="badge bg-secondary">{{ $o->TrangThai }}</span>
                                @endif
                            </td>
                            <td>
                                <form action="{{ route('admin.orders.status', $o->MaDH) }}" method="POST" class="d-flex align-items-center gap-1">
                                    @csrf
                                    <select name="trang_thai" class="form-select form-select-sm" style="width: 140px;">
                                        <option value="ChoDuyet" {{ in_array($o->TrangThai, ['ChoDuyet']) ? 'selected' : '' }}>Chờ duyệt</option>
                                        <option value="DangXuLy" {{ in_array($o->TrangThai, ['DangXuLy']) ? 'selected' : '' }}>Đang xử lý</option>
                                        <option value="DangGiao" {{ in_array($o->TrangThai, ['DangGiao']) ? 'selected' : '' }}>Đang giao</option>
                                        <option value="Đã giao" {{ in_array($o->TrangThai, ['Đã giao', 'DaGiao']) ? 'selected' : '' }}>Đã giao</option>
                                        <option value="Đã hủy" {{ in_array($o->TrangThai, ['Đã hủy', 'DaHuy']) ? 'selected' : '' }}>Đã hủy</option>
                                    </select>
                                    <button type="submit" class="btn btn-outline-success btn-sm py-1 px-2" title="Cập nhật">
                                        <i class="fa-solid fa-check"></i>
                                    </button>
                                </form>
                            </td>
                            <td class="text-end">
                                <a href="{{ route('admin.orders.show', $o->MaDH) }}" class="btn btn-outline-primary btn-sm rounded-pill">
                                    <i class="fa-solid fa-eye me-1"></i>Xem
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-center mt-4">
            {{ $orders->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>
@endsection
