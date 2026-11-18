<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GioHang extends Model
{
    use HasFactory;

    protected $table = 'giohang';
    protected $primaryKey = 'MaGioHang';

    protected $fillable = [
        'MaKH',
        'NgayTao',
    ];

    public function khachHang()
    {
        return $this->belongsTo(KhachHang::class, 'MaKH', 'MaKH');
    }

    public function chiTiets()
    {
        return $this->hasMany(ChiTietGioHang::class, 'MaGioHang', 'MaGioHang');
    }
}
