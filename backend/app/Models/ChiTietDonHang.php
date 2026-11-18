<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChiTietDonHang extends Model
{
    use HasFactory;

    protected $table = 'chitietdonhang';
    public $incrementing = false;
    protected $primaryKey = null;

    protected $fillable = [
        'MaDH',
        'MaSach',
        'SoLuong',
        'DonGia',
    ];

    protected $casts = [
        'DonGia' => 'decimal:2',
    ];

    public function donHang()
    {
        return $this->belongsTo(DonHang::class, 'MaDH', 'MaDH');
    }

    public function sach()
    {
        return $this->belongsTo(Sach::class, 'MaSach', 'MaSach');
    }

    public function tinhThanhTien(): float
    {
        return (float) ($this->SoLuong * $this->DonGia);
    }

    /**
     * Hỗ trợ composite primary key cho Eloquent save/update/delete
     */
    protected function setKeysForSaveQuery($query)
    {
        return $query->where('MaDH', $this->getAttribute('MaDH') ?? $this->getOriginal('MaDH'))
                     ->where('MaSach', $this->getAttribute('MaSach') ?? $this->getOriginal('MaSach'));
    }
}
