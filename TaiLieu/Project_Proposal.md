# ĐỀ XUẤT DỰ ÁN (PROJECT PROPOSAL)

**Tên Dự án:** Hệ thống Quản lý và Bán hàng Nhà Sách Trực Tuyến (Bookstore RESTful API System)
**Mã Dự án:** PRJ-BOOKSTORE-26
**Phiên bản:** 2.0 (Cập nhật Kiến trúc & Công nghệ Chi tiết)
**Ngày đề xuất:** 25/08/2026

---

## 1. TỔNG QUAN DỰ ÁN (PROJECT OVERVIEW)

### 1.1 Bối cảnh (Context)
Sự bùng nổ của thương mại điện tử đòi hỏi các cửa hàng truyền thống phải số hóa mô hình kinh doanh. Dự án Bookstore được thiết kế nhằm xây dựng một hạ tầng phần mềm cốt lõi (Backend API) vững chắc cho một hệ thống bán sách trực tuyến đa nền tảng. Hệ thống không chỉ phục vụ việc bán hàng mà còn số hóa toàn bộ quy trình quản lý kho, quản lý danh mục sách phức tạp và chăm sóc khách hàng.

### 1.2 Mục tiêu Dự án (Objectives)
- Xây dựng hệ thống Backend API (RESTful) chuẩn hóa, bảo mật, phục vụ luồng nghiệp vụ mua bán sách trực tuyến.
- Quản lý vòng đời sản phẩm từ nhập kho, phân loại danh mục, tác giả, nhà xuất bản đến cấu hình giá khuyến mãi linh hoạt.
- Quản trị toàn diện đơn hàng, giỏ hàng, hóa đơn, và kiểm duyệt phản hồi cộng đồng (bình luận).
- Đảm bảo tính mở rộng cao (Scalability) để dễ dàng tích hợp Frontend (Web, Mobile App) trong tương lai.

---

## 2. PHẠM VI DỰ ÁN (SCOPE OF WORK)

Dự án tập trung vào việc thiết kế và phát triển RESTful API kết nối với Cơ sở dữ liệu quan hệ. Phạm vi triển khai bao gồm các Modules (Router) chính:

### 2.1. Module Quản trị Định danh & Xác thực (Auth Module)
- **`UserRouter.js` / `khachhang.router.js`**: Đăng ký, đăng nhập. Cấp phát và xác thực token JWT, mã hóa mật khẩu `bcrypt`. Liên kết tài khoản User với thông tin định danh Khách hàng (Họ tên, SĐT, Địa chỉ).

### 2.2. Module Catalog (Sản phẩm & Thuộc tính)
- **`sach.router.js`**: Quản lý thông tin sách toàn diện (Tên, Hình ảnh Cloudinary, Mô tả chi tiết, Tồn kho, Giá gốc, Giá Sale). Tích hợp tìm kiếm, lọc sách đang khuyến mãi.
- **`danhmuc.router.js`**: Quản lý danh mục đa cấp (hỗ trợ phân cấp Parent-Child).
- **`tacgia.router.js` & `nhaxuatban.router.js` & `loaisach.router.js`**: Quản lý thông tin chi tiết về Tác giả (Năm sinh, Năm mất, Quê quán) và Nhà xuất bản (Năm thành lập, Địa chỉ).

### 2.3. Module Bán hàng (Sales Module)
- **`cart.router.js` & `cartDetail.router.js`**: Quản lý giỏ hàng của từng khách hàng, thêm bớt số lượng sách trong giỏ.
- **`order.router.js` & `hoadon.router.js`**: Khởi tạo đơn đặt hàng (`donhang`), tính toán tổng tiền, phí vận chuyển (`PhiVanChuyen`), ghi chú giao hàng. Chuyển đổi Đơn hàng thành Hóa đơn thanh toán (`hoadon`).
- **`payment.router.js`**: API xử lý tích hợp với các cổng thanh toán nội địa/quốc tế.

### 2.4. Module Quản lý Cộng đồng & Tương tác
- **`binhluan.router.js`**: Xử lý logic độc giả bình luận sách. Hệ thống kiểm duyệt với trạng thái `TrangThai` (pending/approved) dành cho Admin.

### 2.5. Module Quản trị viên (Admin Module)
- **`admin.router.js`**: Cung cấp các endpoint nội bộ cho Dashboard, thống kê doanh thu, số lượng đơn hàng, xử lý kiểm duyệt.

---

## 3. KIẾN TRÚC VÀ CÔNG NGHỆ ĐỀ XUẤT (TECH STACK)

