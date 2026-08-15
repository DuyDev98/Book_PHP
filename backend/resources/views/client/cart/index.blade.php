@extends('layouts.app')

@section('title', 'Giỏ Hàng Của Bạn - Bookstore')

@section('content')
<div class="container my-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none">Trang chủ</a></li>
            <li class="breadcrumb-item active">Giỏ hàng</li>
        </ol>
    </nav>

    <h3 class="fw-bold text-primary mb-4">
        <i class="fa-solid fa-cart-shopping me-2 text-warning"></i>Giỏ Hàng Mua Sắm
    </h3>

    @if($items->isEmpty())
        <div class="card border-0 shadow-sm rounded-4 text-center py-5">
            <div class="py-5">
                <i class="fa-solid fa-cart-arrow-down fs-1 text-muted mb-3"></i>
                <h4 class="fw-bold text-dark">Giỏ hàng của bạn đang trống!</h4>
                <p class="text-muted">Bạn chưa thêm bất kỳ cuốn sách nào vào giỏ. Hãy dạo quanh kho sách để tìm những tựa sách yêu thích nhé.</p>
                <a href="{{ route('books.index') }}" class="btn btn-warning btn-lg px-4 rounded-pill fw-bold mt-2">
                    <i class="fa-solid fa-compass me-2"></i>Khám Phá Sách Ngay
                </a>
            </div>
        </div>
    @else
        <div class="row g-4">
            <!-- Cột Trái: Bảng danh sách sản phẩm trong giỏ -->
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-4 p-3">
                    <form action="{{ route('cart.update') }}" method="POST">
                        @csrf
                        <div class="table-responsive">
                            <table class="table align-middle">
                                <thead class="table-light text-muted small">
                                    <tr>
                                        <th>Sản phẩm</th>
                                        <th class="text-center">Đơn giá</th>
                                        <th class="text-center" style="width: 140px;">Số lượng</th>
                                        <th class="text-end">Thành tiền</th>
                                        <th class="text-center" style="width: 50px;"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($items as $item)
                                        @if($item->sach)
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center gap-3">
                                                        <img src="{{ $item->sach->anh_bia_url }}" alt="{{ $item->sach->TenSach }}" class="rounded shadow-sm" style="width: 60px; height: 80px; object-fit: cover;">
                                                        <div>
                                                            <h6 class="mb-1 fw-bold">
                                                                <a href="{{ route('books.show', $item->sach->MaSach) }}" class="text-dark text-decoration-none">
                                                                    {{ $item->sach->TenSach }}
                                                                </a>
                                                            </h6>
                                                            <small class="text-muted d-block">{{ $item->sach->tacGia->TenTG ?? '' }}</small>
                                                            <small class="text-secondary">Kho còn: {{ $item->sach->SoLuongTon }}</small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="text-center fw-semibold text-dark">
                                                    {{ number_format($item->sach->gia_hien_hanh, 0, ',', '.') }} đ
                                                </td>
                                                <td class="text-center">
                                                    <div class="input-group input-group-sm">
                                                        <button type="button" class="btn btn-outline-secondary" onclick="let inp = document.getElementById('qty_{{ $item->MaSach }}'); if(inp.value > 1) inp.value--;">-</button>
                                                        <input type="number" id="qty_{{ $item->MaSach }}" name="quantities[{{ $item->MaSach }}]" class="form-control text-center" value="{{ $item->SoLuong }}" min="1" max="{{ $item->sach->SoLuongTon }}">
                                                        <button type="button" class="btn btn-outline-secondary" onclick="let inp = document.getElementById('qty_{{ $item->MaSach }}'); if(inp.value < {{ $item->sach->SoLuongTon }}) inp.value++;">+</button>
                                                    </div>
                                                </td>
                                                <td class="text-end fw-bold text-danger">
                                                    {{ number_format($item->sach->gia_hien_hanh * $item->SoLuong, 0, ',', '.') }} đ
                                                </td>
                                                <td class="text-center">
                                                    <button type="submit" formaction="{{ route('cart.remove', $item->MaSach) }}" class="btn btn-sm text-danger hover-bg-light" title="Xóa món này">
                                                        <i class="fa-solid fa-trash-can"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mt-3 pt-3 border-top">
                            <a href="{{ route('books.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill">
                                <i class="fa-solid fa-arrow-left me-1"></i>Tiếp tục mua sách
                            </a>
                            <div class="d-flex gap-2">
                                <button type="submit" formaction="{{ route('cart.clear') }}" class="btn btn-outline-danger btn-sm rounded-pill" onclick="return confirm('Bạn có chắc muốn làm trống giỏ hàng?')">
                                    <i class="fa-solid fa-trash me-1"></i>Xóa sạch giỏ
                                </button>
                                <button type="submit" class="btn btn-primary btn-sm rounded-pill px-3">
                                    <i class="fa-solid fa-rotate me-1"></i>Cập nhật giỏ hàng
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Cột Phải: Bảng tóm tắt đơn hàng & Checkout -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 p-4 sticky-top" style="top: 80px;">
                    <h5 class="fw-bold text-dark mb-3">Tóm Tắt Đơn Hàng</h5>

                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Tạm tính tiền sách:</span>
                        <strong class="text-dark">{{ number_format($subtotal, 0, ',', '.') }} đ</strong>
                    </div>

                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Phí vận chuyển:</span>
                        <span class="{{ $shippingFee == 0 ? 'text-success fw-bold' : 'text-dark' }}">
                            {{ $shippingFee == 0 ? 'Miễn phí (Freeship)' : number_format($shippingFee, 0, ',', '.') . ' đ' }}
                        </span>
                    </div>

                    @if($shippingFee > 0)
                        <div class="alert alert-info py-2 px-3 small rounded-3 mb-3">
                            <i class="fa-solid fa-truck-fast me-1"></i>Mua thêm <strong>{{ number_format(300000 - $subtotal, 0, ',', '.') }} đ</strong> để được <strong>Miễn phí vận chuyển</strong>!
                        </div>
                    @else
                        <div class="alert alert-success py-2 px-3 small rounded-3 mb-3">
                            <i class="fa-solid fa-circle-check me-1"></i>Đơn hàng của bạn đã đủ điều kiện <strong>Freeship</strong>!
                        </div>
                    @endif

                    <hr class="my-3">

                    <div class="d-flex justify-content-between align-items-baseline mb-4">
                        <span class="fw-bold fs-6">Tổng thanh toán:</span>
                        <span class="fw-bold fs-4 text-danger">{{ number_format($total, 0, ',', '.') }} đ</span>
                    </div>

                    <div class="d-grid gap-2">
                        <a href="{{ route('checkout.index') }}" class="btn btn-warning btn-lg rounded-pill fw-bold shadow-sm">
                            <i class="fa-solid fa-credit-card me-2"></i>Tiến Hành Đặt Hàng
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
