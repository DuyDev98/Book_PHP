<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TacGia extends Model
{
    use HasFactory;

    protected $table = 'tacgia';
    protected $primaryKey = 'MaTG';

    protected $fillable = [
        'TenTG',
        'NamSinh',
        'QueQuan',
        'NamMat',
    ];

    public function sachs()
    {
        return $this->hasMany(Sach::class, 'MaTG', 'MaTG');
    }
}
