@extends('layouts.admin')

@section('title', 'Thêm Sách Mới - Bookstore Admin')
@section('page_title', 'Thêm Sách Mới Vào Hệ Thống')

@section('content')
<div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
    <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-4">
        <h5 class="fw-bold text-primary mb-0">
            <i class="fa-solid fa-plus-circle me-2 text-warning"></i>Nhập Thông Tin Sách Mới
        </h5>
        <a href="{{ route('admin.books.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill">
            <i class="fa-solid fa-arrow-left me-1"></i>Quay lại danh sách
        </a>
    </div>

    <form action="{{ route('admin.books.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="row g-4">
            <!-- Cột trái: Thông tin cơ bản -->
            <div class="col-lg-8">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Tên sách <span class="text-danger">*</span></label>
                    <input type="text" name="ten_sach" class="form-control @error('ten_sach') is-invalid @enderror" value="{{ old('ten_sach') }}" placeholder="Nhập tiêu đề cuốn sách..." required>
                    @error('ten_sach')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Giá bìa (VNĐ) <span class="text-danger">*</span></label>
                        <input type="number" name="gia_ban" class="form-control @error('gia_ban') is-invalid @enderror" value="{{ old('gia_ban', 100000) }}" min="0" required>
                        @error('gia_ban')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Số lượng tồn kho <span class="text-danger">*</span></label>
                        <input type="number" name="so_luong_ton" class="form-control @error('so_luong_ton') is-invalid @enderror" value="{{ old('so_luong_ton', 20) }}" min="0" required>
                        @error('so_luong_ton')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Phần trăm giảm (%)</label>
                        <input type="number" name="phan_tram_giam_gia" class="form-control @error('phan_tram_giam_gia') is-invalid @enderror" value="{{ old('phan_tram_giam_gia', 0) }}" min="0" max="100">
                        @error('phan_tram_giam_gia')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Danh mục sách <span class="text-danger">*</span></label>
                        <select name="ma_danhmuc" class="form-select @error('ma_danhmuc') is-invalid @enderror" required>
                            <option value="">-- Chọn danh mục --</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->MaDanhMuc }}" {{ old('ma_danhmuc') == $cat->MaDanhMuc ? 'selected' : '' }}>
                                    {{ $cat->TenDanhMuc }}
                                </option>
                            @endforeach
                        </select>
                        @error('ma_danhmuc')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Tác giả <span class="text-danger">*</span></label>
                        <select name="ma_tg" class="form-select @error('ma_tg') is-invalid @enderror" required>
                            <option value="">-- Chọn tác giả --</option>
                            @foreach($authors as $author)
                                <option value="{{ $author->MaTG }}" {{ old('ma_tg') == $author->MaTG ? 'selected' : '' }}>
                                    {{ $author->TenTG }}
                                </option>
                            @endforeach
                        </select>
                        @error('ma_tg')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Nhà xuất bản</label>
                        <select name="ma_nxb" class="form-select">
                            <option value="">-- Chọn NXB --</option>
                            @foreach($publishers as $pub)
                                <option value="{{ $pub->MaNXB }}" {{ old('ma_nxb') == $pub->MaNXB ? 'selected' : '' }}>
                                    {{ $pub->TenNXB }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Hình thức đóng gói</label>
                        <select name="ma_loaisach" class="form-select">
                            <option value="">-- Chọn hình thức --</option>
                            @foreach($types as $t)
                                <option value="{{ $t->MaLoaiSach }}" {{ old('ma_loaisach') == $t->MaLoaiSach ? 'selected' : '' }}>
                                    {{ $t->TenLoaiSach }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Năm xuất bản</label>
                        <input type="number" name="nam_xuat_ban" class="form-control" value="{{ old('nam_xuat_ban', date('Y')) }}">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Lần tái bản</label>
                        <input type="number" name="lan_tai_ban" class="form-control" value="{{ old('lan_tai_ban', 1) }}" min="1">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Mô tả tóm tắt nội dung sách</label>
                    <textarea name="mo_ta" class="form-control" rows="5" placeholder="Nhập trích dẫn, giới thiệu nội dung hoặc mục lục sách...">{{ old('mo_ta') }}</textarea>
                </div>
            </div>

            <!-- Cột phải: Ảnh bìa sách -->
            <div class="col-lg-4">
                <div class="p-3 bg-light rounded-4 border">
                    <h6 class="fw-bold text-dark mb-3">Hình Ảnh Bìa Sách</h6>

                    <div class="mb-3 text-center">
                        <img id="imagePreview" src="https://images.unsplash.com/photo-1544716278-ca5e3f4abd8c?w=500&q=80" alt="Preview" class="img-fluid rounded shadow-sm" style="max-height: 250px; object-fit: cover;">
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Tải ảnh từ máy tính (Upload):</label>
                        <input type="file" name="anh_bia_file" class="form-control form-control-sm @error('anh_bia_file') is-invalid @enderror" accept="image/*" onchange="previewFile(this)">
                        <small class="text-muted" style="font-size: 0.75rem;">Hỗ trợ JPG, PNG, WEBP, tối đa 2MB</small>
                        @error('anh_bia_file')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Hoặc dán URL ảnh trực tiếp:</label>
                        <input type="url" name="anh_bia_url" class="form-control form-control-sm" placeholder="https://..." value="{{ old('anh_bia_url') }}" oninput="previewUrl(this.value)">
                    </div>
                </div>

                <div class="d-grid gap-2 mt-4">
                    <button type="submit" class="btn btn-primary btn-lg rounded-pill fw-bold shadow-sm">
                        <i class="fa-solid fa-floppy-disk me-2"></i>Lưu Sách Mới
                    </button>
                    <a href="{{ route('admin.books.index') }}" class="btn btn-outline-secondary rounded-pill">Hủy bỏ</a>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    function previewFile(input) {
        if (input.files && input.files[0]) {
            let reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('imagePreview').src = e.target.result;
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
    function previewUrl(url) {
        if (url && url.startsWith('http')) {
            document.getElementById('imagePreview').src = url;
        }
    }
</script>
@endsection
