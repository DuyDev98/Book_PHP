@extends('layouts.app')

@section('title', 'Tất Cả Sách - Bookstore')

@section('content')
<div class="container my-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none">Trang chủ</a></li>
            <li class="breadcrumb-item active">Kho sách</li>
        </ol>
    </nav>

    <div class="row g-4">
        <!-- Sidebar Bộ Lọc -->
        <div class="col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 sticky-top" style="top: 80px; z-index: 10;">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold mb-0 text-primary">
                        <i class="fa-solid fa-filter me-2 text-warning"></i>Bộ Lọc Sách
                    </h5>
                    <a href="{{ route('books.index') }}" class="btn btn-sm btn-outline-secondary rounded-pill py-0 px-2" style="font-size: 0.8rem;">
                        Đặt lại
                    </a>
                </div>

                <form action="{{ route('books.index') }}" method="GET">
                    <!-- Từ khóa -->
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Từ khóa tìm kiếm</label>
                        <input type="text" name="keyword" class="form-control form-control-sm" placeholder="Tên sách, tác giả..." value="{{ request('keyword') }}">
                    </div>

                    <!-- Danh mục -->
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Danh mục sách</label>
                        <select name="category_id" class="form-select form-select-sm">
                            <option value="">-- Tất cả danh mục --</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->MaDanhMuc }}" {{ request('category_id') == $cat->MaDanhMuc ? 'selected' : '' }}>
                                    {{ $cat->TenDanhMuc }} ({{ $cat->sachs_count }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Tác giả -->
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Tác giả</label>
                        <select name="author_id" class="form-select form-select-sm">
                            <option value="">-- Tất cả tác giả --</option>
                            @foreach($authors as $author)
                                <option value="{{ $author->MaTG }}" {{ request('author_id') == $author->MaTG ? 'selected' : '' }}>
                                    {{ $author->TenTG }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Nhà xuất bản -->
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Nhà xuất bản</label>
                        <select name="publisher_id" class="form-select form-select-sm">
                            <option value="">-- Tất cả NXB --</option>
                            @foreach($publishers as $pub)
                                <option value="{{ $pub->MaNXB }}" {{ request('publisher_id') == $pub->MaNXB ? 'selected' : '' }}>
                                    {{ $pub->TenNXB }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Loại sách -->
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Hình thức</label>
                        <select name="type_id" class="form-select form-select-sm">
                            <option value="">-- Tất cả hình thức --</option>
                            @foreach($types as $type)
                                <option value="{{ $type->MaLoaiSach }}" {{ request('type_id') == $type->MaLoaiSach ? 'selected' : '' }}>
                                    {{ $type->TenLoaiSach }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Khoảng giá -->
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Khoảng giá (VNĐ)</label>
                        <div class="d-flex gap-2">
                            <input type="number" name="min_price" class="form-control form-control-sm" placeholder="Từ" value="{{ request('min_price') }}">
                            <input type="number" name="max_price" class="form-control form-control-sm" placeholder="Đến" value="{{ request('max_price') }}">
                        </div>
                    </div>

                    <!-- Chỉ sách giảm giá -->
                    <div class="mb-3 form-check">
                        <input type="checkbox" name="is_sale" value="1" class="form-check-input" id="checkSale" {{ request('is_sale') == 1 ? 'checked' : '' }}>
                        <label class="form-check-label small fw-semibold" for="checkSale">Chỉ sách đang giảm giá</label>
                    </div>

                    <!-- Ẩn giữ sắp xếp hiện tại -->
                    <input type="hidden" name="sort" value="{{ request('sort', 'newest') }}">

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary btn-sm rounded-pill">
                            <i class="fa-solid fa-magnifying-glass me-1"></i>Áp Dụng Bộ Lọc
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Danh sách Sách & Sắp xếp -->
        <div class="col-lg-9">
            <!-- Header bar: Số lượng kết quả & Dropdown sắp xếp -->
            <div class="card border-0 shadow-sm rounded-4 p-3 mb-4">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                    <div>
                        <span class="text-muted">Tìm thấy <strong>{{ $books->total() }}</strong> đầu sách phù hợp</span>
                    </div>

                    <div class="d-flex align-items-center gap-2">
                        <label class="small text-muted text-nowrap">Sắp xếp theo:</label>
                        <select class="form-select form-select-sm" style="width: 180px;" onchange="location = this.value;">
                            <option value="{{ request()->fullUrlWithQuery(['sort' => 'newest']) }}" {{ request('sort') == 'newest' ? 'selected' : '' }}>Mới nhất</option>
                            <option value="{{ request()->fullUrlWithQuery(['sort' => 'price_asc']) }}" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Giá tăng dần</option>
                            <option value="{{ request()->fullUrlWithQuery(['sort' => 'price_desc']) }}" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Giá giảm dần</option>
                            <option value="{{ request()->fullUrlWithQuery(['sort' => 'name_asc']) }}" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>Tên sách A-Z</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Grid Sách -->
            @if($books->isEmpty())
                <div class="card border-0 shadow-sm rounded-4 text-center py-5">
                    <div class="py-4">
                        <i class="fa-solid fa-book-skull fs-1 text-muted mb-3"></i>
                        <h5 class="fw-bold text-dark">Không tìm thấy cuốn sách nào!</h5>
                        <p class="text-muted small">Hãy thử tìm kiếm với từ khóa khác hoặc xóa bớt tiêu chí lọc.</p>
                        <a href="{{ route('books.index') }}" class="btn btn-outline-primary btn-sm rounded-pill mt-2">Xem tất cả sách</a>
                    </div>
                </div>
            @else
                <div class="row g-4">
                    @foreach($books as $book)
                        <div class="col-6 col-md-4">
                            <div class="card book-card shadow-sm h-100">
                                <div class="position-relative">
                                    <img src="{{ $book->anh_bia_url }}" class="card-img-top book-cover" alt="{{ $book->TenSach }}" style="height: 250px; object-fit: cover;">
                                    @if($book->PhanTramGiamGia > 0)
                                        <span class="badge bg-danger position-absolute top-0 start-0 m-2 px-2 py-1">
                                            -{{ $book->PhanTramGiamGia }}%
                                        </span>
                                    @endif
                                    @if($book->SoLuongTon <= 0)
                                        <div class="position-absolute top-0 start-0 w-100 h-100 bg-dark bg-opacity-50 d-flex align-items-center justify-content-center">
                                            <span class="badge bg-danger fs-6">Hết Hàng</span>
                                        </div>
                                    @endif
                                </div>
                                <div class="card-body d-flex flex-column justify-content-between p-3">
                                    <div>
                                        <small class="text-muted d-block mb-1">
                                            <i class="fa-solid fa-user-pen me-1"></i>{{ $book->tacGia->TenTG ?? 'Chưa rõ tác giả' }}
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
                                                <button class="btn btn-secondary btn-sm w-100 rounded-pill" disabled>Tạm hết hàng</button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Phân trang chuẩn Laravel -->
                <div class="d-flex justify-content-center mt-5">
                    {{ $books->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