### 3.1 Nền tảng Server & Ngôn ngữ
- **Môi trường (Runtime):** Node.js
- **Web Framework:** Express.js 5.x (Routing, Middleware xử lý logic).
- **Kiến trúc:** RESTful API với định dạng trao đổi dữ liệu JSON. Cơ chế bảo vệ Route bằng Middleware và cấu hình CORS.

### 3.2 Cơ sở dữ liệu (Database)
- **Hệ quản trị CSDL:** Hỗ trợ đa nền tảng quan hệ - MySQL (`mysql2`) và SQL Server (`mssql`).
- **Lý do chọn CSDL Quan hệ (RDBMS):** Hệ thống E-commerce yêu cầu tính nhất quán dữ liệu cao (ACID) đối với các giao dịch tài chính (Đơn hàng, Hóa đơn) và tính toàn vẹn tham chiếu giữa Sách - Tác Giả - Danh Mục - Khách hàng.

### 3.3 Thư viện & Dịch vụ Bổ trợ
- **Xác thực (Authentication):** `jsonwebtoken` (JWT).
- **Bảo mật (Security):** `bcrypt` (Hashing password).
- **Quản lý Hình ảnh (Media Storage):** `multer` & `multer-storage-cloudinary` kết hợp dịch vụ `Cloudinary` giúp tiết kiệm băng thông Server.

---

## 4. MÔ HÌNH CƠ SỞ DỮ LIỆU (DATABASE SCHEMA HIGHLIGHTS)
Dự án được thiết kế với cơ sở dữ liệu chuẩn hóa (Normalized DB), bao gồm các bảng cốt lõi (Tham khảo file SQL thiết kế):
1. **Thực thể Sản phẩm:** `sach`, `danhmuc`, `tacgia`, `nhaxuatban`, `loaisach`.
   *Đáng chú ý:* Bảng `sach` chứa các trường `PhanTramGiamGia`, `GiaSale`, `IsSale` hỗ trợ cực tốt cho các nghiệp vụ Marketing (Flash sale, giảm giá).
2. **Thực thể Giao dịch:** `giohang`, `chitietgiohang`, `donhang`, `chitietdonhang`, `hoadon`, `chitiethoadon`.
   *Đáng chú ý:* Tách bạch giữa Giỏ hàng (chưa chốt), Đơn hàng (Đã đặt) và Hóa đơn (Giao dịch tài chính) giúp dễ đối soát.
3. **Thực thể Người dùng & Tương tác:** `user`, `khachhang`, `binhluan`.

---

## 5. LỘ TRÌNH TRIỂN KHAI (IMPLEMENTATION ROADMAP)

Dự kiến phát triển trong 06 Sprint (Mỗi Sprint 1-2 tuần):
- **Sprint 1:** Cấu hình Server Node.js, thiết lập kết nối MySQL/MSSQL, chuẩn hóa cấu trúc thư mục MVC (Models, Controllers, Routes). Cài đặt Cloudinary.
- **Sprint 2:** Hoàn thiện Auth API (Login, Register, JWT Middleware). API CRUD cơ bản cho Tác giả, Danh mục, NXB.
- **Sprint 3:** API Quản lý Sách cốt lõi (Xử lý upload ảnh, logic tính giá Sale dựa vào phần trăm giảm giá).
- **Sprint 4:** Xây dựng luồng Mua hàng: Giỏ hàng -> Đơn hàng -> Hóa đơn. Trừ tồn kho (`SoLuongTon`) hợp lý.
- **Sprint 5:** Tính năng Bình luận (Kèm logic kiểm duyệt) và Admin Dashboard API.
- **Sprint 6:** Kiểm thử API (Postman/Jest), Tối ưu hiệu năng Query SQL. Viết tài liệu API (Swagger/Postman Collection) và Deploy lên Cloud (AWS/Vercel/Render).

---

## 6. RỦI RO VÀ GIẢI PHÁP (RISKS & MITIGATION)
- **Rủi ro Đồng thời (Concurrency):** Nhiều khách hàng cùng đặt mua 1 cuốn sách dẫn đến xuất kho âm.
  - *Giải pháp:* Áp dụng SQL Transaction (BEGIN...COMMIT...ROLLBACK) khi Insert chi tiết đơn hàng và Trừ tồn kho.
- **Rủi ro Bảo mật Hình ảnh:** Cloudinary có thể bị tràn dung lượng nếu không kiểm soát size ảnh.
  - *Giải pháp:* Cấu hình `multer` giới hạn kích thước file upload (< 5MB) và chỉ chấp nhận định dạng `.jpg`, `.png`.

---
**Đề xuất bởi:** Technical Architect Team
**Phê duyệt:** Ban Giám đốc Dự án
