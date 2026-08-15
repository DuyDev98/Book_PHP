@extends('layouts.app')

@section('title', $book->TenSach . ' - Chi Tiết Sách')

@section('content')
<div class="container my-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none">Trang chủ</a></li>
            <li class="breadcrumb-item"><a href="{{ route('books.index') }}" class="text-decoration-none">Kho sách</a></li>
            @if($book->danhMuc)
                <li class="breadcrumb-item"><a href="{{ route('books.index', ['category_id' => $book->MaDanhMuc]) }}" class="text-decoration-none">{{ $book->danhMuc->TenDanhMuc }}</a></li>
            @endif
            <li class="breadcrumb-item active text-truncate" style="max-width: 300px;">{{ $book->TenSach }}</li>
        </ol>
    </nav>

    <!-- Chi tiết sách -->
    <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
        <div class="row g-4">
            <!-- Cột Trái: Ảnh bìa sách -->
            <div class="col-md-5 col-lg-4 text-center">
                <div class="position-relative d-inline-block shadow rounded-3 overflow-hidden">
                    <img src="{{ $book->anh_bia_url }}" alt="{{ $book->TenSach }}" class="img-fluid" style="max-height: 420px; object-fit: cover;">
                    @if($book->PhanTramGiamGia > 0)
                        <span class="badge bg-danger position-absolute top-0 start-0 m-3 px-3 py-2 fs-6">
                            -{{ $book->PhanTramGiamGia }}% GIẢM
                        </span>
                    @endif
                </div>
            </div>

            <!-- Cột Phải: Thông tin chi tiết & Mua hàng -->
            <div class="col-md-7 col-lg-8">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-1">
                        {{ $book->danhMuc->TenDanhMuc ?? 'Chưa phân loại' }}
                    </span>
                    <span class="badge bg-secondary-subtle text-secondary border px-3 py-1">
                        {{ $book->loaiSach->TenLoaiSach ?? 'Sách in' }}
                    </span>
                </div>

                <h2 class="fw-bold text-dark mb-3">{{ $book->TenSach }}</h2>

                <!-- Giá sách -->
                <div class="p-3 bg-light rounded-3 mb-4 d-flex align-items-baseline gap-3">
                    <span class="text-danger fw-bold fs-2">
                        {{ number_format($book->gia_hien_hanh, 0, ',', '.') }} đ
                    </span>
                    @if($book->IsSale && $book->GiaSale)
                        <del class="text-muted fs-5">{{ number_format($book->GiaBan, 0, ',', '.') }} đ</del>
                        <span class="text-success small fw-semibold">
                            (Tiết kiệm {{ number_format($book->GiaBan - $book->GiaSale, 0, ',', '.') }} đ)
                        </span>
                    @endif
                </div>

                <!-- Thông số xuất bản -->
                <div class="row g-2 mb-4 text-muted small">
                    <div class="col-sm-6">
                        <strong><i class="fa-solid fa-user-pen me-2 text-primary"></i>Tác giả:</strong>
                        <span class="text-dark">{{ $book->tacGia->TenTG ?? 'Chưa cập nhật' }}</span>
                    </div>
                    <div class="col-sm-6">
                        <strong><i class="fa-solid fa-building me-2 text-primary"></i>Nhà xuất bản:</strong>
                        <span class="text-dark">{{ $book->nhaXuatBan->TenNXB ?? 'Chưa cập nhật' }}</span>
                    </div>
                    <div class="col-sm-6">
                        <strong><i class="fa-solid fa-calendar me-2 text-primary"></i>Năm xuất bản:</strong>
                        <span class="text-dark">{{ $book->NamXuatBan ?? 'N/A' }}</span>
                    </div>
                    <div class="col-sm-6">
                        <strong><i class="fa-solid fa-rotate me-2 text-primary"></i>Tái bản lần:</strong>
                        <span class="text-dark">{{ $book->LanTaiBan ?? '1' }}</span>
                    </div>
                    <div class="col-sm-6">
                        <strong><i class="fa-solid fa-boxes-stacked me-2 text-primary"></i>Tình trạng kho:</strong>
                        @if($book->SoLuongTon > 10)
                            <span class="badge bg-success-subtle text-success">Còn hàng ({{ $book->SoLuongTon }} cuốn)</span>
                        @elseif($book->SoLuongTon > 0)
                            <span class="badge bg-warning-subtle text-warning border border-warning">Sắp hết hàng (Chỉ còn {{ $book->SoLuongTon }} cuốn)</span>
                        @else
                            <span class="badge bg-danger-subtle text-danger">Tạm hết hàng</span>
                        @endif
                    </div>
                </div>

                <!-- Form Thêm vào giỏ -->
                @if($book->SoLuongTon > 0)
                    <form action="{{ route('cart.add') }}" method="POST" class="d-flex flex-wrap align-items-center gap-3">
                        @csrf
                        <input type="hidden" name="ma_sach" value="{{ $book->MaSach }}">
                        <div class="input-group" style="width: 130px;">
                            <button type="button" class="btn btn-outline-secondary" onclick="let inp = document.getElementById('qtyInput'); if(inp.value > 1) inp.value--;">-</button>
                            <input type="number" id="qtyInput" name="so_luong" class="form-control text-center" value="1" min="1" max="{{ $book->SoLuongTon }}">
                            <button type="button" class="btn btn-outline-secondary" onclick="let inp = document.getElementById('qtyInput'); if(inp.value < {{ $book->SoLuongTon }}) inp.value++;">+</button>
                        </div>
                        <button type="submit" class="btn btn-primary btn-lg px-4 rounded-pill shadow-sm">
                            <i class="fa-solid fa-cart-plus me-2"></i>Thêm Vào Giỏ Hàng
                        </button>
                    </form>
                @else
                    <button class="btn btn-secondary btn-lg px-4 rounded-pill" disabled>
                        <i class="fa-solid fa-ban me-2"></i>Tạm Thời Hết Hàng
                    </button>
                @endif
            </div>
        </div>

        <hr class="my-4">

        <!-- Giới thiệu nội dung sách -->
        <div>
            <h5 class="fw-bold text-primary mb-3">
                <i class="fa-solid fa-align-left me-2 text-warning"></i>Giới Thiệu Nội Dung Sách
            </h5>
            <div class="text-secondary lh-lg" style="white-space: pre-line;">
                {{ $book->MoTa ?: 'Hiện chưa có phần giới thiệu chi tiết cho cuốn sách này.' }}
            </div>
        </div>
    </div>

    <!-- Khu vực Bình luận & Đánh giá độc giả -->
    <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
        <h5 class="fw-bold text-primary mb-4">
            <i class="fa-solid fa-comments me-2 text-warning"></i>Độc Giả Đánh Giá & Bình Luận ({{ $comments->count() }})
        </h5>

        <!-- Form gửi đánh giá -->
        @auth
            <div class="p-3 bg-light rounded-3 mb-4">
                <h6 class="fw-bold text-dark mb-2">Gửi nhận xét của bạn về cuốn sách:</h6>
                <form action="{{ route('books.comment', $book->MaSach) }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <textarea name="noi_dung" class="form-control @error('noi_dung') is-invalid @enderror" rows="3" placeholder="Chia sẻ cảm nhận, ấn tượng của bạn về nội dung sách (tối thiểu 5 ký tự)..." required>{{ old('noi_dung') }}</textarea>
                        @error('noi_dung')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <small class="text-muted"><i class="fa-solid fa-shield-halved me-1"></i>Bình luận sẽ được kiểm duyệt trước khi hiển thị công khai.</small>
                        <button type="submit" class="btn btn-primary btn-sm px-4 rounded-pill">
                            <i class="fa-solid fa-paper-plane me-1"></i>Gửi Đánh Giá
                        </button>
                    </div>
                </form>
            </div>
        @else
            <div class="alert alert-warning d-flex align-items-center justify-content-between rounded-3 mb-4">
                <div>
                    <i class="fa-solid fa-lock me-2"></i>Bạn cần đăng nhập để gửi bình luận đánh giá sách.
                </div>
                <a href="{{ route('login') }}" class="btn btn-warning btn-sm rounded-pill fw-semibold">Đăng nhập ngay</a>
            </div>
        @endauth

        <!-- Danh sách bình luận -->
        @if($comments->isEmpty())
            <div class="text-center py-4 text-muted">
                <i class="fa-regular fa-comment-dots fs-2 mb-2"></i>
                <p class="mb-0">Chưa có bình luận nào cho cuốn sách này. Hãy là người đầu tiên để lại cảm nhận!</p>
            </div>
        @else
            <div class="d-flex flex-column gap-3">
                @foreach($comments as $comment)
                    <div class="border-bottom pb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <div class="d-flex align-items-center gap-2">
                                <i class="fa-solid fa-circle-user text-primary fs-5"></i>
                                <strong class="text-dark">{{ $comment->khachHang->HoTen ?? 'Bạn đọc' }}</strong>
                            </div>
                            <small class="text-muted">{{ \Carbon\Carbon::parse($comment->NgayBinhLuan)->format('d/m/Y H:i') }}</small>
                        </div>
                        <p class="mb-0 text-secondary ps-4">{{ $comment->NoiDung }}</p>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- Sách cùng danh mục liên quan -->
    @if($relatedBooks->isNotEmpty())
        <div class="mb-4">
            <h4 class="fw-bold text-primary mb-3">
                <i class="fa-solid fa-bookmark me-2 text-warning"></i>Sách Cùng Thể Loại Có Thể Bạn Thích
            </h4>
            <div class="row g-4">
                @foreach($relatedBooks as $rel)
                    <div class="col-6 col-md-3">
                        <div class="card book-card shadow-sm h-100">
                            <img src="{{ $rel->anh_bia_url }}" class="card-img-top book-cover" alt="{{ $rel->TenSach }}" style="height: 200px; object-fit: cover;">
                            <div class="card-body p-3">
                                <h6 class="fw-bold text-dark text-truncate-2 mb-2">
                                    <a href="{{ route('books.show', $rel->MaSach) }}" class="text-dark text-decoration-none">
                                        {{ $rel->TenSach }}
                                    </a>
                                </h6>
                                <span class="text-danger fw-bold">
                                    {{ number_format($rel->gia_hien_hanh, 0, ',', '.') }} đ
                                </span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection
