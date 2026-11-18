<?php

namespace App\Http\Controllers;

use App\Models\DanhMuc;
use App\Models\Sach;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        // 1. Danh mục sách chính
        $categories = DanhMuc::whereNull('ParentID')
            ->withCount('sachs')
            ->get();

        // 2. Sách đang khuyến mãi (Flash Sale)
        $saleBooks = Sach::with(['tacGia', 'danhMuc'])
            ->where('IsSale', true)
            ->orWhere('PhanTramGiamGia', '>', 0)
            ->take(8)
            ->get();

        // 3. Sách mới phát hành
        $newBooks = Sach::with(['tacGia', 'danhMuc'])
            ->orderBy('MaSach', 'desc')
            ->take(8)
            ->get();

        // 4. Sách bán chạy / Nổi bật
        $featuredBooks = Sach::with(['tacGia', 'danhMuc'])
            ->where('SoLuongTon', '>', 0)
            ->orderBy('MaSach', 'asc')
            ->take(8)
            ->get();

        return view('client.home', compact('categories', 'saleBooks', 'newBooks', 'featuredBooks'));
    }
}
