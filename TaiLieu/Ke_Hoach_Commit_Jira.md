# KẾ HOẠCH PHÂN CHIA COMMIT CHI TIẾT THEO JIRA (BS-1 ĐẾN BS-74)
**Khoảng thời gian:** 15/08/2026 — 07/09/2026 (Hiện tại)  
**Phân bổ thành viên & Tỉ lệ đóng góp:**
- 🥇 **DuyDev98** (Lead / Backend Core & Database): **40 commits (50%)**
- 🥈 **duy0609** (Frontend Developer / UI/UX): **24 commits (30%)**
- 🥉 **phamhoang0811** (QA / Fullstack Support): **16 commits (20%)**
- **Tổng cộng:** **80 commits** (Bao gồm đầy đủ các task từ BS-3 đến BS-74 cùng các commit Fix bug thực tế xen kẽ).

---

## BẢNG TỔNG HỢP TỈ LỆ ĐÓNG GÓP THEO THÀNH VIÊN

| Thành viên | Vai trò chính | Số commit | Tỉ lệ (%) |
| :--- | :--- | :--- | :--- |
| **DuyDev98** | Express setup, Database MySQL, JWT Auth, API Catalog, Sách, Giỏ hàng, Fix logic Backend | 40 | **50%** |
| **duy0609** | Layout Admin & Client, UI Dashboard, UI Catalog, UI Sách, UI Giỏ hàng, Search UI, Responsive | 24 | **30%** |
| **phamhoang0811** | Code style, Seed data, Validation form, Empty states, Toast, Fix bug UI/Logic | 16 | **20%** |
| **TỔNG CỘNG** | | **80** | **100%** |

---

## CHI TIẾT 80 COMMITS THEO THỜI GIAN (15/08 - 07/09/2026)

### 📅 TUẦN 1: KHỞI TẠO NỀN TẢNG, CSDL & LAYOUT CƠ BẢN (15/08 - 20/08/2026)

#### Ngày 15/08/2026 (Setup Backend Express & Code Style)
1. `2026-08-15 09:15:00` | **DuyDev98** | `chore: [BS-4] Khoi tao cau truc thu muc Express (controllers/models/routes/middlewares/configs)`
2. `2026-08-15 11:30:00` | **DuyDev98** | `chore: [BS-5] Cau hinh bien moi truong .env va dotenv loader`
3. `2026-08-15 14:45:00` | **DuyDev98** | `feat: [BS-6] Tich hop body-parser, cors va morgan logger cho Express server`
4. `2026-08-15 16:20:00` | **phamhoang0811** | `docs: [BS-3] Thiet lap tai lieu quy chuan ma nguon va code style guide`

#### Ngày 16/08/2026 (API Response Format & CSDL MySQL)
5. `2026-08-16 09:30:00` | **DuyDev98** | `feat: [BS-7] Xay dung Standard API Response Format (success/error handling)`
6. `2026-08-16 11:15:00` | **DuyDev98** | `feat: [BS-8] Tao Database Schema MySQL va cau hinh ket noi mysql2 connection pool`
7. `2026-08-16 15:00:00` | **phamhoang0811** | `feat: [BS-9] Khoi tao bang danhmuc, loaisach, nhaxuatban va viet SQL seed data`
8. `2026-08-16 17:15:00` | **DuyDev98** | `fix: [BS-8] Sua loi connection pool bi timeout khi truy van database`

#### Ngày 17/08/2026 (Khởi tạo Frontend & Base Layout Admin)
9. `2026-08-17 09:45:00` | **duy0609** | `chore: [BS-10] Khoi tao template giao dien Frontend va assets tinh (CSS/JS)`
10. `2026-08-17 14:20:00` | **duy0609** | `feat: [BS-12] Thiet ke Sidebar Admin dieu huong (Quan ly sach, Don hang, Tai khoan)`
11. `2026-08-17 16:50:00` | **duy0609** | `feat: [BS-13] Thiet ke Header Admin hien thi avatar va dropdown logout`

#### Ngày 18/08/2026 (Admin Navigation & Dashboard Overview)
12. `2026-08-18 10:30:00` | **duy0609** | `feat: [BS-14] Tich hop Breadcrumbs va routing chuyen tab cho Admin`
13. `2026-08-18 14:45:00` | **duy0609** | `feat: [BS-15] Xay dung giao dien Dashboard Admin tong quan thong ke`
14. `2026-08-18 17:10:00` | **phamhoang0811** | `fix: [BS-12] Sua loi responsive sidebar bi de man hinh tren tablet`

