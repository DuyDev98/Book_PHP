@extends('layouts.app')

@section('title', 'Lịch Sử Đơn Hàng Của Tôi - Bookstore')

@section('content')
<div class="container my-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none">Trang chủ</a></li>
            <li class="breadcrumb-item active">Đơn hàng của tôi</li>
        </ol>
    </nav>

    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <h3 class="fw-bold text-primary mb-0">
            <i class="fa-solid fa-box-archive me-2 text-warning"></i>Lịch Sử Đơn Hàng Của Tôi
        </h3>

        <!-- Bộ lọc trạng thái đơn -->
        <div class="d-flex align-items-center gap-2">
            <label class="small text-muted text-nowrap">Trạng thái:</label>
            <select class="form-select form-select-sm" style="width: 170px;" onchange="location = this.value;">
                <option value="{{ route('orders.index') }}" {{ !request('status') ? 'selected' : '' }}>Tất cả trạng thái</option>
                <option value="{{ route('orders.index', ['status' => 'ChoDuyet']) }}" {{ request('status') == 'ChoDuyet' ? 'selected' : '' }}>Chờ duyệt</option>
                <option value="{{ route('orders.index', ['status' => 'DangGiao']) }}" {{ request('status') == 'DangGiao' ? 'selected' : '' }}>Đang giao</option>
                <option value="{{ route('orders.index', ['status' => 'Đã giao']) }}" {{ request('status') == 'Đã giao' ? 'selected' : '' }}>Đã giao</option>
                <option value="{{ route('orders.index', ['status' => 'Đã hủy']) }}" {{ request('status') == 'Đã hủy' ? 'selected' : '' }}>Đã hủy</option>
            </select>
        </div>
    </div>

    @if($orders->isEmpty())
        <div class="card border-0 shadow-sm rounded-4 text-center py-5">
            <div class="py-5">
                <i class="fa-solid fa-receipt fs-1 text-muted mb-3"></i>
                <h5 class="fw-bold text-dark">Chưa có đơn hàng nào!</h5>
                <p class="text-muted small">Bạn chưa thực hiện bất kỳ giao dịch đặt mua sách nào.</p>
                <a href="{{ route('books.index') }}" class="btn btn-primary btn-sm rounded-pill px-4 mt-2">Dạo mua sách ngay</a>
            </div>
        </div>
    @else
        <div class="d-flex flex-column gap-3">
            @foreach($orders as $order)
                <div class="card border-0 shadow-sm rounded-4 p-4">
                    <div class="d-flex flex-wrap justify-content-between align-items-center border-bottom pb-3 mb-3 gap-2">
                        <div>
                            <span class="fw-bold fs-5 text-primary me-2">#DH-{{ $order->MaDH }}</span>
                            <span class="text-muted small">
                                <i class="fa-regular fa-clock me-1"></i>Ngày đặt: {{ \Carbon\Carbon::parse($order->NgayDat)->format('d/m/Y H:i') }}
                            </span>
                        </div>

                        <div>
                            @if(in_array($order->TrangThai, ['ChoDuyet']))
                                <span class="badge bg-warning text-dark px-3 py-2 rounded-pill"><i class="fa-solid fa-hourglass-half me-1"></i>Chờ duyệt</span>
                            @elseif(in_array($order->TrangThai, ['DangXuLy', 'DangGiao']))
                                <span class="badge bg-primary px-3 py-2 rounded-pill"><i class="fa-solid fa-truck me-1"></i>Đang giao hàng</span>
                            @elseif(in_array($order->TrangThai, ['Đã giao', 'DaGiao']))
                                <span class="badge bg-success px-3 py-2 rounded-pill"><i class="fa-solid fa-check me-1"></i>Giao thành công</span>
                            @elseif(in_array($order->TrangThai, ['Đã hủy', 'DaHuy']))
                                <span class="badge bg-danger px-3 py-2 rounded-pill"><i class="fa-solid fa-xmark me-1"></i>Đã hủy</span>
                            @else
                                <span class="badge bg-secondary px-3 py-2 rounded-pill">{{ $order->TrangThai }}</span>
                            @endif
                        </div>
                    </div>

                    <!-- Danh sách món tóm tắt -->
                    <div class="mb-3">
                        @foreach($order->chiTiets as $ct)
                            <div class="d-flex justify-content-between align-items-center py-1">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="fa-solid fa-book text-muted"></i>
                                    <span class="text-dark">{{ $ct->sach->TenSach ?? 'Sách đã xóa' }}</span>
                                    <span class="text-muted small">x {{ $ct->SoLuong }}</span>
                                </div>
                                <span class="fw-semibold text-secondary">{{ number_format($ct->DonGia * $ct->SoLuong, 0, ',', '.') }} đ</span>
                            </div>
                        @endforeach
                    </div>

                    <div class="d-flex flex-wrap justify-content-between align-items-center pt-3 border-top gap-3">
                        <div>
                            <span class="text-muted small d-block">Địa chỉ: {{ $order->DiaChiGiaoHang }}</span>
                            <span class="text-muted small">Tổng thanh toán: </span>
                            <span class="fw-bold fs-5 text-danger">{{ number_format($order->TongTien, 0, ',', '.') }} đ</span>
                        </div>

                        <div class="d-flex gap-2">
                            <a href="{{ route('orders.show', $order->MaDH) }}" class="btn btn-outline-primary btn-sm rounded-pill px-3">
                                <i class="fa-solid fa-eye me-1"></i>Chi tiết đơn
                            </a>

                            @if($order->TrangThai === 'ChoDuyet')
                                <form action="{{ route('orders.cancel', $order->MaDH) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn hủy đơn hàng này? Sách sẽ được hoàn lại kho.');">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill px-3">
                                        <i class="fa-solid fa-ban me-1"></i>Hủy đơn hàng
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="d-flex justify-content-center mt-4">
            {{ $orders->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>
@endsection
