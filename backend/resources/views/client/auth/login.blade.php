@extends('layouts.app')

@section('title', 'Đăng Nhập Tài Khoản - Bookstore')

@section('content')
<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card border-0 shadow rounded-4 p-4">
                <div class="text-center mb-4">
                    <div class="text-warning fs-1 mb-2">
                        <i class="fa-solid fa-book-open-reader"></i>
                    </div>
                    <h3 class="fw-bold text-primary">Đăng Nhập Hệ Thống</h3>
                    <p class="text-muted small">Chào mừng bạn trở lại với Nhà Sách Trực Tuyến</p>
                </div>

                @if($errors->has('login_error'))
                    <div class="alert alert-danger py-2 small mb-3">
                        <i class="fa-solid fa-triangle-exclamation me-1"></i>{{ $errors->first('login_error') }}
                    </div>
                @endif

                <form action="{{ route('login.submit') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Tên đăng nhập (Username) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="fa-solid fa-user text-muted"></i></span>
                            <input type="text" id="usernameInput" name="username" class="form-control @error('username') is-invalid @enderror" placeholder="Nhập username..." value="{{ old('username') }}" required autofocus>
                        </div>
                        @error('username')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Mật khẩu <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="fa-solid fa-lock text-muted"></i></span>
                            <input type="password" id="passwordInput" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="Nhập mật khẩu..." required>
                        </div>
                        @error('password')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="remember" id="rememberMe">
                            <label class="form-check-label small" for="rememberMe">Ghi nhớ đăng nhập</label>
                        </div>
                    </div>

                    <div class="d-grid mb-3">
                        <button type="submit" class="btn btn-warning btn-lg fw-bold rounded-pill shadow-sm">
                            <i class="fa-solid fa-arrow-right-to-bracket me-2"></i>Đăng Nhập
                        </button>
                    </div>

                    <!-- Nút đăng nhập mẫu 1-click phục vụ chấm bài / demo của giảng viên -->
                    <div class="p-3 bg-light rounded-3 mb-3 border">
                        <small class="fw-bold d-block text-secondary mb-2 text-center">
                            <i class="fa-solid fa-wand-magic-sparkles me-1 text-primary"></i>Tài khoản Demo nhanh (1-Click):
                        </small>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-outline-primary btn-sm flex-fill rounded-pill" onclick="fillAccount('admin', '123456')">
                                <i class="fa-solid fa-shield me-1"></i>Admin
                            </button>
                            <button type="button" class="btn btn-outline-success btn-sm flex-fill rounded-pill" onclick="fillAccount('khachhang1', '123456')">
                                <i class="fa-solid fa-user me-1"></i>Khách hàng
                            </button>
                        </div>
                    </div>

                    <div class="text-center small text-muted">
                        Chưa có tài khoản? <a href="{{ route('register') }}" class="text-primary fw-bold text-decoration-none">Đăng ký ngay</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function fillAccount(username, password) {
        document.getElementById('usernameInput').value = username;
        document.getElementById('passwordInput').value = password;
    }
</script>
@endsection
