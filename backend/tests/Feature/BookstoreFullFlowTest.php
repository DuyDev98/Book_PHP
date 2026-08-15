<?php

namespace Tests\Feature;

use App\Models\BinhLuan;
use App\Models\DanhMuc;
use App\Models\DonHang;
use App\Models\KhachHang;
use App\Models\Sach;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class BookstoreFullFlowTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * 1. Kiểm tra khách truy cập xem được Trang chủ
     */
    public function test_guest_can_view_homepage()
    {
        $response = $this->get(route('home'));
        $response->assertStatus(200);
        $response->assertSee('BOOKSTORE');
        $response->assertSee('Danh Mục Nổi Bật');
    }

    /**
     * 2. Kiểm tra tìm kiếm và lọc sách
     */
    public function test_guest_can_search_and_filter_books()
    {
        $response = $this->get(route('books.index', ['keyword' => 'Đắc Nhân Tâm']));
        $response->assertStatus(200);
        $response->assertSee('Đắc Nhân Tâm');
    }

    /**
     * 3. Kiểm tra xem chi tiết sách
     */
    public function test_guest_can_view_book_detail()
    {
        $book = Sach::first();
        $this->assertNotNull($book);

        $response = $this->get(route('books.show', $book->MaSach));
        $response->assertStatus(200);
        $response->assertSee($book->TenSach);
    }

    /**
     * 4. Kiểm tra xem sách không tồn tại (xử lý lỗi 404 không tìm thấy dữ liệu)
     */
    public function test_view_nonexistent_book_returns_404()
    {
        $response = $this->get(route('books.show', 9999999));
        $response->assertStatus(404);
    }

    /**
     * 5. Kiểm tra phân quyền: Khách chưa đăng nhập không thể vào Giỏ hàng & Checkout
     */
    public function test_guest_cannot_access_cart_or_checkout()
    {
        $response = $this->get(route('cart.index'));
        $response->assertRedirect(route('login'));

        $responseCheckout = $this->get(route('checkout.index'));
        $responseCheckout->assertRedirect(route('login'));
    }

    /**
     * 6. Kiểm tra phân quyền: Khách thường không được vào Trang Quản Trị (Admin)
     */
    public function test_customer_cannot_access_admin_dashboard()
    {
        $customer = User::where('VaiTro', 'customer')->first();
        $this->assertNotNull($customer);

        $response = $this->actingAs($customer)->get(route('admin.dashboard'));
        $response->assertRedirect(route('home'));
        $response->assertSessionHas('error');
    }

    /**
     * 7. Kiểm tra Quản trị viên (Admin) truy cập được Dashboard
     */
    public function test_admin_can_access_dashboard()
    {
        $admin = User::where('VaiTro', 'admin')->first();
        $this->assertNotNull($admin);

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));
        $response->assertStatus(200);
        $response->assertSee('Bảng Điều Khiển Quản Trị');
        $response->assertSee('Doanh Thu Thực Tế');
    }

    /**
     * 8. Kiểm tra Đăng ký: Bắt lỗi trùng username và thiếu dữ liệu
     */
    public function test_registration_validation_rules()
    {
        // Thử đăng ký với username đã tồn tại 'admin'
        $response = $this->post(route('register.submit'), [
            'username' => 'admin',
            'password' => '123456',
            'password_confirmation' => '123456',
            'ho_ten' => 'Người Dùng Test',
            'email' => 'test@example.com',
            'so_dien_thoai' => '0912345678',
            'dia_chi' => 'Hà Nội',
        ]);

        $response->assertSessionHasErrors(['username']);
    }

    /**
     * 9. Kiểm tra Đăng nhập: Bắt lỗi sai thông tin đăng nhập
     */
    public function test_login_validation_invalid_credentials()
    {
        $response = $this->post(route('login.submit'), [
            'username' => 'admin',
            'password' => 'sai_mat_khau_123',
        ]);

        $response->assertSessionHasErrors(['login_error']);
        $this->assertGuest();
    }

    /**
     * 10. Kiểm tra Đăng nhập thành công với tài khoản chuẩn
     */
    public function test_login_successful()
    {
        $response = $this->post(route('login.submit'), [
            'username' => 'admin',
            'password' => '123456',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticated();
    }

    /**
     * 11. Kiểm tra Luồng Giỏ hàng, Đặt hàng trừ tồn kho và Hủy đơn hoàn kho
     */
    public function test_cart_order_inventory_and_cancellation_flow()
    {
        $customer = User::where('VaiTro', 'customer')->first();
        $this->assertNotNull($customer);

        // Đảm bảo giỏ hàng sạch sẽ trước khi test
        $this->actingAs($customer)->post(route('cart.clear'));

        $book = Sach::where('SoLuongTon', '>', 5)->first();
        $this->assertNotNull($book);
        $initialStock = $book->SoLuongTon;

        // 1. Khách hàng thêm 2 cuốn vào giỏ
        $this->actingAs($customer)->post(route('cart.add'), [
            'ma_sach' => $book->MaSach,
            'so_luong' => 2,
        ]);

        // 2. Tiến hành đặt hàng
        $responseOrder = $this->actingAs($customer)->post(route('checkout.store'), [
            'ho_ten' => 'Khách Mua Test',
            'so_dien_thoai' => '0987654321',
            'dia_chi' => 'Số 10 Phố Huế, Hà Nội',
            'ghi_chu' => 'Giao hàng cẩn thận bọc bìa bóng',
        ]);

        // Kiểm tra tồn kho đã bị trừ đi 2 cuốn
        $book->refresh();
        $this->assertEquals($initialStock - 2, $book->SoLuongTon);

        // Lấy đơn hàng vừa tạo
        $donHang = DonHang::where('MaKH', $customer->khachHang->MaKH)->orderBy('MaDH', 'desc')->first();
        $this->assertNotNull($donHang);
        $this->assertEquals('ChoDuyet', $donHang->TrangThai);

        // 3. Khách hàng hủy đơn hàng -> Kiểm tra sách được tự động hoàn lại kho
        $this->actingAs($customer)->post(route('orders.cancel', $donHang->MaDH));

        $donHang->refresh();
        $this->assertEquals('Đã hủy', $donHang->TrangThai);

        $book->refresh();
        $this->assertEquals($initialStock, $book->SoLuongTon, 'Số lượng tồn kho phải được hoàn trả chính xác sau khi hủy đơn.');
    }

    /**
     * 12. Kiểm tra Admin CRUD Sách & Duyệt Bình luận
     */
    public function test_admin_book_crud_and_comment_moderation()
    {
        $admin = User::where('VaiTro', 'admin')->first();
        $category = DanhMuc::first();

        // 1. Thêm sách mới
        $responseStore = $this->actingAs($admin)->post(route('admin.books.store'), [
            'ten_sach' => 'Sách Kiểm Thử Tự Động Laravel 12',
            'gia_ban' => 150000,
            'so_luong_ton' => 30,
            'phan_tram_giam_gia' => 10,
            'ma_danhmuc' => $category->MaDanhMuc,
        ]);

        $responseStore->assertRedirect(route('admin.books.index'));

        $newBook = Sach::where('TenSach', 'Sách Kiểm Thử Tự Động Laravel 12')->first();
        $this->assertNotNull($newBook);
        $this->assertEquals(135000, (float) $newBook->GiaSale);

        // 2. Nhập thêm kho
        $this->actingAs($admin)->post(route('admin.books.restock', $newBook->MaSach), [
            'so_luong_them' => 15,
        ]);
        $newBook->refresh();
        $this->assertEquals(45, $newBook->SoLuongTon);

        // 3. Kiểm duyệt bình luận
        $customer = User::where('VaiTro', 'customer')->first();
        $comment = BinhLuan::create([
            'MaKH' => $customer->khachHang->MaKH,
            'MaSach' => $newBook->MaSach,
            'NoiDung' => 'Sách viết rất hay và dễ hiểu!',
            'NgayBinhLuan' => now(),
            'TrangThai' => 'pending',
        ]);

        $this->actingAs($admin)->post(route('admin.comments.approve', $comment->MaBL));
        $comment->refresh();
        $this->assertEquals('approved', $comment->TrangThai);
    }
}
