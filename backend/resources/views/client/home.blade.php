@extends('layouts.app')

@section('title', 'Nhà Sách Trực Tuyến Bookstore - Trang Chủ')

@section('content')
    <!-- Hero Banner -->
    <div class="hero-banner">
        <div class="container text-center py-4">
            <h1 class="display-4 fw-bold mb-3">Thế Giới Sách Tri Thức Trong Tầm Tay</h1>
            <p class="lead mb-4 mx-auto" style="max-width: 700px;">
                Khám phá hàng ngàn tựa sách kinh điển, sách chuyên ngành lập trình, văn học kinh điển và kỹ năng sống với mức giá ưu đãi nhất.
            </p>
            <div class="d-flex justify-content-center gap-3">
                <a href="{{ route('books.index') }}" class="btn btn-warning btn-lg px-4 fw-bold rounded-pill">
                    <i class="fa-solid fa-compass me-2"></i>Khám Phá Ngay
                </a>
                <a href="{{ route('books.index', ['is_sale' => 1]) }}" class="btn btn-outline-light btn-lg px-4 rounded-pill">
                    <i class="fa-solid fa-tags me-2"></i>Sách Đang Giảm Giá
                </a>
            </div>
        </div>
    </div>

    <div class="container">
        <!-- Danh mục nổi bật -->
        <section class="mb-5">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h3 class="fw-bold mb-0 text-primary">
                    <i class="fa-solid fa-shapes me-2 text-warning"></i>Danh Mục Nổi Bật
                </h3>
                <a href="{{ route('books.index') }}" class="text-decoration-none fw-semibold">Xem tất cả <i class="fa-solid fa-arrow-right"></i></a>
            </div>
            <div class="row g-3">
                @foreach($categories as $cat)
                    <div class="col-6 col-md-3">
                        <a href="{{ route('books.index', ['category_id' => $cat->MaDanhMuc]) }}" class="text-decoration-none">
                            <div class="card h-100 border-0 shadow-sm rounded-4 text-center p-3 category-box hover-shadow">
                                <div class="mb-2 text-primary fs-3">
                                    <i class="fa-solid fa-book-bookmark"></i>
                                </div>
                                <h6 class="fw-bold text-dark mb-1">{{ $cat->TenDanhMuc }}</h6>
                                <span class="badge bg-light text-muted border">{{ $cat->sachs_count }} đầu sách</span>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
        </section>

        <!-- Sách khuyến mãi (Flash Sale) -->
        @if($saleBooks->isNotEmpty())
            <section class="mb-5">
                <div class="p-3 bg-danger bg-gradient text-white rounded-4 mb-3 d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fa-solid fa-bolt fs-4 text-warning"></i>
                        <h4 class="mb-0 fw-bold">SIÊU SALE SÁCH HAY - GIÁ TỐT NHẤT</h4>
                    </div>
                    <a href="{{ route('books.index', ['is_sale' => 1]) }}" class="btn btn-sm btn-light text-danger fw-bold rounded-pill">Xem tất cả</a>
                </div>

                <div class="row g-4">
                    @foreach($saleBooks as $book)
                        <div class="col-6 col-md-4 col-lg-3">
                            <div class="card book-card shadow-sm">
                                <div class="position-relative">
                                    <img src="{{ $book->anh_bia_url }}" class="card-img-top book-cover" alt="{{ $book->TenSach }}" style="height: 260px; object-fit: cover;">
                                    @if($book->PhanTramGiamGia > 0)
                                        <span class="badge bg-danger position-absolute top-0 start-0 m-2 px-2 py-1 fs-6">
                                            -{{ $book->PhanTramGiamGia }}%
                                        </span>
                                    @endif
                                    @if($book->SoLuongTon <= 0)
                                        <div class="position-absolute top-0 start-0 w-100 h-100 bg-dark bg-opacity-50 d-flex align-items-center justify-content-center">
                                            <span class="badge bg-danger fs-6">Tạm Hết Hàng</span>
                                        </div>
                                    @endif
                                </div>
                                <div class="card-body d-flex flex-direction-column justify-content-between p-3">
                                    <div>
                                        <small class="text-muted d-block mb-1">
                                            <i class="fa-solid fa-user-pen me-1"></i>{{ $book->tacGia->TenTG ?? 'Nhiều tác giả' }}
                                        </small>
                                        <h6 class="card-title fw-bold text-dark text-truncate-2" title="{{ $book->TenSach }}">
                                            <a href="{{ route('books.show', $book->MaSach) }}" class="text-dark text-decoration-none">
                                                {{ $book->TenSach }}
                                            </a>
                                        </h6>
                                    </div>
                                    <div class="mt-3">
                                        <div class="d-flex align-items-baseline gap-2 mb-2">
                                            <span class="text-danger fw-bold fs-5">
                                                {{ number_format($book->gia_hien_hanh, 0, ',', '.') }} đ
                                            </span>
                                            @if($book->IsSale && $book->GiaSale)
                                                <del class="text-muted small">{{ number_format($book->GiaBan, 0, ',', '.') }} đ</del>
                                            @endif
                                        </div>
                                        <div class="d-grid gap-2">
                                            @if($book->SoLuongTon > 0)
                                                <form action="{{ route('cart.add') }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="ma_sach" value="{{ $book->MaSach }}">
                                                    <input type="hidden" name="so_luong" value="1">
                                                    <button type="submit" class="btn btn-outline-primary btn-sm w-100 rounded-pill">
                                                        <i class="fa-solid fa-cart-plus me-1"></i>Thêm giỏ hàng
                                                    </button>
                                                </form>
                                            @else
                                                <button class="btn btn-secondary btn-sm w-100 rounded-pill" disabled>Hết hàng</button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>
        @endif

        <!-- Sách mới nhất -->
        <section class="mb-5">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h3 class="fw-bold mb-0 text-primary">
                    <i class="fa-solid fa-sparkles me-2 text-warning"></i>Sách Mới Xuất Bản
                </h3>
                <a href="{{ route('books.index') }}" class="text-decoration-none fw-semibold">Xem thêm <i class="fa-solid fa-arrow-right"></i></a>
            </div>
            <div class="row g-4">
                @foreach($newBooks as $book)
                    <div class="col-6 col-md-4 col-lg-3">
                        <div class="card book-card shadow-sm">
                            <div class="position-relative">
                                <img src="{{ $book->anh_bia_url }}" class="card-img-top book-cover" alt="{{ $book->TenSach }}" style="height: 260px; object-fit: cover;">
                                <span class="badge bg-success position-absolute top-0 end-0 m-2">Mới</span>
                            </div>
                            <div class="card-body d-flex flex-direction-column justify-content-between p-3">
                                <div>
                                    <small class="text-muted d-block mb-1">
                                        <i class="fa-solid fa-user-pen me-1"></i>{{ $book->tacGia->TenTG ?? 'Chưa rõ' }}
                                    </small>
                                    <h6 class="card-title fw-bold text-dark text-truncate-2" title="{{ $book->TenSach }}">
                                        <a href="{{ route('books.show', $book->MaSach) }}" class="text-dark text-decoration-none">
                                            {{ $book->TenSach }}
                                        </a>
                                    </h6>
                                </div>
                                <div class="mt-3">
                                    <div class="d-flex align-items-baseline gap-2 mb-2">
                                        <span class="text-primary fw-bold fs-5">
                                            {{ number_format($book->gia_hien_hanh, 0, ',', '.') }} đ
                                        </span>
                                    </div>
                                    <div class="d-grid gap-2">
                                        @if($book->SoLuongTon > 0)
                                            <form action="{{ route('cart.add') }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="ma_sach" value="{{ $book->MaSach }}">
                                                <input type="hidden" name="so_luong" value="1">
                                                <button type="submit" class="btn btn-outline-primary btn-sm w-100 rounded-pill">
                                                    <i class="fa-solid fa-cart-plus me-1"></i>Thêm giỏ hàng
                                                </button>
                                            </form>
                                        @else
                                            <button class="btn btn-secondary btn-sm w-100 rounded-pill" disabled>Hết hàng</button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>

        <!-- Cam kết chất lượng -->
        <section class="p-4 bg-white rounded-4 shadow-sm my-5">
            <div class="row g-4 text-center">
                <div class="col-md-3">
                    <i class="fa-solid fa-shield-halved fs-1 text-primary mb-2"></i>
                    <h6 class="fw-bold mb-1">Sách Thật 100%</h6>
                    <small class="text-muted">Bảo đảm từ các nhà xuất bản uy tín</small>
                </div>
                <div class="col-md-3">
                    <i class="fa-solid fa-truck-fast fs-1 text-success mb-2"></i>
                    <h6 class="fw-bold mb-1">Giao Hàng Toàn Quốc</h6>
                    <small class="text-muted">Freeship cho đơn hàng từ 300K</small>
                </div>
                <div class="col-md-3">
                    <i class="fa-solid fa-rotate-left fs-1 text-warning mb-2"></i>
                    <h6 class="fw-bold mb-1">Đổi Trả Dễ Dàng</h6>
                    <small class="text-muted">Hỗ trợ đổi trả trong vòng 7 ngày</small>
                </div>
                <div class="col-md-3">
                    <i class="fa-solid fa-headset fs-1 text-danger mb-2"></i>
                    <h6 class="fw-bold mb-1">Hỗ Trợ 24/7</h6>
                    <small class="text-muted">Tư vấn chọn sách miễn phí</small>
                </div>
            </div>
        </section>
    </div>
@endsection