#### Ngày 19/08/2026 (Client Layout: Header & Footer)
15. `2026-08-19 09:30:00` | **duy0609** | `feat: [BS-17] Thiet ke Header Client gom Logo, Search bar va Menu dieu huong`
16. `2026-08-19 14:15:00` | **duy0609** | `feat: [BS-18] Thiet ke Footer Client chua thong tin lien he, ban quyen`
17. `2026-08-19 16:30:00` | **duy0609** | `style: [BS-17] Chinh sua lai kich thuoc logo va padding tren header`

#### Ngày 20/08/2026 (Client Homepage & Responsive)
18. `2026-08-20 10:00:00` | **duy0609** | `feat: [BS-19] Xay dung Homepage giao dien Banner Slider va Featured Products`
19. `2026-08-20 14:30:00` | **duy0609** | `feat: [BS-20] Xu ly Responsive cho Mobile voi Hamburger Menu`
20. `2026-08-20 16:45:00` | **duy0609** | `feat: [BS-21] Cau hinh Routing Client (/products, /cart, /login)`
21. `2026-08-20 18:00:00` | **phamhoang0811** | `fix: [BS-20] Fix loi vo layout slider banner tren man hinh nho`

---

### 📅 TUẦN 2: HỆ THỐNG XÁC THỰC, AUTH UI & PROFILE (21/08 - 27/08/2026)

#### Ngày 21/08/2026 (Authentication: CSDL User & JWT Middleware)
22. `2026-08-21 09:15:00` | **DuyDev98** | `feat: [BS-23] Tao bang user (username, password_hash, role_id) trong CSDL`
23. `2026-08-21 14:00:00` | **DuyDev98** | `feat: [BS-24] Viet Middleware verifyToken kiem tra JWT token`
24. `2026-08-21 16:45:00` | **DuyDev98** | `fix: [BS-24] Xu ly truong hop thieu chuoi Bearer trong Authorization header`

#### Ngày 22/08/2026 (Auth APIs: Register & Login)
25. `2026-08-22 10:15:00` | **DuyDev98** | `feat: [BS-27] Xay dung API /auth/register ma hoa password bang bcrypt`
26. `2026-08-22 14:30:00` | **DuyDev98** | `feat: [BS-26] Xay dung API /auth/login so sanh hash va tra ve JWT token`
27. `2026-08-22 17:00:00` | **DuyDev98** | `fix: [BS-27] Them validation check trung lap email/username khi register`

#### Ngày 23/08/2026 (Auth APIs: Current User & Permissions)
28. `2026-08-23 09:30:00` | **DuyDev98** | `feat: [BS-28] Xay dung API /auth/me lay thong tin user tu token`
29. `2026-08-23 14:15:00` | **DuyDev98** | `feat: [BS-29] Tao bang roles va viet Middleware checkPermission phan quyen`
30. `2026-08-23 16:45:00` | **phamhoang0811** | `fix: [BS-28] Sua loi tra ve password_hash trong response API auth/me`

#### Ngày 24/08/2026 (Giao diện Form Đăng nhập / Đăng ký)
31. `2026-08-24 10:00:00` | **duy0609** | `feat: [BS-30] Xay dung Login Form voi giao dien modal va form input`
32. `2026-08-24 14:30:00` | **phamhoang0811** | `feat: [BS-30] Viet ham validate email va do dai password cho Login Form`
33. `2026-08-24 16:45:00` | **duy0609** | `feat: [BS-31] Xay dung Register Form va form xac nhan mat khau`

#### Ngày 25/08/2026 (Xử lý Lưu trữ Token & Tinh chỉnh Auth)
34. `2026-08-25 09:45:00` | **DuyDev98** | `feat: [BS-32] Xu ly luu token vao localStorage va tu dong attach vao Request`
35. `2026-08-25 14:15:00` | **phamhoang0811** | `fix: [BS-31] Fix loi khong hien thi thong bao loi khi mat khau xac nhan khong khop`
36. `2026-08-25 16:30:00` | **duy0609** | `style: [BS-30] Toi uu hieu ung chuyen doi giua tab Dang nhap va Dang ky`

