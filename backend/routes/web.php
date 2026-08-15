<?php

use App\Http\Controllers\Admin\AuthorController as AdminAuthorController;
use App\Http\Controllers\Admin\BookController as AdminBookController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\CommentController as AdminCommentController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\PublisherController as AdminPublisherController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OrderController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes (MVC Architecture with Blade)
|--------------------------------------------------------------------------
*/

// ==================== 1. KHÁCH HÀNG & CÔNG KHAI ====================
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/books', [BookController::class, 'index'])->name('books.index');
Route::get('/books/{id}', [BookController::class, 'show'])->name('books.show');

// Xác thực & Đăng nhập
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ==================== 2. KHU VỰC CẦN ĐĂNG NHẬP (AUTH) ====================
Route::middleware(['auth'])->group(function () {
    // Hồ sơ cá nhân
    Route::get('/profile', [AuthController::class, 'profile'])->name('profile.index');
    Route::post('/profile', [AuthController::class, 'updateProfile'])->name('profile.update');

    // Bình luận đánh giá sách
    Route::post('/books/{id}/comment', [BookController::class, 'storeComment'])->name('books.comment');

    // Giỏ hàng
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
    Route::post('/cart/update', [CartController::class, 'update'])->name('cart.update');
    Route::post('/cart/remove/{id}', [CartController::class, 'remove'])->name('cart.remove');
    Route::post('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');

    // Đặt hàng & Đơn hàng
    Route::get('/checkout', [OrderController::class, 'checkout'])->name('checkout.index');
    Route::post('/checkout', [OrderController::class, 'store'])->name('checkout.store');
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{id}', [OrderController::class, 'show'])->name('orders.show');
    Route::post('/orders/{id}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');
});

// ==================== 3. KHU VỰC QUẢN TRỊ VIÊN (ADMIN) ====================
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [AdminDashboardController::class, 'index']);

    // Quản lý Sách (CRUD + Upload + Nhập kho)
    Route::get('/books', [AdminBookController::class, 'index'])->name('books.index');
    Route::get('/books/create', [AdminBookController::class, 'create'])->name('books.create');
    Route::post('/books', [AdminBookController::class, 'store'])->name('books.store');
    Route::get('/books/{id}/edit', [AdminBookController::class, 'edit'])->name('books.edit');
    Route::put('/books/{id}', [AdminBookController::class, 'update'])->name('books.update');
    Route::delete('/books/{id}', [AdminBookController::class, 'destroy'])->name('books.destroy');
    Route::post('/books/{id}/restock', [AdminBookController::class, 'restock'])->name('books.restock');

    // Quản lý Danh mục (CRUD đa cấp)
    Route::get('/categories', [AdminCategoryController::class, 'index'])->name('categories.index');
    Route::post('/categories', [AdminCategoryController::class, 'store'])->name('categories.store');
    Route::put('/categories/{id}', [AdminCategoryController::class, 'update'])->name('categories.update');
    Route::delete('/categories/{id}', [AdminCategoryController::class, 'destroy'])->name('categories.destroy');

    // Quản lý Tác giả (CRUD)
    Route::get('/authors', [AdminAuthorController::class, 'index'])->name('authors.index');
    Route::post('/authors', [AdminAuthorController::class, 'store'])->name('authors.store');
    Route::put('/authors/{id}', [AdminAuthorController::class, 'update'])->name('authors.update');
    Route::delete('/authors/{id}', [AdminAuthorController::class, 'destroy'])->name('authors.destroy');

    // Quản lý Nhà xuất bản (CRUD)
    Route::get('/publishers', [AdminPublisherController::class, 'index'])->name('publishers.index');
    Route::post('/publishers', [AdminPublisherController::class, 'store'])->name('publishers.store');
    Route::put('/publishers/{id}', [AdminPublisherController::class, 'update'])->name('publishers.update');
    Route::delete('/publishers/{id}', [AdminPublisherController::class, 'destroy'])->name('publishers.destroy');

    // Quản lý Đơn hàng
    Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{id}', [AdminOrderController::class, 'show'])->name('orders.show');
    Route::post('/orders/{id}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.status');
    Route::post('/orders/{orderId}/items/{itemId}/remove', [AdminOrderController::class, 'removeItem'])->name('orders.removeItem');

    // Kiểm duyệt Bình luận
    Route::get('/comments', [AdminCommentController::class, 'index'])->name('comments.index');
    Route::post('/comments/{id}/approve', [AdminCommentController::class, 'approve'])->name('comments.approve');
    Route::post('/comments/{id}/reject', [AdminCommentController::class, 'reject'])->name('comments.reject');
    Route::delete('/comments/{id}', [AdminCommentController::class, 'destroy'])->name('comments.destroy');
});
