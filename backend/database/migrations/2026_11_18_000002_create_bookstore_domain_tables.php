<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. khachhang
        Schema::create('khachhang', function (Blueprint $table) {
            $table->increments('MaKH');
            $table->string('Username', 50)->unique();
            $table->string('HoTen', 100);
            $table->string('DiaChi', 255)->nullable();
            $table->string('SDienThoai', 15)->nullable();
            $table->string('Email', 100)->nullable();
            $table->timestamps();

            $table->foreign('Username')->references('Username')->on('user')->onUpdate('cascade')->onDelete('cascade');
        });

        // 2. danhmuc (phân cấp cha-con)
        Schema::create('danhmuc', function (Blueprint $table) {
            $table->increments('MaDanhMuc');
            $table->string('TenDanhMuc', 100);
            $table->unsignedInteger('ParentID')->nullable();
            $table->timestamps();

            $table->foreign('ParentID')->references('MaDanhMuc')->on('danhmuc')->onUpdate('cascade')->onDelete('set null');
        });

        // 3. tacgia
        Schema::create('tacgia', function (Blueprint $table) {
            $table->increments('MaTG');
            $table->string('TenTG', 100);
            $table->integer('NamSinh')->nullable();
            $table->string('QueQuan', 100)->nullable();
            $table->integer('NamMat')->nullable();
            $table->timestamps();
        });

        // 4. nhaxuatban
        Schema::create('nhaxuatban', function (Blueprint $table) {
            $table->increments('MaNXB');
            $table->string('TenNXB', 100);
            $table->integer('NamThanhLap')->nullable();
            $table->string('DiaChi', 100)->nullable();
            $table->timestamps();
        });

        // 5. loaisach
        Schema::create('loaisach', function (Blueprint $table) {
            $table->increments('MaLoaiSach');
            $table->string('TenLoaiSach', 100);
            $table->timestamps();
        });

        // 6. sach
        Schema::create('sach', function (Blueprint $table) {
            $table->increments('MaSach');
            $table->string('TenSach', 200);
            $table->string('AnhBia', 255)->nullable();
            $table->decimal('GiaBan', 10, 2)->default(0);
            $table->integer('SoLuongTon')->default(0);
            $table->integer('PhanTramGiamGia')->default(0);
            $table->decimal('GiaSale', 10, 2)->nullable();
            $table->boolean('IsSale')->default(false);
            $table->integer('NamXuatBan')->nullable();
            $table->integer('LanTaiBan')->nullable();
            $table->text('MoTa')->nullable();
            $table->unsignedInteger('MaTG')->nullable();
            $table->unsignedInteger('MaNXB')->nullable();
            $table->unsignedInteger('MaLoaiSach')->nullable();
            $table->unsignedInteger('MaDanhMuc')->nullable();
            $table->timestamps();

            $table->foreign('MaTG')->references('MaTG')->on('tacgia')->onUpdate('cascade')->onDelete('set null');
            $table->foreign('MaNXB')->references('MaNXB')->on('nhaxuatban')->onUpdate('cascade')->onDelete('set null');
            $table->foreign('MaLoaiSach')->references('MaLoaiSach')->on('loaisach')->onUpdate('cascade')->onDelete('set null');
            $table->foreign('MaDanhMuc')->references('MaDanhMuc')->on('danhmuc')->onUpdate('cascade')->onDelete('set null');
        });

        // 7. giohang
        Schema::create('giohang', function (Blueprint $table) {
            $table->increments('MaGioHang');
            $table->unsignedInteger('MaKH');
            $table->dateTime('NgayTao')->useCurrent();
            $table->timestamps();

            $table->foreign('MaKH')->references('MaKH')->on('khachhang')->onUpdate('cascade')->onDelete('cascade');
        });

        // 8. chitietgiohang
        Schema::create('chitietgiohang', function (Blueprint $table) {
            $table->unsignedInteger('MaGioHang');
            $table->unsignedInteger('MaSach');
            $table->integer('SoLuong')->default(1);
            $table->timestamps();

            $table->primary(['MaGioHang', 'MaSach']);
            $table->foreign('MaGioHang')->references('MaGioHang')->on('giohang')->onDelete('cascade');
            $table->foreign('MaSach')->references('MaSach')->on('sach')->onDelete('cascade');
        });

        // 9. donhang
        Schema::create('donhang', function (Blueprint $table) {
            $table->increments('MaDH');
            $table->unsignedInteger('MaKH');
            $table->dateTime('NgayDat')->useCurrent();
            $table->decimal('TongTien', 12, 2)->default(0);
            $table->decimal('PhiVanChuyen', 10, 2)->default(0);
            $table->string('DiaChiGiaoHang', 255);
            $table->text('GhiChuGiaoHang')->nullable();
            $table->string('TrangThai', 50)->default('ChoDuyet');
            $table->timestamps();

            $table->foreign('MaKH')->references('MaKH')->on('khachhang')->onUpdate('cascade')->onDelete('cascade');
        });

        // 10. chitietdonhang
        Schema::create('chitietdonhang', function (Blueprint $table) {
            $table->unsignedInteger('MaDH');
            $table->unsignedInteger('MaSach');
            $table->integer('SoLuong')->default(1);
            $table->decimal('DonGia', 10, 2);
            $table->timestamps();

            $table->primary(['MaDH', 'MaSach']);
            $table->foreign('MaDH')->references('MaDH')->on('donhang')->onDelete('cascade');
            $table->foreign('MaSach')->references('MaSach')->on('sach')->onDelete('restrict');
        });

        // 11. binhluan
        Schema::create('binhluan', function (Blueprint $table) {
            $table->increments('MaBL');
            $table->unsignedInteger('MaKH');
            $table->unsignedInteger('MaSach');
            $table->text('NoiDung');
            $table->dateTime('NgayBinhLuan')->useCurrent();
            $table->string('TrangThai', 20)->default('pending');
            $table->timestamps();

            $table->foreign('MaKH')->references('MaKH')->on('khachhang')->onDelete('cascade');
            $table->foreign('MaSach')->references('MaSach')->on('sach')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('binhluan');
        Schema::dropIfExists('chitietdonhang');
        Schema::dropIfExists('donhang');
        Schema::dropIfExists('chitietgiohang');
        Schema::dropIfExists('giohang');
        Schema::dropIfExists('sach');
        Schema::dropIfExists('loaisach');
        Schema::dropIfExists('nhaxuatban');
        Schema::dropIfExists('tacgia');
        Schema::dropIfExists('danhmuc');
        Schema::dropIfExists('khachhang');
    }
};
