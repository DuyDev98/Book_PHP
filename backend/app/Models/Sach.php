<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sach extends Model
{
    use HasFactory;

    protected $table = 'sach';
    protected $primaryKey = 'MaSach';

    protected $fillable = [
        'TenSach',
        'AnhBia',
        'GiaBan',
        'SoLuongTon',
        'PhanTramGiamGia',
        'GiaSale',
        'IsSale',
        'NamXuatBan',
        'LanTaiBan',
        'MoTa',
        'MaTG',
        'MaNXB',
        'MaLoaiSach',
        'MaDanhMuc',
    ];

    protected $casts = [
        'GiaBan' => 'decimal:2',
        'GiaSale' => 'decimal:2',
        'IsSale' => 'boolean',
        'SoLuongTon' => 'integer',
        'PhanTramGiamGia' => 'integer',
    ];

    public function tacGia()
    {
        return $this->belongsTo(TacGia::class, 'MaTG', 'MaTG');
    }

    public function nhaXuatBan()
    {
        return $this->belongsTo(NhaXuatBan::class, 'MaNXB', 'MaNXB');
    }

    public function loaiSach()
    {
        return $this->belongsTo(LoaiSach::class, 'MaLoaiSach', 'MaLoaiSach');
    }

    public function danhMuc()
    {
        return $this->belongsTo(DanhMuc::class, 'MaDanhMuc', 'MaDanhMuc');
    }

    public function binhLuans()
    {
        return $this->hasMany(BinhLuan::class, 'MaSach', 'MaSach');
    }

    public function chiTietDonHangs()
    {
        return $this->hasMany(ChiTietDonHang::class, 'MaSach', 'MaSach');
    }

    /**
     * Tự động tính giá Sale dựa theo phần trăm giảm giá
     */
    public function tinhGiaSale(): float
    {
        if ($this->PhanTramGiamGia > 0) {
            $giaSale = $this->GiaBan * (100 - $this->PhanTramGiamGia) / 100;
            $this->GiaSale = round($giaSale, 2);
            $this->IsSale = true;
        } else {
            $this->GiaSale = null;
            $this->IsSale = false;
        }
        return (float) ($this->GiaSale ?? $this->GiaBan);
    }

    /**
     * Lấy giá hiện hành của sách (Giá Sale nếu đang khuyến mãi, hoặc Giá Gốc)
     */
    public function getGiaHienHanhAttribute(): float
    {
        return ($this->IsSale && $this->GiaSale !== null) ? (float) $this->GiaSale : (float) $this->GiaBan;
    }

    /**
     * Lấy URL ảnh bìa hiển thị đầy đủ
     */
    public function getAnhBiaUrlAttribute(): string
    {
        if (empty($this->AnhBia)) {
            return 'https://images.unsplash.com/photo-1544716278-ca5e3f4abd8c?w=500&q=80';
        }
        if (str_starts_with($this->AnhBia, 'http://') || str_starts_with($this->AnhBia, 'https://')) {
            return $this->AnhBia;
        }
        return asset($this->AnhBia);
    }

    /**
     * Kiểm tra tồn kho
     */
    public function kiemTraTonKho(int $soLuong): bool
    {
        return $this->SoLuongTon >= $soLuong;
    }
}
