<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BinhLuan extends Model
{
    use HasFactory;

    protected $table = 'binhluan';
    protected $primaryKey = 'MaBL';

    protected $fillable = [
        'MaKH',
        'MaSach',
        'NoiDung',
        'NgayBinhLuan',
        'TrangThai', // 'pending', 'approved', 'rejected'
    ];

    protected $casts = [
        'NgayBinhLuan' => 'datetime',
    ];

    public function khachHang()
    {
        return $this->belongsTo(KhachHang::class, 'MaKH', 'MaKH');
    }

    public function sach()
    {
        return $this->belongsTo(Sach::class, 'MaSach', 'MaSach');
    }
}
