<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KhachHang extends Model
{
    use HasFactory;

    protected $table = 'khachhang';
    protected $primaryKey = 'MaKH';

    protected $fillable = [
        'Username',
        'HoTen',
        'DiaChi',
        'SDienThoai',
        'Email',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'Username', 'Username');
    }

    public function gioHang()
    {
        return $this->hasOne(GioHang::class, 'MaKH', 'MaKH');
    }

    public function donHangs()
    {
        return $this->hasMany(DonHang::class, 'MaKH', 'MaKH');
    }

    public function binhLuans()
    {
        return $this->hasMany(BinhLuan::class, 'MaKH', 'MaKH');
    }
}
