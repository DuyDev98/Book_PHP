<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class DonHang extends Model
{
    use HasFactory;

    protected $table = 'donhang';
    protected $primaryKey = 'MaDH';

    protected $fillable = [
        'MaKH',
        'NgayDat',
        'TongTien',
        'PhiVanChuyen',
        'DiaChiGiaoHang',
        'GhiChuGiaoHang',
        'TrangThai', // 'ChoDuyet', 'DangXuLy', 'DangGiao', 'DaGiao', 'DaHuy'
    ];

    protected $casts = [
        'TongTien' => 'decimal:2',
        'PhiVanChuyen' => 'decimal:2',
        'NgayDat' => 'datetime',
    ];

    public function khachHang()
    {
        return $this->belongsTo(KhachHang::class, 'MaKH', 'MaKH');
    }

    public function chiTiets()
    {
        return $this->hasMany(ChiTietDonHang::class, 'MaDH', 'MaDH');
    }

    /**
     * Hủy đơn hàng và tự động hoàn kho theo tài liệu nghiệp vụ
     */
    public function huyDonHang(): bool
    {
        if ($this->TrangThai === 'DaHuy' || $this->TrangThai === 'Đã hủy' || $this->TrangThai === 'DaGiao' || $this->TrangThai === 'Đã giao') {
            return false;
        }

        return DB::transaction(function () {
            // Hoàn lại tồn kho cho từng sản phẩm trong đơn
            foreach ($this->chiTiets as $chiTiet) {
                Sach::where('MaSach', $chiTiet->MaSach)
                    ->increment('SoLuongTon', $chiTiet->SoLuong);
            }

            $this->TrangThai = 'Đã hủy';
            $this->save();

            return true;
        });
    }
}
