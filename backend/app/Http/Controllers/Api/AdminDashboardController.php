<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BinhLuan;
use App\Models\DonHang;
use App\Models\KhachHang;
use App\Models\Sach;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    /**
     * Thống kê Dashboard Admin theo chuẩn nghiệp vụ tài liệu đồ án
     */
    public function stats(Request $request)
    {
        $now = Carbon::now();

        // 1. Tổng doanh thu tháng (Chỉ đơn 'Đã giao' trong tháng này)
        $monthlyRevenue = DonHang::whereIn('TrangThai', ['Đã giao', 'DaGiao'])
            ->whereMonth('NgayDat', $now->month)
            ->whereYear('NgayDat', $now->year)
            ->sum('TongTien');

        // 2. Tổng doanh thu toàn thời gian (Chỉ đơn 'Đã giao')
        $totalRevenue = DonHang::whereIn('TrangThai', ['Đã giao', 'DaGiao'])->sum('TongTien');

        // 3. Tổng số đơn hàng trong tháng (Loại trừ hoàn toàn các đơn 'Đã hủy')
        $monthlyOrderCount = DonHang::whereNotIn('TrangThai', ['Đã hủy', 'DaHuy'])
            ->whereMonth('NgayDat', $now->month)
            ->whereYear('NgayDat', $now->year)
            ->count();

        // 4. Tổng số sách và số khách hàng
        $totalBooks = Sach::count();
        $totalCustomers = KhachHang::count();

        // 5. Số đơn chờ duyệt & bình luận chờ duyệt
        $pendingOrders = DonHang::whereIn('TrangThai', ['ChoDuyet', 'Chờ xác nhận', 'Chờ duyệt'])->count();
        $pendingComments = BinhLuan::where('TrangThai', 'pending')->count();

        // 6. Cảnh báo hết hàng (Sách có SoLuongTon < 10 theo tài liệu)
        $lowStockBooks = Sach::where('SoLuongTon', '<', 10)
            ->orderBy('SoLuongTon', 'asc')
            ->take(10)
            ->get();

        // 7. Biểu đồ doanh thu 7 ngày gần nhất (Chỉ tính đơn 'Đã giao')
        $sevenDaysAgo = Carbon::now()->subDays(6)->startOfDay();
        $dailyRevenuesRaw = DonHang::whereIn('TrangThai', ['Đã giao', 'DaGiao'])
            ->where('NgayDat', '>=', $sevenDaysAgo)
            ->select(
                DB::raw('DATE(NgayDat) as date'),
                DB::raw('SUM(TongTien) as revenue'),
                DB::raw('COUNT(*) as order_count')
            )
            ->groupBy(DB::raw('DATE(NgayDat)'))
            ->get()
            ->keyBy('date');

        $chartLabels = [];
        $chartData = [];
        for ($i = 6; $i >= 0; $i--) {
            $d = Carbon::now()->subDays($i)->format('Y-m-d');
            $displayDate = Carbon::now()->subDays($i)->format('d/m');
            $chartLabels[] = $displayDate;
            $chartData[] = isset($dailyRevenuesRaw[$d]) ? (float) $dailyRevenuesRaw[$d]->revenue : 0;
        }

        // 8. Đơn hàng mới nhất
        $recentOrders = DonHang::with('khachHang')
            ->orderBy('NgayDat', 'desc')
            ->take(5)
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'monthlyRevenue' => (float) $monthlyRevenue,
                'totalRevenue' => (float) $totalRevenue,
                'monthlyOrderCount' => $monthlyOrderCount,
                'totalBooks' => $totalBooks,
                'totalCustomers' => $totalCustomers,
                'pendingOrders' => $pendingOrders,
                'pendingComments' => $pendingComments,
                'lowStockBooks' => $lowStockBooks,
                'revenueChart' => [
                    'labels' => $chartLabels,
                    'series' => $chartData,
                ],
                'recentOrders' => $recentOrders,
            ],
        ]);
    }
}
