<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Quản Trị Hệ Thống - Bookstore Admin')</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome 6 -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <!-- Custom Admin CSS -->
    <link href="{{ asset('css/admin.css') }}" rel="stylesheet">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .admin-main {
            margin-left: var(--admin-sidebar-width);
            padding: 25px;
            min-height: 100vh;
        }
        @media (max-width: 992px) {
            .admin-sidebar {
                margin-left: calc(-1 * var(--admin-sidebar-width));
            }
            .admin-sidebar.show {
                margin-left: 0;
            }
            .admin-main {
                margin-left: 0;
            }
        }
    </style>
    @stack('styles')
</head>
<body class="admin-body">

    <!-- Admin Sidebar -->
    <aside class="admin-sidebar" id="adminSidebar">
        <div class="sidebar-brand">
            <i class="fa-solid fa-book-bookmark text-warning"></i>
            <span>BOOKSTORE ADMIN</span>
        </div>

        <nav class="nav flex-column mt-3">
            <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                <i class="fa-solid fa-chart-pie"></i>
                <span>Bảng điều khiển</span>
            </a>

            <div class="px-3 pt-3 pb-1 text-uppercase text-secondary fw-semibold" style="font-size: 0.75rem; letter-spacing: 0.5px;">Quản lý dữ liệu</div>

            <a class="nav-link {{ request()->routeIs('admin.books.*') ? 'active' : '' }}" href="{{ route('admin.books.index') }}">
                <i class="fa-solid fa-book"></i>
                <span>Quản lý Sách</span>
            </a>
            <a class="nav-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}" href="{{ route('admin.categories.index') }}">
                <i class="fa-solid fa-layer-group"></i>
                <span>Quản lý Danh mục</span>
            </a>
            <a class="nav-link {{ request()->routeIs('admin.authors.*') ? 'active' : '' }}" href="{{ route('admin.authors.index') }}">
                <i class="fa-solid fa-feather-pointed"></i>
                <span>Quản lý Tác giả</span>
            </a>
            <a class="nav-link {{ request()->routeIs('admin.publishers.*') ? 'active' : '' }}" href="{{ route('admin.publishers.index') }}">
                <i class="fa-solid fa-building"></i>
                <span>Nhà xuất bản</span>
            </a>

            <div class="px-3 pt-3 pb-1 text-uppercase text-secondary fw-semibold" style="font-size: 0.75rem; letter-spacing: 0.5px;">Giao dịch & Tương tác</div>

            <a class="nav-link {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}" href="{{ route('admin.orders.index') }}">
                <i class="fa-solid fa-truck-fast"></i>
                <span>Quản lý Đơn hàng</span>
            </a>
            <a class="nav-link {{ request()->routeIs('admin.comments.*') ? 'active' : '' }}" href="{{ route('admin.comments.index') }}">
                <i class="fa-solid fa-comments"></i>
                <span>Kiểm duyệt Đánh giá</span>
            </a>

            <hr class="border-secondary mx-3 my-3">

            <a class="nav-link text-info" href="{{ route('home') }}" target="_blank">
                <i class="fa-solid fa-arrow-up-right-from-square"></i>
                <span>Xem Website Khách</span>
            </a>
        </nav>
    </aside>

    <!-- Main Content Area -->
    <div class="admin-main">
        <!-- Top Navbar -->
        <header class="d-flex justify-content-between align-items-center bg-white p-3 rounded-3 shadow-sm mb-4">
            <div class="d-flex align-items-center gap-3">
                <button class="btn btn-light d-lg-none" type="button" onclick="document.getElementById('adminSidebar').classList.toggle('show')">
                    <i class="fa-solid fa-bars"></i>
                </button>
                <h4 class="mb-0 fw-bold text-dark">@yield('page_title', 'Bảng Điều Khiển Quản Trị')</h4>
            </div>

            <div class="d-flex align-items-center gap-3">
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2 rounded-pill">
                    <i class="fa-solid fa-user-shield me-1"></i>{{ Auth::user()->Username ?? 'Admin' }} (Quản trị viên)
                </span>
                <form action="{{ route('logout') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-outline-danger btn-sm">
                        <i class="fa-solid fa-right-from-bracket me-1"></i>Đăng xuất
                    </button>
                </form>
            </div>
        </header>

        <!-- Flash messages -->
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
                <div class="fw-bold mb-1"><i class="fa-solid fa-triangle-exclamation me-2"></i>Lỗi dữ liệu:</div>
                <ul class="mb-0 ps-3">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Content Body -->
        @yield('content')
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
