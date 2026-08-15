@extends('layouts.app')

@section('title', 'Chi Tiết Đơn Hàng #DH-' . $donHang->MaDH . ' - Bookstore')

@section('content')
<div class="container my-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none">Trang chủ</a></li>
            <li class="breadcrumb-item"><a href="{{ route('orders.index') }}" class="text-decoration-none">Đơn hàng của tôi</a></li>
            <li class="breadcrumb-item active">#DH-{{ $donHang->MaDH }}</li>
        </ol>
    </nav>

    <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
        <div class="d-flex flex-wrap justify-content-between align-items-center border-bottom pb-3 mb-4 gap-3">
            <div>
                <h3 class="fw-bold text-primary mb-1">
                    <i class="fa-solid fa-receipt me-2 text-warning"></i>Đơn Hàng #DH-{{ $donHang->MaDH }}
                </h3>
                <span class="text-muted small">Thời gian đặt: {{ \Carbon\Carbon::parse($donHang->NgayDat)->format('d/m/Y H:i:s') }}</span>
            </div>

            <div class="d-flex align-items-center gap-2">
                @if(in_array($donHang->TrangThai, ['ChoDuyet']))
                    <span class="badge bg-warning text-dark px-3 py-2 rounded-pill fs-6"><i class="fa-solid fa-hourglass-half me-1"></i>Chờ duyệt</span>
                @elseif(in_array($donHang->TrangThai, ['DangXuLy', 'DangGiao']))
                    <span class="badge bg-primary px-3 py-2 rounded-pill fs-6"><i class="fa-solid fa-truck me-1"></i>Đang giao hàng</span>
                @elseif(in_array($donHang->TrangThai, ['Đã giao', 'DaGiao']))
                    <span class="badge bg-success px-3 py-2 rounded-pill fs-6"><i class="fa-solid fa-check me-1"></i>Giao thành công</span>
                @elseif(in_array($donHang->TrangThai, ['Đã hủy', 'DaHuy']))
                    <span class="badge bg-danger px-3 py-2 rounded-pill fs-6"><i class="fa-solid fa-xmark me-1"></i>Đã hủy</span>
                @else
                    <span class="badge bg-secondary px-3 py-2 rounded-pill fs-6">{{ $donHang->TrangThai }}</span>
                @endif
            </div>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-md-6">
                <div class="p-3 bg-light rounded-3 h-100">
                    <h6 class="fw-bold text-dark mb-2">
                        <i class="fa-solid fa-location-dot me-2 text-danger"></i>Thông Tin Giao Hàng
                    </h6>
                    <p class="mb-1 text-secondary"><strong>Người nhận / Địa chỉ:</strong> {{ $donHang->DiaChiGiaoHang }}</p>
                    @if($donHang->GhiChuGiaoHang)
                        <p class="mb-0 text-muted small"><strong>Ghi chú:</strong> {{ $donHang->GhiChuGiaoHang }}</p>
                    @endif
                </div>
            </div>

            <div class="col-md-6">
                <div class="p-3 bg-light rounded-3 h-100">
                    <h6 class="fw-bold text-dark mb-2">
                        <i class="fa-solid fa-wallet me-2 text-success"></i>Thanh Toán & Vận Chuyển
                    </h6>
                    <p class="mb-1 text-secondary"><strong>Hình thức:</strong> Thanh toán khi nhận hàng (COD)</p>
                    <p class="mb-0 text-secondary"><strong>Phí giao hàng:</strong> {{ $donHang->PhiVanChuyen == 0 ? 'Miễn phí' : number_format($donHang->PhiVanChuyen, 0, ',', '.') . ' đ' }}</p>
                </div>
            </div>
        </div>

        <h5 class="fw-bold text-dark mb-3">Danh Sách Sản Phẩm Trong Đơn</h5>

        <div class="table-responsive mb-4">
            <table class="table align-middle">
                <thead class="table-light text-muted small">
                    <tr>
                        <th>Sách</th>
                        <th class="text-center">Đơn giá</th>
                        <th class="text-center">Số lượng</th>
                        <th class="text-end">Thành tiền</th>
                    </tr>
                </thead>
                <tbody>
                    @php $subtotal = 0; @endphp
                    @foreach($donHang->chiTiets as $ct)
                        @php $thanhTien = $ct->DonGia * $ct->SoLuong; $subtotal += $thanhTien; @endphp
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    <img src="{{ $ct->sach->anh_bia_url ?? 'https://images.unsplash.com/photo-1544716278-ca5e3f4abd8c?w=500&q=80' }}" class="rounded shadow-sm" style="width: 50px; height: 70px; object-fit: cover;">
                                    <div>
                                        <h6 class="mb-1 fw-bold text-dark">{{ $ct->sach->TenSach ?? 'Sách đã xóa' }}</h6>
                                        <small class="text-muted">{{ $ct->sach->tacGia->TenTG ?? '' }}</small>
                                    </div>
                                </div>
                            </td>
                            <td class="text-center">{{ number_format($ct->DonGia, 0, ',', '.') }} đ</td>
                            <td class="text-center fw-bold">{{ $ct->SoLuong }}</td>
                            <td class="text-end fw-bold text-danger">{{ number_format($thanhTien, 0, ',', '.') }} đ</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="row justify-content-end">
            <div class="col-md-5 col-lg-4">
                <div class="p-3 bg-light rounded-3">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Tiền sách:</span>
                        <span class="fw-semibold">{{ number_format($subtotal, 0, ',', '.') }} đ</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Phí ship:</span>
                        <span class="fw-semibold">{{ number_format($donHang->PhiVanChuyen, 0, ',', '.') }} đ</span>
                    </div>
                    <hr class="my-2">
                    <div class="d-flex justify-content-between align-items-baseline">
                        <span class="fw-bold">Tổng thanh toán:</span>
                        <span class="fw-bold fs-4 text-danger">{{ number_format($donHang->TongTien, 0, ',', '.') }} đ</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
            <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill">
                <i class="fa-solid fa-arrow-left me-1"></i>Quay lại danh sách đơn
            </a>

            @if($donHang->TrangThai === 'ChoDuyet')
                <form action="{{ route('orders.cancel', $donHang->MaDH) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn hủy đơn hàng này? Sách sẽ được tự động hoàn lại kho.');">
                    @csrf
                    <button type="submit" class="btn btn-danger btn-sm rounded-pill px-3">
                        <i class="fa-solid fa-ban me-1"></i>Hủy đơn hàng này
                    </button>
                </form>
            @endif
        </div>
    </div>
</div>
@endsection
