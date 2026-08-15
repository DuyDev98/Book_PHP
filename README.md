# ĐỒ ÁN HỆ THỐNG QUẢN LÝ VÀ BÁN SÁCH TRỰC TUYẾN (BOOKSTORE E-COMMERCE)

> **Môn học:** Chuyên đề tổng hợp  
> **Nhóm thực hiện:** Nhóm 9 — Trường Đại học Xây Dựng Hà Nội  
> **Công nghệ:** PHP Laravel (RESTful API, Sanctum Token, Eloquent ORM), MySQL 8.4, HTML5, Bootstrap 5, JavaScript (Fetch/Axios), SweetAlert2, Chart.js, Cloudinary API Service.

---

## 1. TỔNG QUAN KIẾN TRÚC HỆ THỐNG
Dự án được xây dựng theo mô hình **1B — RESTful API Backend kết hợp Frontend tách biệt (Client & Admin)**, bám sát 100% tài liệu thiết kế hệ thống tại [TaiLieu/Bao_Cao_Thiet_Ke_He_Thong_Va_Dac_Ta_API.md](file:///A:/bookPHP/TaiLieu/Bao_Cao_Thiet_Ke_He_Thong_Va_Dac_Ta_API.md):

```
bookPHP/
├── backend/                  # Mã nguồn Laravel 12 RESTful API
│   ├── app/
│   │   ├── Http/Controllers/Api/  # Auth, Book, Catalog, Cart, Order, Comment, AdminDashboard
│   │   ├── Http/Middleware/       # AdminMiddleware phân quyền
│   │   ├── Models/                # 11 Eloquent Models chuẩn ERD
│   │   └── Services/              # CloudinaryService (Upload CDN & Local fallback)
│   ├── database/migrations/       # Migration tạo 11 bảng CSDL
│   ├── database/seeders/          # Seeder dữ liệu mẫu phong phú
│   └── routes/api.php             # 46 API Endpoints
├── frontend/                 # Giao diện Web tĩnh gọi API không đồng bộ
│   ├── index.html            # Trang chủ (Banner, Flash Sale, Sách mới)
│   ├── books.html            # Danh sách sách, bộ lọc đa tầng & phân trang
│   ├── book-detail.html      # Chi tiết sách, tồn kho, bình luận đánh giá
│   ├── cart.html             # Giỏ hàng mua sắm
│   ├── checkout.html         # Đặt hàng thanh toán COD
│   ├── orders.html           # Lịch sử đơn hàng & hủy đơn hoàn kho
│   ├── login.html            # Đăng nhập & Đăng ký
│   ├── profile.html          # Hồ sơ khách hàng
│   ├── admin/                # Phân hệ Quản trị viên
│   │   ├── index.html        # Dashboard, Biểu đồ Chart.js 7 ngày, Xuất Excel Doanh thu
│   │   ├── books.html        # Quản lý Sách, Upload ảnh, Nhập hàng
│   │   ├── categories.html   # Quản lý Danh mục phân cấp cha - con
│   │   ├── booktypes.html    # Quản lý Định dạng & Loại sách (Bìa mềm, Bìa cứng,...)
│   │   ├── authors.html      # Quản lý Tác giả
│   │   ├── publishers.html   # Quản lý Nhà xuất bản
│   │   ├── orders.html       # Quản lý & duyệt đơn hàng, Xuất Excel đơn hàng
│   │   └── comments.html     # Kiểm duyệt bình luận độc giả
│   └── assets/               # CSS, JS (api.js, common.js)
├── start_bookstore.bat       # Script khởi động 1-click toàn bộ hệ thống
└── TaiLieu/                  # Hồ sơ báo cáo môn học hiện có
```

---

## 2. CƠ SỞ DỮ LIỆU CHUẨN HÓA (11 BẢNG)
Toàn bộ 11 bảng được thiết kế chuẩn hóa 3NF trong CSDL MySQL `bookstore_db`:
1. `user`: Tài khoản đăng nhập (Username PK, PassWord băm bcrypt, VaiTro: admin/customer).
2. `khachhang`: Hồ sơ cá nhân khách hàng (MaKH, HoTen, DiaChi, SDienThoai, Email).
3. `danhmuc`: Phân cấp cây danh mục đa cấp (MaDanhMuc, TenDanhMuc, ParentID).
4. `tacgia`: Thông tin tác giả (MaTG, TenTG, NamSinh, QueQuan, NamMat).
5. `nhaxuatban`: Nhà xuất bản (MaNXB, TenNXB, NamThanhLap, DiaChi).
6. `loaisach`: Phân loại sách (MaLoaiSach, TenLoaiSach).
7. `sach`: Kho sách (TenSach, AnhBia, GiaBan, PhanTramGiamGia, GiaSale, IsSale, SoLuongTon, MoTa).
8. `giohang`: Giỏ hàng của từng khách hàng (MaGioHang, MaKH, NgayTao).
9. `chitietgiohang`: Chi tiết các cuốn sách trong giỏ (MaGioHang, MaSach, SoLuong).
10. `donhang`: Đơn hàng (MaDH, MaKH, NgayDat, TongTien, PhiVanChuyen, DiaChiGiaoHang, TrangThai).
11. `chitietdonhang`: Chi tiết sản phẩm trong đơn, lưu snapshot đơn giá tại thời điểm mua.
12. `binhluan`: Đánh giá sách có kiểm duyệt (pending -> approved/rejected).

---

## 3. CÁC QUY TẮC NGHIỆP VỤ CỐT LÕI ĐÃ ĐƯỢC HIỆN THỰC
1. **Kiểm soát Tồn kho (Inventory Flow) & Giao dịch (ACID):**
   - Đặt hàng: Kiểm tra tồn kho từng món. Nếu đủ hàng, tạo đơn và trừ tồn kho ngay lập tức trong `DB::transaction`.
   - Hủy đơn: Khi khách hàng hoặc admin hủy đơn ở trạng thái chưa giao, hệ thống **tự động hoàn trả chính xác số lượng sách về lại kho**.
   - Admin xóa món lẻ trong đơn: Hoàn trả số lượng món đó về kho và tự động tính lại tổng tiền.
2. **Quy tắc Tính Doanh thu Chuẩn:**
   - Chỉ các đơn hàng có trạng thái **`TrangThai = 'Đã giao'`** mới được tính vào doanh thu thực tế.
   - Các đơn `ChoDuyet`, `Đang giao`, hoặc `Đã hủy` tuyệt đối không ghi nhận vào doanh thu.
   - Thống kê tự động hiển thị biểu đồ Chart.js 7 ngày và danh sách cảnh báo sắp hết hàng (`SoLuongTon < 10`).
   - Hỗ trợ nút **Xuất Báo Cáo Doanh Thu ra Excel (.xlsx)** đa trang (Tổng quan, 7 ngày, Đơn mới nhất, Cảnh báo hết hàng).
   - Hỗ trợ **Xuất Danh Sách Đơn Hàng ra Excel (.xlsx)** theo bộ lọc tìm kiếm và trạng thái.
3. **Kiểm duyệt Đánh giá (Moderation):**
   - Độc giả bình luận mặc định ở trạng thái `pending`. Chỉ sau khi Admin duyệt (`approved`) thì bình luận mới hiển thị công khai ở trang chi tiết sách.
4. **Cảnh Báo Thông Minh Khi Quản Lý Danh Mục, Tác Giả, NXB, Loại Sách:**
   - Khi xóa một thực thể đang có sách liên kết, hệ thống cảnh báo cụ thể số lượng sách ảnh hưởng để tránh thao tác nhầm lẫn.
5. **Lưu trữ Hình ảnh (Cloudinary & Local Storage):**
   - Cung cấp sẵn Service kết nối Cloudinary CDN qua API Key hoặc fallback tự động lưu an toàn trong máy chủ (`storage/app/public/uploads`).

---

## 4. HƯỚNG DẪN TRUY CẬP VÀ DEMO

### Địa chỉ truy cập:
* **Giao diện Khách Hàng (Website):** [http://127.0.0.1:3000](http://127.0.0.1:3000)
* **Giao diện Quản Trị (Admin Panel):** [http://127.0.0.1:3000/admin/index.html](http://127.0.0.1:3000/admin/index.html)
* **Backend RESTful API:** [http://127.0.0.1:8000/api](http://127.0.0.1:8000/api)

### Tài khoản đăng nhập thử nghiệm:
| Vai trò | Tên đăng nhập (Username) | Mật khẩu | Chức năng chính |
| :--- | :--- | :--- | :--- |
| **Quản trị viên (Admin)** | `admin` | `123456` | Toàn quyền Dashboard, Quản lý sách, Duyệt đơn hàng, Kiểm duyệt bình luận, Nhập kho |
| **Khách hàng 1** | `khachhang1` | `123456` | Mua sách, Xem giỏ hàng, Đặt hàng COD, Lịch sử đơn hàng, Gửi đánh giá |
| **Khách hàng 2** | `khachhang2` | `123456` | Mua sách, Quản lý hồ sơ cá nhân |

*(Có hỗ trợ nút 1-click tự động điền tài khoản tại trang [login.html](file:///A:/bookPHP/frontend/login.html)).*
