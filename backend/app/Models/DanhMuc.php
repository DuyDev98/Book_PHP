<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DanhMuc extends Model
{
    use HasFactory;

    protected $table = 'danhmuc';
    protected $primaryKey = 'MaDanhMuc';

    protected $fillable = [
        'TenDanhMuc',
        'ParentID',
    ];

    public function parent()
    {
        return $this->belongsTo(DanhMuc::class, 'ParentID', 'MaDanhMuc');
    }

    public function children()
    {
        return $this->hasMany(DanhMuc::class, 'ParentID', 'MaDanhMuc');
    }

    public function sachs()
    {
        return $this->hasMany(Sach::class, 'MaDanhMuc', 'MaDanhMuc');
    }
}
