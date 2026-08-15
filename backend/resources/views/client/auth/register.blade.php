@extends('layouts.app')

@section('title', 'Đăng Ký Tài Khoản Mới - Bookstore')

@section('content')
<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-7 col-lg-6">
            <div class="card border-0 shadow rounded-4 p-4">
                <div class="text-center mb-4">
                    <div class="text-warning fs-1 mb-2">
                        <i class="fa-solid fa-user-plus"></i>
                    </div>
                    <h3 class="fw-bold text-primary">Tạo Tài Khoản Mới</h3>
                    <p class="text-muted small">Đăng ký để mua sách và theo dõi đơn hàng dễ dàng</p>
                </div>

                <form action="{{ route('register.submit') }}" method="POST">
                    @csrf

                    <div class="row g-3 mb-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold">Tên đăng nhập (Username) <span class="text-danger">*</span></label>
                            <input type="text" name="username" class="form-control @error('username') is-invalid @enderror" placeholder="Tối thiểu 4 ký tự viết liền..." value="{{ old('username') }}" required>
                            @error('username')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Mật khẩu <span class="text-danger">*</span></label>
                            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="Tối thiểu 6 ký tự..." required>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Xác nhận mật khẩu <span class="text-danger">*</span></label>
                            <input type="password" name="password_confirmation" class="form-control" placeholder="Nhập lại mật khẩu..." required>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold">Họ và tên <span class="text-danger">*</span></label>
                            <input type="text" name="ho_ten" class="form-control @error('ho_ten') is-invalid @enderror" placeholder="Ví dụ: Nguyễn Văn An" value="{{ old('ho_ten') }}" required>
                            @error('ho_ten')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Địa chỉ Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" placeholder="email@example.com" value="{{ old('email') }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Số điện thoại <span class="text-danger">*</span></label>
                            <input type="text" name="so_dien_thoai" class="form-control @error('so_dien_thoai') is-invalid @enderror" placeholder="0912345678" value="{{ old('so_dien_thoai') }}" required>
                            @error('so_dien_thoai')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Địa chỉ nhận hàng <span class="text-danger">*</span></label>
                            <textarea name="dia_chi" class="form-control @error('dia_chi') is-invalid @enderror" rows="2" placeholder="Số nhà, đường, quận/huyện, tỉnh/thành phố..." required>{{ old('dia_chi') }}</textarea>
                            @error('dia_chi')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="d-grid mb-3">
                        <button type="submit" class="btn btn-warning btn-lg fw-bold rounded-pill shadow-sm">
                            <i class="fa-solid fa-user-check me-2"></i>Đăng Ký Tài Khoản
                        </button>
                    </div>

                    <div class="text-center small text-muted">
                        Đã có tài khoản? <a href="{{ route('login') }}" class="text-primary fw-bold text-decoration-none">Đăng nhập</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
