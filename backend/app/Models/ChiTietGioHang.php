<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChiTietGioHang extends Model
{
    use HasFactory;

    protected $table = 'chitietgiohang';
    public $incrementing = false;
    protected $primaryKey = null;

    protected $fillable = [
        'MaGioHang',
        'MaSach',
        'SoLuong',
    ];

    public function gioHang()
    {
        return $this->belongsTo(GioHang::class, 'MaGioHang', 'MaGioHang');
    }

    public function sach()
    {
        return $this->belongsTo(Sach::class, 'MaSach', 'MaSach');
    }

    /**
     * Hỗ trợ composite primary key cho Eloquent save/update/delete
     */
    protected function setKeysForSaveQuery($query)
    {
        return $query->where('MaGioHang', $this->getAttribute('MaGioHang') ?? $this->getOriginal('MaGioHang'))
                     ->where('MaSach', $this->getAttribute('MaSach') ?? $this->getOriginal('MaSach'));
    }
}
