<?php

namespace Database\Seeders;

use App\Models\BinhLuan;
use App\Models\ChiTietDonHang;
use App\Models\DanhMuc;
use App\Models\DonHang;
use App\Models\KhachHang;
use App\Models\LoaiSach;
use App\Models\NhaXuatBan;
use App\Models\Sach;
use App\Models\TacGia;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. TÀI KHOẢN & KHÁCH HÀNG
        $admin = User::create([
            'Username' => 'admin',
            'PassWord' => Hash::make('123456'),
            'VaiTro' => 'admin',
        ]);

        $user1 = User::create([
            'Username' => 'khachhang1',
            'PassWord' => Hash::make('123456'),
            'VaiTro' => 'customer',
        ]);
        $kh1 = KhachHang::create([
            'Username' => 'khachhang1',
            'HoTen' => 'Nguyễn Văn An',
            'DiaChi' => 'Số 123 Đường Cầu Giấy, Hà Nội',
            'SDienThoai' => '0912345678',
            'Email' => 'an.nguyen@example.com',
        ]);

        $user2 = User::create([
            'Username' => 'khachhang2',
            'PassWord' => Hash::make('123456'),
            'VaiTro' => 'customer',
        ]);
        $kh2 = KhachHang::create([
            'Username' => 'khachhang2',
            'HoTen' => 'Trần Thị Bình',
            'DiaChi' => 'Số 456 Nguyễn Huệ, Quận 1, TP.HCM',
            'SDienThoai' => '0987654321',
            'Email' => 'binh.tran@example.com',
        ]);

        // 2. DANH MỤC (Đa cấp)
        $dmVanHoc = DanhMuc::create(['TenDanhMuc' => 'Văn Học', 'ParentID' => null]);
        $dmKinhTe = DanhMuc::create(['TenDanhMuc' => 'Kinh Tế & Quản Trị', 'ParentID' => null]);
        $dmCNTT = DanhMuc::create(['TenDanhMuc' => 'Công Nghệ Thông Tin', 'ParentID' => null]);
        $dmKyNang = DanhMuc::create(['TenDanhMuc' => 'Kỹ Năng Sống', 'ParentID' => null]);

        $dmTieuThuyet = DanhMuc::create(['TenDanhMuc' => 'Tiểu Thuyết', 'ParentID' => $dmVanHoc->MaDanhMuc]);
        $dmTruyenNgan = DanhMuc::create(['TenDanhMuc' => 'Truyện Ngắn - Tản Văn', 'ParentID' => $dmVanHoc->MaDanhMuc]);
        $dmKhoiNghiep = DanhMuc::create(['TenDanhMuc' => 'Khởi Nghiệp - Làm Giàu', 'ParentID' => $dmKinhTe->MaDanhMuc]);
        $dmLapTrinh = DanhMuc::create(['TenDanhMuc' => 'Lập Trình Phần Mềm', 'ParentID' => $dmCNTT->MaDanhMuc]);

        // 3. TÁC GIẢ
        $tg1 = TacGia::create(['TenTG' => 'Nguyễn Nhật Ánh', 'NamSinh' => 1955, 'QueQuan' => 'Quảng Nam']);
        $tg2 = TacGia::create(['TenTG' => 'Dale Carnegie', 'NamSinh' => 1888, 'QueQuan' => 'Hoa Kỳ', 'NamMat' => 1955]);
        $tg3 = TacGia::create(['TenTG' => 'Robert C. Martin', 'NamSinh' => 1952, 'QueQuan' => 'Hoa Kỳ']);
        $tg4 = TacGia::create(['TenTG' => 'Paulo Coelho', 'NamSinh' => 1947, 'QueQuan' => 'Brazil']);
        $tg5 = TacGia::create(['TenTG' => 'Robert Kiyosaki', 'NamSinh' => 1947, 'QueQuan' => 'Hoa Kỳ']);

        // 4. NHÀ XUẤT BẢN
        $nxb1 = NhaXuatBan::create(['TenNXB' => 'NXB Trẻ', 'NamThanhLap' => 1981, 'DiaChi' => '161B Lý Chính Thắng, Q.3, TP.HCM']);
        $nxb2 = NhaXuatBan::create(['TenNXB' => 'NXB Kim Đồng', 'NamThanhLap' => 1957, 'DiaChi' => '55 Quang Trung, Hà Nội']);
        $nxb3 = NhaXuatBan::create(['TenNXB' => 'NXB Tổng Hợp TP.HCM', 'NamThanhLap' => 1977, 'DiaChi' => '62 Nguyễn Thị Minh Khai, Q.1, TP.HCM']);
        $nxb4 = NhaXuatBan::create(['TenNXB' => 'NXB Lao Động', 'NamThanhLap' => 1945, 'DiaChi' => '175 Giảng Võ, Hà Nội']);

        // 5. LOẠI SÁCH
        $loai1 = LoaiSach::create(['TenLoaiSach' => 'Sách Bìa Mềm']);
        $loai2 = LoaiSach::create(['TenLoaiSach' => 'Sách Bìa Cứng Cao Cấp']);
        $loai3 = LoaiSach::create(['TenLoaiSach' => 'Tái Bản Bổ Sung']);

        // 6. SÁCH
        $booksData = [
            [
                'TenSach' => 'Cho Tôi Xin Một Vé Đi Tuổi Thơ',
                'AnhBia' => 'https://images.unsplash.com/photo-1544716278-ca5e3f4abd8c?w=500&q=80',
                'GiaBan' => 90000,
                'SoLuongTon' => 45,
                'PhanTramGiamGia' => 20,
                'NamXuatBan' => 2022,
                'LanTaiBan' => 5,
                'MoTa' => 'Cuốn sách đưa người đọc trở về những năm tháng ấu thơ trong trẻo, hồn nhiên đầy ắp tiếng cười và những bài học cuộc đời sâu sắc.',
                'MaTG' => $tg1->MaTG,
                'MaNXB' => $nxb1->MaNXB,
                'MaLoaiSach' => $loai1->MaLoaiSach,
                'MaDanhMuc' => $dmTruyenNgan->MaDanhMuc,
            ],
            [
                'TenSach' => 'Mắt Biếc',
                'AnhBia' => 'https://images.unsplash.com/photo-1512820790803-83ca734da794?w=500&q=80',
                'GiaBan' => 110000,
                'SoLuongTon' => 30,
                'PhanTramGiamGia' => 15,
                'NamXuatBan' => 2021,
                'LanTaiBan' => 8,
                'MoTa' => 'Một trong những tác phẩm tiêu biểu và xúc động nhất của nhà văn Nguyễn Nhật Ánh về mối tình đơn phương khắc khoải của Ngạn dành cho Hà Lan.',
                'MaTG' => $tg1->MaTG,
                'MaNXB' => $nxb1->MaNXB,
                'MaLoaiSach' => $loai1->MaLoaiSach,
                'MaDanhMuc' => $dmTieuThuyet->MaDanhMuc,
            ],
            [
                'TenSach' => 'Đắc Nhân Tâm (How to Win Friends and Influence People)',
                'AnhBia' => 'https://images.unsplash.com/photo-1589829085413-56de8ae18c73?w=500&q=80',
                'GiaBan' => 88000,
                'SoLuongTon' => 100,
                'PhanTramGiamGia' => 25,
                'NamXuatBan' => 2023,
                'LanTaiBan' => 20,
                'MoTa' => 'Cuốn sách nghệ thuật thu phục lòng người kinh điển nhất mọi thời đại, mang lại những nguyên tắc vàng trong ứng xử và phát triển bản thân.',
                'MaTG' => $tg2->MaTG,
                'MaNXB' => $nxb3->MaNXB,
                'MaLoaiSach' => $loai1->MaLoaiSach,
                'MaDanhMuc' => $dmKyNang->MaDanhMuc,
            ],
            [
                'TenSach' => 'Clean Code: A Handbook of Agile Software Craftsmanship',
                'AnhBia' => 'https://images.unsplash.com/photo-1532012164546-f432f2e3dd48?w=500&q=80',
                'GiaBan' => 320000,
                'SoLuongTon' => 20,
                'PhanTramGiamGia' => 10,
                'NamXuatBan' => 2020,
                'LanTaiBan' => 3,
                'MoTa' => 'Cẩm nang gối đầu giường của mọi kỹ sư phần mềm về nghệ thuật viết mã nguồn sạch, dễ đọc, dễ bảo trì và mở rộng.',
                'MaTG' => $tg3->MaTG,
                'MaNXB' => $nxb4->MaNXB,
                'MaLoaiSach' => $loai2->MaLoaiSach,
                'MaDanhMuc' => $dmLapTrinh->MaDanhMuc,
            ],
            [
                'TenSach' => 'Nhà Giả Kim (The Alchemist)',
                'AnhBia' => 'https://images.unsplash.com/photo-1497633762265-9d179a990aa6?w=500&q=80',
                'GiaBan' => 79000,
                'SoLuongTon' => 8, // Thấp để test cảnh báo hết hàng < 10
                'PhanTramGiamGia' => 0,
                'NamXuatBan' => 2023,
                'LanTaiBan' => 15,
                'MoTa' => 'Câu chuyện huyền bí và triết lý về hành trình theo đuổi ước mơ và lắng nghe tiếng nói của trái tim của chàng chăn cừu Santiago.',
                'MaTG' => $tg4->MaTG,
                'MaNXB' => $nxb1->MaNXB,
                'MaLoaiSach' => $loai1->MaLoaiSach,
                'MaDanhMuc' => $dmTieuThuyet->MaDanhMuc,
            ],
            [
                'TenSach' => 'Cha Giàu Cha Nghèo (Rich Dad Poor Dad)',
                'AnhBia' => 'https://images.unsplash.com/photo-1592496431122-2349e0fbc666?w=500&q=80',
                'GiaBan' => 125000,
                'SoLuongTon' => 50,
                'PhanTramGiamGia' => 30,
                'NamXuatBan' => 2022,
                'LanTaiBan' => 12,
                'MoTa' => 'Thay đổi tư duy tài chính, phân biệt tài sản và tiêu sản, con đường dẫn tới sự tự do tài chính bền vững.',
                'MaTG' => $tg5->MaTG,
                'MaNXB' => $nxb3->MaNXB,
                'MaLoaiSach' => $loai1->MaLoaiSach,
                'MaDanhMuc' => $dmKhoiNghiep->MaDanhMuc,
            ],
            [
                'TenSach' => 'Lập Trình Web Hiện Đại Với PHP & Laravel',
                'AnhBia' => 'https://images.unsplash.com/photo-1516259762381-22954d7d3ad2?w=500&q=80',
                'GiaBan' => 210000,
                'SoLuongTon' => 5, // Thấp để test cảnh báo hết hàng
                'PhanTramGiamGia' => 10,
                'NamXuatBan' => 2024,
                'LanTaiBan' => 1,
                'MoTa' => 'Hướng dẫn thực chiến xây dựng hệ thống RESTful API, ORM Eloquent, MVC và các kiến trúc web hiện đại với Laravel.',
                'MaTG' => $tg3->MaTG,
                'MaNXB' => $nxb4->MaNXB,
                'MaLoaiSach' => $loai2->MaLoaiSach,
                'MaDanhMuc' => $dmLapTrinh->MaDanhMuc,
            ],
        ];

        $createdBooks = [];
        foreach ($booksData as $bData) {
            $book = new Sach($bData);
            $book->tinhGiaSale();
            $book->save();
            $createdBooks[] = $book;
        }

        // 7. BÌNH LUẬN MẪU
        BinhLuan::create([
            'MaKH' => $kh1->MaKH,
            'MaSach' => $createdBooks[0]->MaSach,
            'NoiDung' => 'Sách rất hay và cảm động, đọc xong như được trở về tuổi thơ!',
            'NgayBinhLuan' => Carbon::now()->subDays(2),
            'TrangThai' => 'approved',
        ]);

        BinhLuan::create([
            'MaKH' => $kh2->MaKH,
            'MaSach' => $createdBooks[2]->MaSach,
            'NoiDung' => 'Cuốn sách ai cũng nên đọc ít nhất một lần trong đời. Giao hàng rất nhanh!',
            'NgayBinhLuan' => Carbon::now()->subDay(),
            'TrangThai' => 'approved',
        ]);

        BinhLuan::create([
            'MaKH' => $kh1->MaKH,
            'MaSach' => $createdBooks[3]->MaSach,
            'NoiDung' => 'Sách Clean Code in đẹp, nội dung lập trình rất bổ ích cho sinh viên IT.',
            'NgayBinhLuan' => Carbon::now(),
            'TrangThai' => 'pending', // Chờ duyệt
        ]);

        // 8. ĐƠN HÀNG MẪU (Để Dashboard có doanh thu & biểu đồ 7 ngày hiển thị ngay)
        // Đơn 1: Đã giao 2 ngày trước (Được tính vào doanh thu)
        $don1 = DonHang::create([
            'MaKH' => $kh1->MaKH,
            'NgayDat' => Carbon::now()->subDays(2),
            'TongTien' => 174000,
            'PhiVanChuyen' => 30000,
            'DiaChiGiaoHang' => 'Số 123 Đường Cầu Giấy, Hà Nội',
            'GhiChuGiaoHang' => 'Giao trong giờ hành chính',
            'TrangThai' => 'Đã giao',
        ]);
        ChiTietDonHang::create([
            'MaDH' => $don1->MaDH,
            'MaSach' => $createdBooks[0]->MaSach,
            'SoLuong' => 2,
            'DonGia' => 72000, // Giá sale 90k - 20%
        ]);

        // Đơn 2: Đã giao hôm nay (Được tính vào doanh thu)
        $don2 = DonHang::create([
            'MaKH' => $kh2->MaKH,
            'NgayDat' => Carbon::now(),
            'TongTien' => 318000,
            'PhiVanChuyen' => 30000,
            'DiaChiGiaoHang' => 'Số 456 Nguyễn Huệ, Quận 1, TP.HCM',
            'GhiChuGiaoHang' => 'Gọi trước khi giao',
            'TrangThai' => 'Đã giao',
        ]);
        ChiTietDonHang::create([
            'MaDH' => $don2->MaDH,
            'MaSach' => $createdBooks[3]->MaSach,
            'SoLuong' => 1,
            'DonGia' => 288000,
        ]);

        // Đơn 3: Đang chờ duyệt (ChoDuyet - chưa tính vào doanh thu)
        $don3 = DonHang::create([
            'MaKH' => $kh1->MaKH,
            'NgayDat' => Carbon::now(),
            'TongTien' => 117500,
            'PhiVanChuyen' => 30000,
            'DiaChiGiaoHang' => 'Số 123 Đường Cầu Giấy, Hà Nội',
            'GhiChuGiaoHang' => 'Giao hàng cẩn thận giúp mình nhé',
            'TrangThai' => 'ChoDuyet',
        ]);
        ChiTietDonHang::create([
            'MaDH' => $don3->MaDH,
            'MaSach' => $createdBooks[5]->MaSach,
            'SoLuong' => 1,
            'DonGia' => 87500,
        ]);
    }
}
