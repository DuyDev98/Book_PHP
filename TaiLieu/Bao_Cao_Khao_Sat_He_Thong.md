# BÁO CÁO KHẢO SÁT HỆ THỐNG (SYSTEM SURVEY REPORT)

**Tên Dự án:** Xây dựng Website Quản lý và Bán Sách Trực Tuyến (Bookstore E-Commerce)
**Người lập báo cáo:** Nhóm Phân tích Hệ thống (System Analysts)
**Phiên bản tài liệu:** 2.0 (Chi tiết hóa dựa trên cấu trúc CSDL và tính năng thực tế)
**Ngày cập nhật:** 25/08/2026

---

## 1. MỤC ĐÍCH KHẢO SÁT
Báo cáo khảo sát này được thực hiện nhằm mục đích chuyển đổi quy trình kinh doanh truyền thống của nhà sách sang nền tảng kỹ thuật số (E-commerce). Các mục tiêu cụ thể bao gồm:
- **Đánh giá quy trình hiện tại:** Phân tích những điểm yếu trong việc quản lý kho, bán hàng và chăm sóc khách hàng offline.
- **Xác định yêu cầu kỹ thuật & nghiệp vụ:** Đưa ra bộ yêu cầu chi tiết (Business & System Requirements) bao gồm quản lý hàng hóa, xử lý đơn hàng, chương trình khuyến mãi và tương tác người dùng.
- **Làm cơ sở thiết kế hệ thống:** Cung cấp định hướng cho đội ngũ lập trình (Dev Team) trong việc xây dựng API Backend và thiết kế cơ sở dữ liệu (Database Schema).

## 2. HIỆN TRẠNG VÀ CÁC VẤN ĐỀ TỒN ĐỌNG (PAIN POINTS)
Trong mô hình bán lẻ sách offline hiện hành, nhà sách đang đối mặt với nhiều giới hạn:

### 2.1. Đối với Khách hàng (End-Users)
- **Hạn chế tiếp cận:** Chỉ có thể mua sách trực tiếp hoặc nhắn tin qua mạng xã hội, tốn thời gian xác nhận.
- **Thiếu thông tin sách:** Rất khó để tra cứu tác giả, nhà xuất bản, thể loại hay các chương trình giảm giá mà không hỏi trực tiếp nhân viên.
- **Trải nghiệm mua sắm rời rạc:** Thiếu không gian đánh giá sách khách quan từ cộng đồng độc giả khác. Không thể theo dõi lịch sử đơn hàng của mình.

### 2.2. Đối với Quản trị viên (Admin / Store Owner)
- **Quản lý dữ liệu phân tán:** Thông tin Sách, Tác giả, Nhà xuất bản được ghi chép thủ công. Khó quản lý sự liên kết giữa sách và phân loại danh mục phức tạp.
- **Không kiểm soát tốt tồn kho:** Số lượng tồn kho dễ xảy ra sai sót, dẫn đến tình trạng khách đặt mua nhưng không còn hàng.
- **Khó triển khai chương trình khuyến mãi:** Việc áp dụng giảm giá hàng loạt cho sách (Sale) phải tính toán thủ công, dễ nhầm lẫn giá bán.
- **Quản lý phản hồi kém:** Không có cơ chế kiểm duyệt nội dung phản hồi của khách hàng về sách.

## 3. PHÂN TÍCH ĐỐI THỦ VÀ BÀI HỌC ÁP DỤNG
So sánh với các nền tảng bán sách hàng đầu (Tiki, Fahasa, Shopee), hệ thống mới cần được thiết kế với các đặc điểm:
- **Danh mục đa tầng (Hierarchical Categories):** Học hỏi từ Fahasa, hệ thống cần thiết kế bảng `danhmuc` hỗ trợ phân cấp cha-con (ParentID) để tổ chức sách khoa học.
- **Hệ thống đánh giá có kiểm duyệt (Moderated Reviews):** Học hỏi từ Tiki, tính năng Bình luận cần có cơ chế duyệt nội dung (Trạng thái: Pending/Approved) để ngăn chặn spam/quảng cáo rác.
- **Giao diện & Cấu trúc giảm giá minh bạch:** Hiển thị rõ % giảm giá và Giá bán sau khi giảm trực tiếp trên sản phẩm.

## 4. YÊU CẦU CHỨC NĂNG CHI TIẾT (FUNCTIONAL REQUIREMENTS)
Dựa trên phân tích mã nguồn và thiết kế CSDL (MySQL/SQL Server), hệ thống chia thành 2 phân hệ (module) chính:

