@extends('layouts.app')

@section('title', 'Hồ Sơ Cá Nhân - Bookstore')

@section('content')
<div class="container my-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none">Trang chủ</a></li>
            <li class="breadcrumb-item active">Hồ sơ cá nhân</li>
        </ol>
    </nav>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 p-4">
                <div class="d-flex align-items-center gap-3 border-bottom pb-3 mb-4">
                    <div class="bg-primary bg-opacity-10 text-primary p-3 rounded-circle fs-3">
                        <i class="fa-solid fa-user-pen"></i>
                    </div>
                    <div>
                        <h4 class="fw-bold text-dark mb-1">Cập Nhật Hồ Sơ Cá Nhân</h4>
                        <span class="text-muted small">Quản lý thông tin tài khoản và địa chỉ giao hàng</span>
                    </div>
                </div>

                <form action="{{ route('profile.update') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Tên đăng nhập (Username)</label>
                        <input type="text" class="form-control bg-light" value="{{ $user->Username }}" disabled readonly>
                        <small class="text-muted">Tên tài khoản không thể thay đổi</small>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Họ và tên <span class="text-danger">*</span></label>
                            <input type="text" name="ho_ten" class="form-control @error('ho_ten') is-invalid @enderror" value="{{ old('ho_ten', $khachHang->HoTen ?? '') }}" required>
                            @error('ho_ten')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Số điện thoại <span class="text-danger">*</span></label>
                            <input type="text" name="so_dien_thoai" class="form-control @error('so_dien_thoai') is-invalid @enderror" value="{{ old('so_dien_thoai', $khachHang->SDienThoai ?? '') }}" required>
                            @error('so_dien_thoai')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Địa chỉ Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $khachHang->Email ?? '') }}" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">Địa chỉ giao hàng mặc định <span class="text-danger">*</span></label>
                        <textarea name="dia_chi" class="form-control @error('dia_chi') is-invalid @enderror" rows="2" required>{{ old('dia_chi', $khachHang->DiaChi ?? '') }}</textarea>
                        @error('dia_chi')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="p-3 bg-light rounded-3 mb-4">
                        <h6 class="fw-bold text-dark mb-2">Đổi Mật Khẩu (để trống nếu không đổi)</h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">Mật khẩu mới</label>
                                <input type="password" name="new_password" class="form-control form-control-sm @error('new_password') is-invalid @enderror" placeholder="Tối thiểu 6 ký tự...">
                                @error('new_password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">Xác nhận mật khẩu mới</label>
                                <input type="password" name="new_password_confirmation" class="form-control form-control-sm" placeholder="Nhập lại mật khẩu mới...">
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('home') }}" class="btn btn-outline-secondary rounded-pill px-4">Hủy bỏ</a>
                        <button type="submit" class="btn btn-primary rounded-pill px-4 fw-semibold">
                            <i class="fa-solid fa-floppy-disk me-1"></i>Lưu Thay Đổi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