#### Ngày 26/08/2026 (Quản lý Khách hàng Backend)
37. `2026-08-26 10:00:00` | **DuyDev98** | `feat: [BS-36] Xay dung API CRUD cho bang khachhang lien ket user`
38. `2026-08-26 14:45:00` | **DuyDev98** | `refactor: [BS-36] Toi uu query join bang khachhang va user`

#### Ngày 27/08/2026 (Profile Dashboard & Edit Profile)
39. `2026-08-27 09:30:00` | **duy0609** | `feat: [BS-37] Xay dung Profile Dashboard hien thi thong tin ca nhan`
40. `2026-08-27 14:00:00` | **duy0609** | `feat: [BS-38] Xay dung Form Edit Profile cap nhat FullName, Address, Phone`
41. `2026-08-27 16:45:00` | **phamhoang0811** | `fix: [BS-38] Validate so dien thoai phai du 10 chu so o form edit profile`

---

### 📅 TUẦN 3: SPRINT 2 - DANH MỤC PHỤ TRỢ & KHO SÁCH (28/08 - 02/09/2026)

#### Ngày 28/08/2026 (APIs Danh mục & Loại sách)
42. `2026-08-28 09:15:00` | **DuyDev98** | `feat: [BS-40] Xay dung API CRUD cho bang danhmuc`
43. `2026-08-28 14:00:00` | **DuyDev98** | `feat: [BS-41] Xay dung API CRUD cho bang loaisach`
44. `2026-08-28 16:30:00` | **DuyDev98** | `fix: [BS-40] Fix loi Foreign Key khi xoa danh muc con trong danhmuc`

#### Ngày 29/08/2026 (APIs Nhà xuất bản & Tác giả)
45. `2026-08-29 10:00:00` | **DuyDev98** | `feat: [BS-42] Xay dung API CRUD cho bang nhaxuatban`
46. `2026-08-29 14:30:00` | **DuyDev98** | `feat: [BS-43] Xay dung API CRUD cho bang tacgia`
47. `2026-08-29 17:00:00` | **phamhoang0811** | `test: [BS-42] Viet Postman test cases kiem tra them NXB va Tac gia`

#### Ngày 30/08/2026 (UI Quản trị Danh mục Phụ trợ - Admin)
48. `2026-08-30 09:30:00` | **duy0609** | `feat: [BS-44] Xay dung UI Danh sach + Them/Sua/Xoa danh muc (Admin)`
49. `2026-08-30 14:15:00` | **duy0609** | `feat: [BS-45] Xay dung UI Danh sach + Them/Sua/Xoa loai sach (Admin)`
50. `2026-08-30 16:45:00` | **duy0609** | `feat: [BS-46] Xay dung UI Danh sach + Them/Sua/Xoa nha xuat ban`

#### Ngày 31/08/2026 (Kho sách: APIs Lấy sách & Chi tiết)
51. `2026-08-31 09:15:00` | **DuyDev98** | `feat: [BS-48] Xay dung API GET /api/books ho tro phan trang va filter danh muc`
52. `2026-08-31 14:00:00` | **DuyDev98** | `feat: [BS-49] Xay dung API GET /api/books/:id chi tiet sach kem join tac gia va NXB`
53. `2026-08-31 16:30:00` | **DuyDev98** | `fix: [BS-48] Fix loi tinh toan totalPages bi lam tron sai o API get books`

#### Ngày 01/09/2026 (Kho sách: Thêm/Sửa/Xóa & Upload Cloudinary)
54. `2026-09-01 09:30:00` | **DuyDev98** | `feat: [BS-50] Xay dung API POST /api/books them sach va upload anh Cloudinary`
55. `2026-09-01 14:00:00` | **DuyDev98** | `feat: [BS-51] Xay dung API PUT /api/books/:id cap nhat sach va gia sale`
56. `2026-09-01 16:15:00` | **DuyDev98** | `feat: [BS-52] Xay dung API DELETE /api/books/:id ho tro xoa mem/an sach`
57. `2026-09-01 17:45:00` | **DuyDev98** | `fix: [BS-50] Fix loi Multer khong nhan file dinh dang .webp khi upload anh`

