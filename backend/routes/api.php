<?php

use App\Http\Controllers\Api\AdminDashboardController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BookController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\CatalogController;
use App\Http\Controllers\Api\CommentController;
use App\Http\Controllers\Api\OrderController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| PUBLIC ROUTES
|--------------------------------------------------------------------------
*/
// 1. Xác thực (Auth)
Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);

// 2. Tra cứu sách (Catalog & Books)
Route::get('/sach', [BookController::class, 'index']);
Route::get('/sach/{id}', [BookController::class, 'show']);

Route::get('/danhmuc', [CatalogController::class, 'getCategories']);
Route::get('/tacgia', [CatalogController::class, 'getAuthors']);
Route::get('/nhaxuatban', [CatalogController::class, 'getPublishers']);
Route::get('/loaisach', [CatalogController::class, 'getBookTypes']);

// 3. Bình luận của sách (chỉ xem bình luận đã approved)
Route::get('/binhluan/{maSach}', [CommentController::class, 'getByBook']);

/*
|--------------------------------------------------------------------------
| AUTHENTICATED USER ROUTES
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {
    // Thông tin tài khoản
    Route::get('/auth/me', [AuthController::class, 'me']);
    Route::put('/auth/profile', [AuthController::class, 'updateProfile']);
    Route::post('/auth/logout', [AuthController::class, 'logout']);

    // Giỏ hàng
    Route::get('/cart', [CartController::class, 'index']);
    Route::post('/cart/add', [CartController::class, 'addToCart']);
    Route::put('/cart/update', [CartController::class, 'updateQuantity']);
    Route::delete('/cart/item/{maSach}', [CartController::class, 'removeItem']);
    Route::delete('/cart/clear', [CartController::class, 'clear']);

    // Đơn hàng của khách hàng
    Route::post('/orders/create', [OrderController::class, 'create']);
    Route::get('/orders/my-orders', [OrderController::class, 'myOrders']);
    Route::get('/orders/best-sellers', [OrderController::class, 'bestSellers']);
    Route::get('/orders/customer/{maKH}', [OrderController::class, 'customerOrders']);
    Route::get('/orders/{id}', [OrderController::class, 'show']);
    Route::put('/orders/{id}/cancel', [OrderController::class, 'cancel']);

    // Gửi bình luận đánh giá sách
    Route::post('/binhluan', [CommentController::class, 'store']);
});

/*
|--------------------------------------------------------------------------
| ADMIN ONLY ROUTES
|--------------------------------------------------------------------------
*/
Route::middleware(['auth:sanctum', 'admin'])->group(function () {
    // Dashboard thống kê
    Route::get('/admin/dashboard', [AdminDashboardController::class, 'stats']);

    // Quản lý sách
    Route::post('/sach', [BookController::class, 'store']);
    Route::post('/sach/{id}', [BookController::class, 'update']); // Hỗ trợ upload file FormData
    Route::put('/sach/{id}', [BookController::class, 'update']);
    Route::delete('/sach/{id}', [BookController::class, 'destroy']);
    Route::put('/sach/{id}/nhap-hang', [BookController::class, 'nhapHang']);

    // Quản lý Danh mục
    Route::post('/danhmuc', [CatalogController::class, 'storeCategory']);
    Route::put('/danhmuc/{id}', [CatalogController::class, 'updateCategory']);
    Route::delete('/danhmuc/{id}', [CatalogController::class, 'destroyCategory']);

    // Quản lý Tác giả
    Route::post('/tacgia', [CatalogController::class, 'storeAuthor']);
    Route::put('/tacgia/{id}', [CatalogController::class, 'updateAuthor']);
    Route::delete('/tacgia/{id}', [CatalogController::class, 'destroyAuthor']);

    // Quản lý Nhà xuất bản
    Route::post('/nhaxuatban', [CatalogController::class, 'storePublisher']);
    Route::put('/nhaxuatban/{id}', [CatalogController::class, 'updatePublisher']);
    Route::delete('/nhaxuatban/{id}', [CatalogController::class, 'destroyPublisher']);

    // Quản lý Loại sách
    Route::post('/loaisach', [CatalogController::class, 'storeBookType']);
    Route::put('/loaisach/{id}', [CatalogController::class, 'updateBookType']);
    Route::delete('/loaisach/{id}', [CatalogController::class, 'destroyBookType']);

    // Quản lý Đơn hàng
    Route::get('/admin/orders', [OrderController::class, 'adminIndex']);
    Route::put('/orders/{id}/status', [OrderController::class, 'updateStatus']);
    Route::delete('/orders/{id}/items/{bookId}', [OrderController::class, 'deleteItem']);

    // Kiểm duyệt Bình luận
    Route::get('/admin/binhluan', [CommentController::class, 'adminIndex']);
    Route::put('/binhluan/{id}/status', [CommentController::class, 'updateStatus']);
    Route::delete('/binhluan/admin/delete/{id}', [CommentController::class, 'destroy']);
});
