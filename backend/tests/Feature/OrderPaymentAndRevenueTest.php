<?php

namespace Tests\Feature;

use App\Models\ChiTietDonHang;
use App\Models\DonHang;
use App\Models\KhachHang;
use App\Models\Sach;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class OrderPaymentAndRevenueTest extends TestCase
{
    use DatabaseTransactions;

    protected $adminUser;
    protected $customerUser;
    protected $customer;
    protected $book1;
    protected $book2;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminUser = User::where('VaiTro', 'admin')->first();
        $this->customerUser = User::where('VaiTro', 'customer')->first();
        $this->customer = $this->customerUser->khachHang;

        // Lấy 2 cuốn sách mẫu có đủ tồn kho
        $books = Sach::where('SoLuongTon', '>=', 10)->take(2)->get();
        $this->book1 = $books[0];
        $this->book2 = $books[1];
    }

    /**
     * Test: Đặt hàng tính đúng tiền, đúng phí ship và trừ tồn kho
     */
    public function test_order_creation_calculates_payment_correctly_and_decrements_stock()
    {
        $initialStock = $this->book1->SoLuongTon;

        $response = $this->actingAs($this->customerUser, 'sanctum')->postJson('/api/orders/create', [
            'DiaChiGiaoHang' => '123 Đường ABC, Hà Nội',
            'GhiChuGiaoHang' => 'Giao giờ hành chính',
            'items' => [
                [
                    'MaSach' => $this->book1->MaSach,
                    'SoLuong' => 2,
                ]
            ]
        ]);

        $response->assertStatus(201);
        $orderData = $response->json('data');

        $this->book1->refresh();
        $this->assertEquals($initialStock - 2, $this->book1->SoLuongTon, 'Tồn kho phải trừ 2 cuốn');

        $expectedSubtotal = $this->book1->gia_hien_hanh * 2;
        $expectedShipping = ($expectedSubtotal >= 250000) ? 0 : 30000;
        $expectedTotal = $expectedSubtotal + $expectedShipping;

        $this->assertEquals($expectedTotal, (float) $orderData['TongTien'], 'Tổng tiền thanh toán phải tính đúng');
    }

    /**
     * Test: Đơn hàng mới đặt (ChoDuyet) hoặc Đã hủy KHÔNG được tính vào tổng doanh thu
     */
    public function test_revenue_excludes_pending_and_cancelled_orders()
    {
        // Lấy doanh thu ban đầu
        $initialStats = $this->actingAs($this->adminUser, 'sanctum')->getJson('/api/admin/dashboard')->json('data');
        $initialRevenue = $initialStats['totalRevenue'];

        // Tạo đơn hàng mới ở trạng thái ChoDuyet
        $donHang = DonHang::create([
            'MaKH' => $this->customer->MaKH,
            'NgayDat' => now(),
            'TongTien' => 200000,
            'PhiVanChuyen' => 30000,
            'DiaChiGiaoHang' => 'Hà Nội',
            'TrangThai' => 'ChoDuyet',
        ]);

        ChiTietDonHang::create([
            'MaDH' => $donHang->MaDH,
            'MaSach' => $this->book1->MaSach,
            'SoLuong' => 1,
            'DonGia' => 170000,
        ]);

        // Doanh thu phải giữ nguyên (không tính đơn ChoDuyet)
        $statsAfterPending = $this->actingAs($this->adminUser, 'sanctum')->getJson('/api/admin/dashboard')->json('data');
        $this->assertEquals($initialRevenue, $statsAfterPending['totalRevenue'], 'Đơn ChoDuyet không được cộng vào doanh thu');

        // Khách hàng hủy đơn hàng
        $cancelResponse = $this->actingAs($this->customerUser, 'sanctum')->putJson("/api/orders/{$donHang->MaDH}/cancel");
        $cancelResponse->assertStatus(200);

        // Doanh thu sau khi khách hủy vẫn phải chuẩn xác
        $statsAfterCancel = $this->actingAs($this->adminUser, 'sanctum')->getJson('/api/admin/dashboard')->json('data');
        $this->assertEquals($initialRevenue, $statsAfterCancel['totalRevenue'], 'Doanh thu phải giữ nguyên chính xác khi khách hàng hủy đơn');
    }

    /**
     * Test: Khi đơn chuyển sang Đã giao thì cộng doanh thu, nếu sau đó chuyển sang Đã hủy thì doanh thu PHẢI GIẢM LẠI
     */
    public function test_revenue_updates_correctly_when_delivered_and_then_cancelled()
    {
        $initialStats = $this->actingAs($this->adminUser, 'sanctum')->getJson('/api/admin/dashboard')->json('data');
        $initialRevenue = $initialStats['totalRevenue'];

        $stockBefore = $this->book1->SoLuongTon;

        // 1. Tạo đơn hàng và chuyển sang Đã giao
        $donHang = DonHang::create([
            'MaKH' => $this->customer->MaKH,
            'NgayDat' => now(),
            'TongTien' => 250000,
            'PhiVanChuyen' => 30000,
            'DiaChiGiaoHang' => 'Hà Nội',
            'TrangThai' => 'Đang giao',
        ]);

        ChiTietDonHang::create([
            'MaDH' => $donHang->MaDH,
            'MaSach' => $this->book1->MaSach,
            'SoLuong' => 1,
            'DonGia' => 220000,
        ]);
        $this->book1->decrement('SoLuongTon', 1);

        // Chuyển sang Đã giao
        $this->actingAs($this->adminUser, 'sanctum')->putJson("/api/orders/{$donHang->MaDH}/status", [
            'TrangThai' => 'Đã giao'
        ])->assertStatus(200);

        // Doanh thu phải tăng thêm 250.000đ
        $statsDelivered = $this->actingAs($this->adminUser, 'sanctum')->getJson('/api/admin/dashboard')->json('data');
        $this->assertEquals($initialRevenue + 250000, $statsDelivered['totalRevenue'], 'Doanh thu phải cộng thêm đơn Đã giao');

        // 2. Chuyển sang Đã hủy (Khách hoàn trả/hủy sau giao)
        $updateCancel = $this->actingAs($this->adminUser, 'sanctum')->putJson("/api/orders/{$donHang->MaDH}/status", [
            'TrangThai' => 'Đã hủy'
        ]);
        $updateCancel->assertStatus(200);

        // Doanh thu PHẢI TRỪ ĐI, quay về mức ban đầu
        $statsCancelled = $this->actingAs($this->adminUser, 'sanctum')->getJson('/api/admin/dashboard')->json('data');
        $this->assertEquals($initialRevenue, $statsCancelled['totalRevenue'], 'Tổng doanh thu phải giảm đi đúng số tiền của đơn bị hủy');

        // Sách phải được hoàn lại kho
        $this->book1->refresh();
        $this->assertEquals($stockBefore, $this->book1->SoLuongTon, 'Sách phải được tự động hoàn lại tồn kho khi đơn bị hủy');
    }

    /**
     * Test: Khi xóa hết các món lẻ trong đơn, đơn phải tự chuyển sang Đã hủy và tổng tiền về 0
     */
    public function test_delete_all_items_cancels_order_and_sets_total_to_zero()
    {
        $donHang = DonHang::create([
            'MaKH' => $this->customer->MaKH,
            'NgayDat' => now(),
            'TongTien' => 130000,
            'PhiVanChuyen' => 30000,
            'DiaChiGiaoHang' => 'Hà Nội',
            'TrangThai' => 'ChoDuyet',
        ]);

        ChiTietDonHang::create([
            'MaDH' => $donHang->MaDH,
            'MaSach' => $this->book1->MaSach,
            'SoLuong' => 1,
            'DonGia' => 100000,
        ]);
        $stockBefore = $this->book1->SoLuongTon;

        // Xóa món duy nhất trong đơn
        $res = $this->actingAs($this->adminUser, 'sanctum')
            ->deleteJson("/api/orders/{$donHang->MaDH}/items/{$this->book1->MaSach}");
        $res->assertStatus(200);

        $donHang->refresh();
        $this->assertEquals('Đã hủy', $donHang->TrangThai, 'Đơn không còn món nào phải tự động chuyển sang Đã hủy');
        $this->assertEquals(0, (float) $donHang->TongTien, 'Tổng tiền phải về 0 khi không còn món');

        $this->book1->refresh();
        $this->assertEquals($stockBefore + 1, $this->book1->SoLuongTon, 'Phải hoàn kho cho món bị xóa');
    }

    /**
     * Test: Best-sellers chỉ tính trên đơn Đã giao, đơn Đã hủy hoặc ChoDuyet không được tính
     */
    public function test_best_sellers_only_counts_delivered_orders()
    {
        // 1. Tạo đơn Đã giao với book2
        $donGiao = DonHang::create([
            'MaKH' => $this->customer->MaKH,
            'NgayDat' => now(),
            'TongTien' => 500000,
            'PhiVanChuyen' => 0,
            'DiaChiGiaoHang' => 'Hà Nội',
            'TrangThai' => 'Đã giao',
        ]);
        ChiTietDonHang::create([
            'MaDH' => $donGiao->MaDH,
            'MaSach' => $this->book2->MaSach,
            'SoLuong' => 5,
            'DonGia' => 100000,
        ]);

        // 2. Tạo đơn Đã hủy với book1 (số lượng lớn hơn)
        $donHuy = DonHang::create([
            'MaKH' => $this->customer->MaKH,
            'NgayDat' => now(),
            'TongTien' => 1000000,
            'PhiVanChuyen' => 0,
            'DiaChiGiaoHang' => 'Hà Nội',
            'TrangThai' => 'Đã hủy',
        ]);
        ChiTietDonHang::create([
            'MaDH' => $donHuy->MaDH,
            'MaSach' => $this->book1->MaSach,
            'SoLuong' => 20,
            'DonGia' => 50000,
        ]);

        $res = $this->actingAs($this->adminUser, 'sanctum')->getJson('/api/orders/best-sellers');
        $res->assertStatus(200);
        $data = $res->json('data');

        // Sách trong đơn hủy không được lọt vào top 1 nếu đơn không giao
        $bookIds = array_column($data, 'MaSach');
        $this->assertContains($this->book2->MaSach, $bookIds);
    }
}
