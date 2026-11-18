<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BinhLuan;
use App\Models\DonHang;
use App\Models\KhachHang;
use App\Models\Sach;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Doanh thu thực tế (Chỉ tính đơn đã giao thành công)
        $tongDoanhThu = DonHang::whereIn('TrangThai', ['Đã giao', 'DaGiao'])->sum('TongTien');

        // 2. Tổng số đơn hàng thực tế (Loại trừ đơn đã hủy)
        $tongDonHang = DonHang::whereNotIn('TrangThai', ['Đã hủy', 'DaHuy'])->count();

        // 3. Tổng số khách hàng
        $tongKhachHang = KhachHang::count();

        // 4. Tổng số đầu sách
        $tongSach = Sach::count();

        // 5. Cảnh báo sắp hết hàng (Tồn kho < 10)
        $lowStockBooks = Sach::with(['tacGia', 'danhMuc'])
            ->where('SoLuongTon', '<', 10)
            ->orderBy('SoLuongTon', 'asc')
            ->get();

        // 6. Đơn hàng gần đây
        $recentOrders = DonHang::with('khachHang')
            ->orderBy('MaDH', 'desc')
            ->take(5)
            ->get();

        // 7. Bình luận chờ duyệt
        $pendingCommentsCount = BinhLuan::where('TrangThai', 'pending')->count();

        // 8. Dữ liệu biểu đồ doanh thu 7 ngày gần nhất
        $chartLabels = [];
        $chartData = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $dateStr = $date->format('Y-m-d');
            $chartLabels[] = $date->format('d/m');

            $revenueOnDate = DonHang::whereIn('TrangThai', ['Đã giao', 'DaGiao'])
                ->whereDate('NgayDat', $dateStr)
                ->sum('TongTien');

            $chartData[] = (float) $revenueOnDate;
        }

        return view('admin.dashboard', compact(
            'tongDoanhThu',
            'tongDonHang',
            'tongKhachHang',
            'tongSach',
            'lowStockBooks',
            'recentOrders',
            'pendingCommentsCount',
            'chartLabels',
            'chartData'
        ));
    }
}
