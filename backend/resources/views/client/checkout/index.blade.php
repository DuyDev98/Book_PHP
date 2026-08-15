@extends('layouts.app')

@section('title', 'Thanh Toán Đơn Hàng - Bookstore')

@section('content')
<div class="container my-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none">Trang chủ</a></li>
            <li class="breadcrumb-item"><a href="{{ route('cart.index') }}" class="text-decoration-none">Giỏ hàng</a></li>
            <li class="breadcrumb-item active">Thanh toán</li>
        </ol>
    </nav>

    <h3 class="fw-bold text-primary mb-4">
        <i class="fa-solid fa-truck-ramp-box me-2 text-warning"></i>Xác Nhận Đặt Hàng & Thanh Toán
    </h3>

    <form action="{{ route('checkout.store') }}" method="POST">
        @csrf
        <div class="row g-4">
            <!-- Cột Trái: Thông tin giao hàng -->
            <div class="col-lg-7">
                <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
                    <h5 class="fw-bold text-dark mb-3">
                        <i class="fa-solid fa-location-dot me-2 text-danger"></i>1. Thông Tin Nhận Hàng
                    </h5>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Họ và tên người nhận <span class="text-danger">*</span></label>
                        <input type="text" name="ho_ten" class="form-control @error('ho_ten') is-invalid @enderror" value="{{ old('ho_ten', $khachHang->HoTen ?? '') }}" placeholder="Nhập đầy đủ họ tên..." required>
                        @error('ho_ten')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Số điện thoại liên hệ <span class="text-danger">*</span></label>
                            <input type="text" name="so_dien_thoai" class="form-control @error('so_dien_thoai') is-invalid @enderror" value="{{ old('so_dien_thoai', $khachHang->SDienThoai ?? '') }}" placeholder="Ví dụ: 0912345678" required>
                            @error('so_dien_thoai')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Địa chỉ Email</label>
                            <input type="email" class="form-control" value="{{ $khachHang->Email ?? '' }}" readonly disabled>
                            <small class="text-muted">Nhận thông báo đơn qua tài khoản</small>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Địa chỉ chi tiết nhận hàng <span class="text-danger">*</span></label>
                        <textarea name="dia_chi" class="form-control @error('dia_chi') is-invalid @enderror" rows="2" placeholder="Số nhà, tên đường, phường/xã, quận/huyện, tỉnh/thành phố..." required>{{ old('dia_chi', $khachHang->DiaChi ?? '') }}</textarea>
                        @error('dia_chi')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Ghi chú giao hàng (nếu có)</label>
                        <textarea name="ghi_chu" class="form-control @error('ghi_chu') is-invalid @enderror" rows="2" placeholder="Ví dụ: Giao giờ hành chính, gọi trước khi đến...">{{ old('ghi_chu') }}</textarea>
                        @error('ghi_chu')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Phương thức thanh toán -->
                <div class="card border-0 shadow-sm rounded-4 p-4">
                    <h5 class="fw-bold text-dark mb-3">
                        <i class="fa-solid fa-wallet me-2 text-success"></i>2. Phương Thức Thanh Toán
                    </h5>

                    <div class="form-check border p-3 rounded-3 mb-2 bg-light">
                        <input class="form-check-input" type="radio" name="payment_method" id="paymentCOD" value="COD" checked>
                        <label class="form-check-label fw-bold d-block" for="paymentCOD">
                            <i class="fa-solid fa-money-bill-wave text-success me-2"></i>Thanh toán khi nhận hàng (COD)
                        </label>
                        <small class="text-muted ms-4 d-block">Bạn chỉ phải thanh toán tiền mặt cho nhân viên giao hàng sau khi đã kiểm tra sách.</small>
                    </div>

                    <div class="form-check border p-3 rounded-3 opacity-75">
                        <input class="form-check-input" type="radio" disabled id="paymentBank">
                        <label class="form-check-label fw-bold d-block text-muted" for="paymentBank">
                            <i class="fa-solid fa-building-columns text-primary me-2"></i>Chuyển khoản Ngân hàng / VNPay / MoMo (Sắp ra mắt)
                        </label>
                    </div>
                </div>
            </div>

            <!-- Cột Phải: Tóm tắt đơn hàng -->
            <div class="col-lg-5">
                <div class="card border-0 shadow-sm rounded-4 p-4 sticky-top" style="top: 80px;">
                    <h5 class="fw-bold text-dark mb-3">
                        Đơn Hàng Của Bạn ({{ $items->count() }} sản phẩm)
                    </h5>

                    <div class="d-flex flex-column gap-3 mb-3" style="max-height: 280px; overflow-y: auto;">
                        @foreach($items as $item)
                            @if($item->sach)
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="d-flex align-items-center gap-2">
                                        <img src="{{ $item->sach->anh_bia_url }}" alt="{{ $item->sach->TenSach }}" class="rounded" style="width: 45px; height: 60px; object-fit: cover;">
                                        <div>
                                            <h6 class="mb-0 small fw-bold text-truncate" style="max-width: 180px;">{{ $item->sach->TenSach }}</h6>
                                            <small class="text-muted">SL: {{ $item->SoLuong }} x {{ number_format($item->sach->gia_hien_hanh, 0, ',', '.') }} đ</small>
                                        </div>
                                    </div>
                                    <span class="small fw-bold text-danger">
                                        {{ number_format($item->sach->gia_hien_hanh * $item->SoLuong, 0, ',', '.') }} đ
                                    </span>
                                </div>
                            @endif
                        @endforeach
                    </div>

                    <hr class="my-2">

                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Tạm tính:</span>
                        <span class="fw-semibold">{{ number_format($subtotal, 0, ',', '.') }} đ</span>
                    </div>

                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Phí vận chuyển:</span>
                        <span class="{{ $shippingFee == 0 ? 'text-success fw-bold' : 'fw-semibold' }}">
                            {{ $shippingFee == 0 ? 'Miễn phí' : number_format($shippingFee, 0, ',', '.') . ' đ' }}
                        </span>
                    </div>

                    <hr class="my-2">

                    <div class="d-flex justify-content-between align-items-baseline mb-4">
                        <span class="fw-bold fs-6">Tổng cộng:</span>
                        <span class="fw-bold fs-3 text-danger">{{ number_format($total, 0, ',', '.') }} đ</span>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-warning btn-lg rounded-pill fw-bold shadow-sm">
                            <i class="fa-solid fa-check-circle me-2"></i>Hoàn Tất Đặt Hàng
                        </button>
                        <a href="{{ route('cart.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill text-center">
                            Quay lại sửa giỏ hàng
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
