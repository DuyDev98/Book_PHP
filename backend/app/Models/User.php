<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'user';

    protected $fillable = [
        'Username',
        'PassWord',
        'VaiTro',
    ];

    protected $hidden = [
        'PassWord',
        'remember_token',
    ];

    /**
     * Override default password column name for authentication.
     */
    public function getAuthPasswordName()
    {
        return 'PassWord';
    }

    public function getAuthPassword()
    {
        return $this->PassWord;
    }

    public function khachHang()
    {
        return $this->hasOne(KhachHang::class, 'Username', 'Username');
    }

    public function isAdmin(): bool
    {
        return strtolower($this->VaiTro) === 'admin';
    }
}