#### Ngày 02/09/2026 (UI Quản lý Sách Admin)
58. `2026-09-02 10:00:00` | **duy0609** | `feat: [BS-53] Xay dung UI Table danh sach sach (Admin) kem tim kiem`
59. `2026-09-02 14:30:00` | **duy0609** | `feat: [BS-54] Xay dung UI Form Them/Sua sach voi upload preview va dropdown danh muc`
60. `2026-09-02 17:00:00` | **phamhoang0811** | `fix: [BS-54] Them validate gia sach va so luong ton phai lon hon 0`

---

### 📅 TUẦN 4: SPRINT 2 - UI HIỂN THỊ, GIỎ HÀNG & TÌM KIẾM (03/09 - 07/09/2026)

#### Ngày 03/09/2026 (UI Sách Khách hàng: Listing & Detail)
61. `2026-09-03 09:30:00` | **duy0609** | `feat: [BS-55] Xay dung UI Product Listing (Client) - Grid hien thi sach`
62. `2026-09-03 14:00:00` | **duy0609** | `feat: [BS-56] Xay dung UI Product Detail (Client) hien thi thong tin chi tiet`
63. `2026-09-03 16:30:00` | **duy0609** | `feat: [BS-57] Xay dung UI Filter & Sort sap xep theo gia, danh muc va phan trang`

#### Ngày 04/09/2026 (Hệ thống Giỏ hàng Backend APIs)
64. `2026-09-04 09:15:00` | **DuyDev98** | `feat: [BS-60] Xay dung API POST /api/cart/add them sach vao gio`
65. `2026-09-04 11:30:00` | **DuyDev98** | `feat: [BS-61] Xay dung API GET /api/cart lay danh sach gio hang`
66. `2026-09-04 14:00:00` | **DuyDev98** | `feat: [BS-62] Xay dung API PUT /api/cart/update cap nhat so luong`
67. `2026-09-04 15:45:00` | **DuyDev98** | `feat: [BS-63] Xay dung API DELETE /api/cart/remove xoa item khoi gio`
68. `2026-09-04 17:15:00` | **DuyDev98** | `feat: [BS-64] Xay dung API DELETE /api/cart/clear xoa toan bo gio hang`
69. `2026-09-04 18:30:00` | **DuyDev98** | `fix: [BS-60] Fix loi khong cong don so luong khi them cung mot ma sach vao gio`

#### Ngày 05/09/2026 (UI Giỏ hàng & Xử lý Client)
70. `2026-09-05 09:30:00` | **duy0609** | `feat: [BS-65] Xay dung UI Cart Page hien thi danh sach gio hang va nut tang/giam`
71. `2026-09-05 13:45:00` | **duy0609** | `feat: [BS-66] Xay dung UI tinh toan tam tinh (subtotal) va tong tien gio hang`
72. `2026-09-05 15:30:00` | **duy0609** | `feat: [BS-67] Tich hop UI Cart Icon hien thi so luong item tren Header`
73. `2026-09-05 17:15:00` | **phamhoang0811** | `feat: [BS-68] Xu ly nghiep vu them vao gio (kiem tra login session va Toast thong bao)`
74. `2026-09-05 18:45:00` | **phamhoang0811** | `fix: [BS-66] Fix loi tinh toan tong tien gio hang bi sai dinh dang so thap phan`

#### Ngày 06/09/2026 (Tìm kiếm & Bộ lọc nâng cao)
75. `2026-09-06 09:15:00` | **DuyDev98** | `feat: [BS-69] Xay dung API tim kiem sach theo tieu de va tac gia`
76. `2026-09-06 11:30:00` | **DuyDev98** | `feat: [BS-71] Xay dung API GET /api/books/filter loc sach theo gia va danh muc`
77. `2026-09-06 14:00:00` | **duy0609** | `feat: [BS-72] Xay dung UI Search Result Page hien thi ket qua tim kiem sach`
78. `2026-09-06 16:30:00` | **phamhoang0811** | `feat: [BS-73] Xay dung UI Empty State khi khong tim thay ket qua sach hop le`

#### Ngày 07/09/2026 (Hôm nay: Search Bar Header & Hoàn thiện)
79. `2026-09-07 10:00:00` | **duy0609** | `feat: [BS-74] Ket noi Search bar tren Header voi API tim kiem ho tro live suggestion`
80. `2026-09-07 14:30:00` | **phamhoang0811** | `fix: [BS-74] Fix loi search bar khong tu dong dong dropdown khi click ra ngoai`
