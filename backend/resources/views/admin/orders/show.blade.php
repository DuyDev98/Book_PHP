@extends('layouts.admin')

@section('title', 'Chi Tiết Đơn #DH-' . $donHang->MaDH . ' - Bookstore Admin')
@section('page_title', 'Chi Tiết Đơn Hàng #' . $donHang->MaDH)

@section('content')
<div class="card border-0 shadow-sm rounded-4 p-4 bg-white mb-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center border-bottom pb-3 mb-4 gap-3">
        <div>
            <h4 class="fw-bold text-primary mb-1">
                <i class="fa-solid fa-receipt me-2 text-warning"></i>Đơn Hàng #DH-{{ $donHang->MaDH }}
            </h4>
            <span class="text-muted small">Thời gian đặt: {{ \Carbon\Carbon::parse($donHang->NgayDat)->format('d/m/Y H:i:s') }}</span>
        </div>

        <div class="d-flex align-items-center gap-2">
            <!-- Form cập nhật trạng thái đơn hàng -->
            <form action="{{ route('admin.orders.status', $donHang->MaDH) }}" method="POST" class="d-flex align-items-center gap-2">
                @csrf
                <label class="small text-muted text-nowrap fw-semibold">Trạng thái:</label>
                <select name="trang_thai" class="form-select form-select-sm" style="width: 170px;">
                    <option value="ChoDuyet" {{ in_array($donHang->TrangThai, ['ChoDuyet']) ? 'selected' : '' }}>Chờ duyệt</option>
                    <option value="DangXuLy" {{ in_array($donHang->TrangThai, ['DangXuLy']) ? 'selected' : '' }}>Đang xử lý</option>
                    <option value="DangGiao" {{ in_array($donHang->TrangThai, ['DangGiao']) ? 'selected' : '' }}>Đang giao hàng</option>
                    <option value="Đã giao" {{ in_array($donHang->TrangThai, ['Đã giao', 'DaGiao']) ? 'selected' : '' }}>Đã giao thành công</option>
                    <option value="Đã hủy" {{ in_array($donHang->TrangThai, ['Đã hủy', 'DaHuy']) ? 'selected' : '' }}>Đã hủy (Hoàn kho)</option>
                </select>
                <button type="submit" class="btn btn-primary btn-sm px-3 rounded-pill">
                    Lưu
                </button>
            </form>
            <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill">
                Quay lại
            </a>
        </div>
    </div>

    <!-- Thông tin khách & giao hàng -->
    <div class="row g-4 mb-4">
        <div class="col-md-6">
            <div class="p-3 bg-light rounded-3 h-100">
                <h6 class="fw-bold text-dark mb-2">
                    <i class="fa-solid fa-user me-2 text-primary"></i>Thông Tin Khách Hàng
                </h6>
                <p class="mb-1 text-secondary"><strong>Họ tên:</strong> {{ $donHang->khachHang->HoTen ?? 'N/A' }}</p>
                <p class="mb-1 text-secondary"><strong>Tài khoản:</strong> {{ $donHang->khachHang->Username ?? 'N/A' }}</p>
                <p class="mb-1 text-secondary"><strong>Số điện thoại:</strong> {{ $donHang->khachHang->SDienThoai ?? 'N/A' }}</p>
                <p class="mb-0 text-secondary"><strong>Email:</strong> {{ $donHang->khachHang->Email ?? 'N/A' }}</p>
            </div>
        </div>

        <div class="col-md-6">
            <div class="p-3 bg-light rounded-3 h-100">
                <h6 class="fw-bold text-dark mb-2">
                    <i class="fa-solid fa-truck-fast me-2 text-success"></i>Thông Tin Giao Nhận & Thanh Toán
                </h6>
                <p class="mb-1 text-secondary"><strong>Địa chỉ nhận:</strong> {{ $donHang->DiaChiGiaoHang }}</p>
                <p class="mb-1 text-secondary"><strong>Hình thức thanh toán:</strong> COD (Tiền mặt khi nhận)</p>
                <p class="mb-1 text-secondary"><strong>Phí vận chuyển:</strong> {{ number_format($donHang->PhiVanChuyen, 0, ',', '.') }} đ</p>
                @if($donHang->GhiChuGiaoHang)
                    <p class="mb-0 text-muted small"><strong>Ghi chú:</strong> {{ $donHang->GhiChuGiaoHang }}</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Danh sách sản phẩm trong đơn -->
    <h5 class="fw-bold text-dark mb-3">
        <i class="fa-solid fa-boxes-stacked me-2 text-primary"></i>Danh Sách Sản Phẩm ({{ $donHang->chiTiets->count() }} mặt hàng)
    </h5>

    <div class="table-responsive mb-4">
        <table class="table align-middle table-hover">
            <thead class="table-light text-muted small">
                <tr>
                    <th>Sách</th>
                    <th class="text-center">Đơn giá bán</th>
                    <th class="text-center">Số lượng</th>
                    <th class="text-end">Thành tiền</th>
                    <th class="text-center" style="width: 80px;">Xóa món</th>
                </tr>
            </thead>
            <tbody>
                @php $subtotal = 0; @endphp
                @foreach($donHang->chiTiets as $ct)
                    @php $thanhTien = $ct->DonGia * $ct->SoLuong; $subtotal += $thanhTien; @endphp
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-3">
                                <img src="{{ $ct->sach->anh_bia_url ?? 'https://images.unsplash.com/photo-1544716278-ca5e3f4abd8c?w=500&q=80' }}" class="rounded shadow-sm" style="width: 45px; height: 60px; object-fit: cover;">
                                <div>
                                    <h6 class="mb-0 fw-bold text-dark">{{ $ct->sach->TenSach ?? 'Sách đã xóa khỏi hệ thống' }}</h6>
                                    <small class="text-muted">Mã: #{{ $ct->MaSach }}</small>
                                </div>
                            </div>
                        </td>
                        <td class="text-center fw-semibold">{{ number_format($ct->DonGia, 0, ',', '.') }} đ</td>
                        <td class="text-center fw-bold">{{ $ct->SoLuong }}</td>
                        <td class="text-end fw-bold text-danger">{{ number_format($thanhTien, 0, ',', '.') }} đ</td>
                        <td class="text-center">
                            @if(!in_array($donHang->TrangThai, ['Đã giao', 'DaGiao', 'Đã hủy', 'DaHuy']))
                                <form action="{{ route('admin.orders.removeItem', [$donHang->MaDH, $ct->MaSach]) }}" method="POST" onsubmit="return confirm('Bạn có chắc muốn xóa món này khỏi đơn? Số lượng sẽ tự động được hoàn lại kho.');">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-danger btn-sm py-0 px-2" title="Xóa món lẻ hoàn kho">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </form>
                            @else
                                <span class="text-muted small">-</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Tổng tiền -->
    <div class="row justify-content-end">
        <div class="col-md-5 col-lg-4">
            <div class="p-3 bg-light rounded-3">
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Tổng tiền hàng:</span>
                    <span class="fw-semibold">{{ number_format($subtotal, 0, ',', '.') }} đ</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Phí ship:</span>
                    <span class="fw-semibold">{{ number_format($donHang->PhiVanChuyen, 0, ',', '.') }} đ</span>
                </div>
                <hr class="my-2">
                <div class="d-flex justify-content-between align-items-baseline">
                    <span class="fw-bold fs-6">Tổng đơn thanh toán:</span>
                    <span class="fw-bold fs-3 text-danger">{{ number_format($donHang->TongTien, 0, ',', '.') }} đ</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
