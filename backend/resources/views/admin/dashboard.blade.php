@extends('layouts.admin')

@section('title', 'Bảng Điều Khiển Quản Trị - Bookstore Admin')
@section('page_title', 'Tổng Quan Hoạt Động Hệ Thống')

@section('content')
    <!-- 4 Thẻ Thống Kê Chính -->
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100 border-start border-success border-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-muted small text-uppercase fw-bold">Doanh Thu Thực Tế</span>
                        <h3 class="fw-bold text-success mb-0 mt-1">{{ number_format($tongDoanhThu, 0, ',', '.') }} đ</h3>
                        <small class="text-muted" style="font-size: 0.75rem;">(Chỉ tính đơn đã giao)</small>
                    </div>
                    <div class="bg-success-subtle text-success p-3 rounded-circle fs-3">
                        <i class="fa-solid fa-coins"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100 border-start border-primary border-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-muted small text-uppercase fw-bold">Tổng Đơn Hàng</span>
                        <h3 class="fw-bold text-primary mb-0 mt-1">{{ $tongDonHang }}</h3>
                        <small class="text-muted" style="font-size: 0.75rem;">Giao dịch phát sinh</small>
                    </div>
                    <div class="bg-primary-subtle text-primary p-3 rounded-circle fs-3">
                        <i class="fa-solid fa-receipt"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100 border-start border-info border-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-muted small text-uppercase fw-bold">Khách Hàng</span>
                        <h3 class="fw-bold text-info mb-0 mt-1">{{ $tongKhachHang }}</h3>
                        <small class="text-muted" style="font-size: 0.75rem;">Tài khoản đăng ký</small>
                    </div>
                    <div class="bg-info-subtle text-info p-3 rounded-circle fs-3">
                        <i class="fa-solid fa-users"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100 border-start border-warning border-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-muted small text-uppercase fw-bold">Kho Sách</span>
                        <h3 class="fw-bold text-warning mb-0 mt-1">{{ $tongSach }}</h3>
                        <small class="text-danger fw-semibold" style="font-size: 0.75rem;">{{ $lowStockBooks->count() }} sách sắp hết hàng</small>
                    </div>
                    <div class="bg-warning-subtle text-warning p-3 rounded-circle fs-3">
                        <i class="fa-solid fa-book"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Biểu đồ & Cảnh báo kho -->
    <div class="row g-4 mb-4">
        <!-- Biểu đồ doanh thu 7 ngày -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 p-4 bg-white h-100">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold text-dark mb-0">
                        <i class="fa-solid fa-chart-line text-primary me-2"></i>Biểu Đồ Doanh Thu 7 Ngày Gần Nhất
                    </h5>
                    <span class="badge bg-light text-muted border">Đơn vị: VNĐ</span>
                </div>
                <div style="height: 280px;">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Cảnh báo sắp hết hàng (<10) -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 p-4 bg-white h-100">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold text-danger mb-0">
                        <i class="fa-solid fa-triangle-exclamation me-2"></i>Cảnh Báo Tồn Kho
                    </h5>
                    <span class="badge bg-danger rounded-pill">{{ $lowStockBooks->count() }}</span>
                </div>

                @if($lowStockBooks->isEmpty())
                    <div class="text-center py-4 text-muted">
                        <i class="fa-solid fa-circle-check fs-2 text-success mb-2"></i>
                        <p class="mb-0 small">Kho sách hoạt động an toàn, không có sách nào dưới 10 cuốn.</p>
                    </div>
                @else
                    <div class="list-group list-group-flush" style="max-height: 280px; overflow-y: auto;">
                        @foreach($lowStockBooks as $lowBook)
                            <div class="list-group-item px-0 py-2 d-flex justify-content-between align-items-center">
                                <div class="text-truncate me-2" style="max-width: 180px;">
                                    <strong class="d-block text-dark text-truncate small" title="{{ $lowBook->TenSach }}">{{ $lowBook->TenSach }}</strong>
                                    <small class="text-muted">{{ $lowBook->tacGia->TenTG ?? 'Chưa rõ tác giả' }}</small>
                                </div>
                                <div class="text-end text-nowrap">
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle me-1">
                                        Còn: {{ $lowBook->SoLuongTon }}
                                    </span>
                                    <button type="button" class="btn btn-outline-primary btn-sm py-0 px-2" style="font-size: 0.75rem;" data-bs-toggle="modal" data-bs-target="#restockModal{{ $lowBook->MaSach }}">
                                        + Nhập
                                    </button>
                                </div>
                            </div>

                            <!-- Modal Nhập Kho Nhanh -->
                            <div class="modal fade" id="restockModal{{ $lowBook->MaSach }}" tabindex="-1">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <form action="{{ route('admin.books.restock', $lowBook->MaSach) }}" method="POST">
                                            @csrf
                                            <div class="modal-header">
                                                <h6 class="modal-title fw-bold">Nhập Kho: {{ $lowBook->TenSach }}</h6>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <p class="small text-muted mb-2">Tồn kho hiện tại: <strong>{{ $lowBook->SoLuongTon }} cuốn</strong></p>
                                                <div class="mb-3">
                                                    <label class="form-label small fw-semibold">Số lượng nhập thêm:</label>
                                                    <input type="number" name="so_luong_them" class="form-control" value="20" min="1" required>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Đóng</button>
                                                <button type="submit" class="btn btn-sm btn-primary">Xác Nhận Nhập Kho</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- 5 Đơn Hàng Gần Đây -->
    <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-bold text-dark mb-0">
                <i class="fa-solid fa-clock-rotate-left text-primary me-2"></i>Đơn Hàng Gần Đây
            </h5>
            <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-primary btn-sm rounded-pill">Xem tất cả đơn hàng</a>
        </div>

        <div class="table-responsive">
            <table class="table align-middle table-hover">
                <thead class="table-light text-muted small">
                    <tr>
                        <th>Mã đơn</th>
                        <th>Khách hàng</th>
                        <th>Ngày đặt</th>
                        <th>Tổng tiền</th>
                        <th>Trạng thái</th>
                        <th class="text-end">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentOrders as $ro)
                        <tr>
                            <td class="fw-bold text-primary">#DH-{{ $ro->MaDH }}</td>
                            <td>
                                <div>{{ $ro->khachHang->HoTen ?? 'Khách vãng lai' }}</div>
                                <small class="text-muted">{{ $ro->khachHang->SDienThoai ?? '' }}</small>
                            </td>
                            <td>{{ \Carbon\Carbon::parse($ro->NgayDat)->format('d/m/Y H:i') }}</td>
                            <td class="fw-bold text-danger">{{ number_format($ro->TongTien, 0, ',', '.') }} đ</td>
                            <td>
                                @if(in_array($ro->TrangThai, ['ChoDuyet']))
                                    <span class="badge bg-warning text-dark">Chờ duyệt</span>
                                @elseif(in_array($ro->TrangThai, ['DangXuLy', 'DangGiao']))
                                    <span class="badge bg-primary">Đang giao</span>
                                @elseif(in_array($ro->TrangThai, ['Đã giao', 'DaGiao']))
                                    <span class="badge bg-success">Đã giao</span>
                                @elseif(in_array($ro->TrangThai, ['Đã hủy', 'DaHuy']))
                                    <span class="badge bg-danger">Đã hủy</span>
                                @else
                                    <span class="badge bg-secondary">{{ $ro->TrangThai }}</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <a href="{{ route('admin.orders.show', $ro->MaDH) }}" class="btn btn-light btn-sm">
                                    <i class="fa-solid fa-eye me-1"></i>Chi tiết
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    const ctx = document.getElementById('revenueChart').getContext('2d');
    const revenueChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: {!! json_encode($chartLabels) !!},
            datasets: [{
                label: 'Doanh thu (VNĐ)',
                data: {!! json_encode($chartData) !!},
                borderColor: '#1e3c72',
                backgroundColor: 'rgba(30, 60, 114, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.35,
                pointBackgroundColor: '#ff9800',
                pointRadius: 5
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return new Intl.NumberFormat('vi-VN').format(value) + ' đ';
                        }
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });
</script>
@endpush
