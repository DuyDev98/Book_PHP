<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Nhà Sách Trực Tuyến - Bookstore')</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome 6 -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <!-- Custom Style -->
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
    <style>
        .badge-cart {
            font-size: 0.75rem;
            position: absolute;
            top: -5px;
            right: -8px;
        }
    </style>
    @stack('styles')
</head>
<body>

    <!-- Header & Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom sticky-top">
        <div class="container">
            <a class="navbar-brand brand-logo" href="{{ route('home') }}">
                <i class="fa-solid fa-book-open-reader me-2"></i>BOOKSTORE
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarContent">
                <!-- Search Bar -->
                <form class="d-flex mx-auto my-2 my-lg-0 w-50" action="{{ route('books.index') }}" method="GET">
                    <div class="input-group">
                        <input type="text" class="form-control" name="keyword" placeholder="Tìm tên sách, tác giả..." value="{{ request('keyword') }}">
                        <button class="btn btn-warning px-3" type="submit">
                            <i class="fa-solid fa-magnifying-glass"></i>
                        </button>
                    </div>
                </form>

                <!-- Navigation Links -->
                <ul class="navbar-nav ms-auto align-items-center mb-2 mb-lg-0 gap-2">
                    <li class="nav-item">
                        <a class="nav-link text-white {{ request()->routeIs('home') ? 'active fw-bold' : '' }}" href="{{ route('home') }}">
                            <i class="fa-solid fa-house me-1"></i>Trang chủ
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white {{ request()->routeIs('books.index') ? 'active fw-bold' : '' }}" href="{{ route('books.index') }}">
                            <i class="fa-solid fa-book me-1"></i>Tất cả sách
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-warning fw-semibold" href="{{ route('books.index', ['is_sale' => 1]) }}">
                            <i class="fa-solid fa-bolt me-1"></i>Khuyến mãi
                        </a>
                    </li>

                    <!-- Cart -->
                    @php
                        $cartCount = 0;
                        if (auth()->check() && auth()->user()->khachHang) {
                            $cart = \App\Models\GioHang::where('MaKH', auth()->user()->khachHang->MaKH)->first();
                            if ($cart) {
                                $cartCount = \App\Models\ChiTietGioHang::where('MaGioHang', $cart->MaGioHang)->sum('SoLuong');
                            }
                        }
                    @endphp
                    <li class="nav-item">
                        <a class="nav-link text-white position-relative px-2" href="{{ route('cart.index') }}">
                            <i class="fa-solid fa-cart-shopping fs-5"></i>
                            @if($cartCount > 0)
                                <span class="badge rounded-pill bg-danger badge-cart">{{ $cartCount }}</span>
                            @endif
                        </a>
                    </li>

                    <!-- User Authentication -->
                    @guest
                        <li class="nav-item ms-lg-2">
                            <a href="{{ route('login') }}" class="btn btn-outline-light btn-sm px-3 rounded-pill">
                                <i class="fa-solid fa-arrow-right-to-bracket me-1"></i>Đăng nhập
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('register') }}" class="btn btn-warning btn-sm px-3 rounded-pill fw-semibold">
                                Đăng ký
                            </a>
                        </li>
                    @else
                        <li class="nav-item dropdown ms-lg-2">
                            <a class="nav-link dropdown-toggle text-white d-flex align-items-center gap-2" href="#" role="button" data-bs-toggle="dropdown">
                                <i class="fa-solid fa-circle-user fs-4 text-warning"></i>
                                <span>{{ Auth::user()->Username }}</span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                                @if(Auth::user()->isAdmin())
                                    <li>
                                        <a class="dropdown-item text-primary fw-semibold" href="{{ route('admin.dashboard') }}">
                                            <i class="fa-solid fa-gauge-high me-2"></i>Trang Quản Trị
                                        </a>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                @endif
                                <li>
                                    <a class="dropdown-item" href="{{ route('orders.index') }}">
                                        <i class="fa-solid fa-box-open me-2 text-muted"></i>Đơn hàng của tôi
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('profile.index') }}">
                                        <i class="fa-solid fa-id-card me-2 text-muted"></i>Hồ sơ cá nhân
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form action="{{ route('logout') }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-danger">
                                            <i class="fa-solid fa-right-from-bracket me-2"></i>Đăng xuất
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @endguest
                </ul>
            </div>
        </div>
    </nav>

    <!-- Global Flash Messages -->
    <div class="container mt-3">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                <i class="fa-solid fa-circle-check me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                <i class="fa-solid fa-circle-exclamation me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('info'))
            <div class="alert alert-info alert-dismissible fade show shadow-sm" role="alert">
                <i class="fa-solid fa-circle-info me-2"></i>{{ session('info') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                <div class="fw-bold mb-1"><i class="fa-solid fa-triangle-exclamation me-2"></i>Vui lòng kiểm tra lại các lỗi sau:</div>
                <ul class="mb-0 ps-3">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
    </div>

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-dark text-white pt-5 pb-3 mt-5">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-4 col-md-6">
                    <h5 class="fw-bold text-warning mb-3">
                        <i class="fa-solid fa-book-open-reader me-2"></i>BOOKSTORE
                    </h5>
                    <p class="text-white-50">
                        Hệ thống bán sách trực tuyến hiện đại với hàng ngàn đầu sách phong phú, phục vụ nhu cầu tra cứu, học tập và giải trí của bạn đọc toàn quốc.
                    </p>
                    <div class="d-flex gap-3 fs-5">
                        <a href="#" class="text-white-50 hover-text-warning"><i class="fa-brands fa-facebook"></i></a>
                        <a href="#" class="text-white-50 hover-text-warning"><i class="fa-brands fa-youtube"></i></a>
                        <a href="#" class="text-white-50 hover-text-warning"><i class="fa-brands fa-github"></i></a>
                    </div>
                </div>

                <div class="col-lg-2 col-md-6">
                    <h6 class="fw-bold text-white mb-3">Liên kết nhanh</h6>
                    <ul class="list-unstyled text-white-50 small">
                        <li class="mb-2"><a href="{{ route('home') }}" class="text-white-50 text-decoration-none">Trang chủ</a></li>
                        <li class="mb-2"><a href="{{ route('books.index') }}" class="text-white-50 text-decoration-none">Kho sách</a></li>
                        <li class="mb-2"><a href="{{ route('books.index', ['is_sale' => 1]) }}" class="text-white-50 text-decoration-none">Sách giảm giá</a></li>
                        <li class="mb-2"><a href="{{ route('cart.index') }}" class="text-white-50 text-decoration-none">Giỏ hàng</a></li>
                    </ul>
                </div>

                <div class="col-lg-3 col-md-6">
                    <h6 class="fw-bold text-white mb-3">Chính sách & Hỗ trợ</h6>
                    <ul class="list-unstyled text-white-50 small">
                        <li class="mb-2">Giao hàng COD toàn quốc</li>
                        <li class="mb-2">Đổi trả sách trong 7 ngày</li>
                        <li class="mb-2">Hỗ trợ 24/7 qua Hotline</li>
                        <li class="mb-2">Bảo mật thông tin khách hàng</li>
                    </ul>
                </div>

                <div class="col-lg-3 col-md-6">
                    <h6 class="fw-bold text-white mb-3">Thông tin đồ án</h6>
                    <p class="text-white-50 small mb-1"><strong>Môn học:</strong> Chuyên đề tổng hợp</p>
                    <p class="text-white-50 small mb-1"><strong>Trường:</strong> Đại học Xây Dựng Hà Nội</p>
                    <p class="text-white-50 small mb-1"><strong>Nhóm thực hiện:</strong> Nhóm 9 (DuyDev98, duy0609, phamhoang0811)</p>
                    <p class="text-white-50 small mb-0"><strong>Công nghệ:</strong> PHP Laravel MVC & MySQL</p>
                </div>
            </div>

            <hr class="border-secondary my-4">

            <div class="text-center text-white-50 small">
                © {{ date('Y') }} Bookstore. Dự án Đồ án Lập trình Web Laravel MVC chuẩn. Mọi quyền được bảo lưu.
            </div>
        </div>
    </footer>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