### Phân hệ Khách hàng (Customer Site)
1. **Quản lý Tài khoản (User/Customer Management):**
   - Đăng ký/Đăng nhập (Authentication) tài khoản.
   - Lưu trữ thông tin cá nhân (Họ tên, Địa chỉ, SĐT, Email).
2. **Khám phá và Tìm kiếm Sách:**
   - Xem danh sách sách theo Danh mục (`danhmuc`), Tác giả (`tacgia`), Nhà xuất bản (`nhaxuatban`), Loại sách (`loaisach`).
   - Lọc các sản phẩm đang có chương trình khuyến mãi (`IsSale = TRUE`).
   - Hiển thị chi tiết sách (Tên sách, Ảnh bìa, Tồn kho, Giá gốc, Giá Sale, % Giảm giá).
3. **Giỏ hàng (Shopping Cart):**
   - Thêm/Sửa/Xóa sách trong giỏ hàng.
   - Lưu trữ giỏ hàng trong CSDL (bảng `giohang` và `chitietgiohang`) để đồng bộ giữa các thiết bị.
4. **Thanh toán & Đơn hàng (Checkout & Orders):**
   - Đặt hàng và tính tổng tiền (Bao gồm phí vận chuyển).
   - Ghi chú giao hàng và xác nhận địa chỉ.
   - Xem lịch sử đơn hàng và chi tiết trạng thái đơn.
5. **Tương tác Độc giả:**
   - Cho phép khách hàng để lại bình luận, nhận xét về sách.
   - Bình luận sau khi đăng sẽ ở trạng thái chờ duyệt (Pending).

### Phân hệ Quản trị viên (Admin Panel)
1. **Quản lý Danh mục & Sản phẩm (Catalog Management):**
   - CRUD (Create, Read, Update, Delete) Danh mục đa cấp.
   - CRUD Tác giả và Nhà xuất bản (Quản lý cả địa chỉ NXB, năm sinh/mất của tác giả).
   - Quản lý Sách: Thêm sách mới, tải ảnh bìa lên Cloud (Cloudinary), gán danh mục, tác giả, NXB.
2. **Quản lý Tồn kho & Khuyến mãi (Inventory & Sales):**
   - Cập nhật số lượng tồn kho (`SoLuongTon`).
   - Quản lý cấu hình giảm giá: Cài đặt `% Giảm giá` (`PhanTramGiamGia`), hệ thống tự động tính toán `Giá Sale` (`GiaSale`) và cập nhật trạng thái `IsSale`.
3. **Quản lý Đơn hàng (Order Management):**
   - Duyệt đơn đặt hàng mới (`donhang`), chuyển trạng thái (Đang xử lý, Đang giao, Đã hoàn thành).
   - Xuất Hóa đơn (`hoadon`).
4. **Kiểm duyệt Bình luận (Comment Moderation):**
   - Xem danh sách bình luận của khách hàng.
   - Thay đổi trạng thái bình luận: Duyệt (Approved) hoặc Ẩn/Xóa bỏ.
5. **Quản lý Người dùng (User Management):**
   - Cấp quyền truy cập hệ thống (Role: Admin/Customer).

## 5. YÊU CẦU PHI CHỨC NĂNG (NON-FUNCTIONAL REQUIREMENTS)
1. **Bảo mật (Security):** 
   - Mật khẩu lưu trong CSDL (`user` table) phải được băm (hash) bằng `bcrypt`.
   - Các API nhạy cảm phải được bảo vệ bằng JSON Web Token (JWT).
2. **Hiệu suất & Khả năng mở rộng:**
   - Tối ưu hóa truy vấn SQL, đảm bảo thời gian tải danh sách sách nhanh chóng.
   - Sử dụng Cloudinary CDN để lưu trữ và tải hình ảnh bìa sách mượt mà, giảm tải cho máy chủ nội bộ.
3. **Môi trường hoạt động:**
   - Hệ thống Backend hoạt động trên nền tảng Node.js.
   - Tương thích tốt với các hệ quản trị CSDL quan hệ như MySQL hoặc SQL Server.

## 6. ĐỀ XUẤT GIẢI PHÁP TỔNG THỂ
Nhóm Phân tích Hệ thống thống nhất đề xuất việc xây dựng **Backend RESTful API** tập trung. API này sẽ đóng vai trò xử lý toàn bộ logic nghiệp vụ, giao tiếp với cơ sở dữ liệu quan hệ (được chuẩn hóa cao từ mức Category đến Order Details), và cung cấp dữ liệu định dạng JSON cho ứng dụng Frontend (Website hoặc Mobile App) sau này. Cơ chế bảo mật JWT và lưu trữ ảnh trên Cloudinary sẽ được áp dụng triệt để để đảm bảo tính hiện đại, bảo mật của dự án.
