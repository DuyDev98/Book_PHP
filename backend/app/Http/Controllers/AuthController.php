<?php

namespace App\Http\Controllers;

use App\Models\GioHang;
use App\Models\KhachHang;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Màn hình đăng nhập
     */
    public function showLoginForm()
    {
        if (Auth::check()) {
            if (Auth::user()->isAdmin()) {
                return redirect()->route('admin.dashboard');
            }
            return redirect()->route('home');
        }
        return view('client.auth.login');
    }

    /**
     * Xử lý đăng nhập
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ], [
            'username.required' => 'Vui lòng nhập tên đăng nhập.',
            'password.required' => 'Vui lòng nhập mật khẩu.',
        ]);

        $remember = $request->filled('remember');

        if (Auth::attempt(['Username' => $credentials['username'], 'password' => $credentials['password']], $remember)) {
            $request->session()->regenerate();

            $user = Auth::user();
            if ($user->isAdmin()) {
                return redirect()->intended(route('admin.dashboard'))
                    ->with('success', "Xin chào Quản trị viên {$user->Username}! Đăng nhập thành công.");
            }

            return redirect()->intended(route('home'))
                ->with('success', "Đăng nhập thành công! Chào mừng {$user->Username} đã quay trở lại.");
        }

        return back()->withErrors([
            'login_error' => 'Tên đăng nhập hoặc mật khẩu không chính xác. Vui lòng kiểm tra lại.',
        ])->withInput($request->only('username', 'remember'));
    }

    /**
     * Màn hình đăng ký
     */
    public function showRegisterForm()
    {
        if (Auth::check()) {
            return redirect()->route('home');
        }
        return view('client.auth.register');
    }

    /**
     * Xử lý đăng ký tài khoản mới
     */
    public function register(Request $request)
    {
        $request->validate([
            'username' => 'required|string|min:4|max:50|unique:user,Username',
            'password' => 'required|string|min:6|confirmed',
            'ho_ten' => 'required|string|max:100',
            'email' => 'required|email|max:100',
            'so_dien_thoai' => 'required|regex:/^[0-9]{10,11}$/',
            'dia_chi' => 'required|string|max:255',
        ], [
            'username.required' => 'Vui lòng nhập tên tài khoản.',
            'username.min' => 'Tên tài khoản phải có ít nhất 4 ký tự.',
            'username.unique' => 'Tên tài khoản này đã được sử dụng. Vui lòng chọn tên khác.',
            'password.required' => 'Vui lòng nhập mật khẩu.',
            'password.min' => 'Mật khẩu phải có ít nhất 6 ký tự.',
            'password.confirmed' => 'Mật khẩu xác nhận không khớp.',
            'ho_ten.required' => 'Vui lòng nhập họ và tên của bạn.',
            'email.required' => 'Vui lòng nhập địa chỉ email.',
            'email.email' => 'Địa chỉ email không đúng định dạng.',
            'so_dien_thoai.required' => 'Vui lòng nhập số điện thoại.',
            'so_dien_thoai.regex' => 'Số điện thoại phải từ 10 - 11 chữ số hợp lệ.',
            'dia_chi.required' => 'Vui lòng nhập địa chỉ nhận hàng.',
        ]);

        try {
            DB::transaction(function () use ($request) {
                // 1. Tạo User
                $user = User::create([
                    'Username' => trim($request->username),
                    'PassWord' => Hash::make($request->password),
                    'VaiTro' => 'customer',
                ]);

                // 2. Tạo Hồ sơ Khách hàng
                $khachHang = KhachHang::create([
                    'Username' => $user->Username,
                    'HoTen' => trim($request->ho_ten),
                    'Email' => trim($request->email),
                    'SDienThoai' => trim($request->so_dien_thoai),
                    'DiaChi' => trim($request->dia_chi),
                ]);

                // 3. Tạo Giỏ hàng ban đầu
                GioHang::create([
                    'MaKH' => $khachHang->MaKH,
                    'NgayTao' => now(),
                ]);

                // Đăng nhập luôn
                Auth::login($user);
            });

            return redirect()->route('home')->with('success', 'Đăng ký tài khoản thành công! Bạn đã được tự động đăng nhập.');

        } catch (\Exception $e) {
            return back()->with('error', 'Có lỗi xảy ra trong quá trình đăng ký: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Đăng xuất
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('success', 'Bạn đã đăng xuất tài khoản an toàn.');
    }

    /**
     * Hồ sơ cá nhân
     */
    public function profile()
    {
        $user = Auth::user();
        $khachHang = $user->khachHang;

        return view('client.profile.index', compact('user', 'khachHang'));
    }

    /**
     * Cập nhật thông tin cá nhân
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        $khachHang = $user->khachHang;

        $request->validate([
            'ho_ten' => 'required|string|max:100',
            'so_dien_thoai' => 'required|regex:/^[0-9]{10,11}$/',
            'email' => 'required|email|max:100',
            'dia_chi' => 'required|string|max:255',
            'new_password' => 'nullable|string|min:6|confirmed',
        ], [
            'ho_ten.required' => 'Họ và tên không được để trống.',
            'so_dien_thoai.required' => 'Số điện thoại không được để trống.',
            'email.required' => 'Email không được để trống.',
            'dia_chi.required' => 'Địa chỉ không được để trống.',
            'new_password.min' => 'Mật khẩu mới phải có tối thiểu 6 ký tự.',
            'new_password.confirmed' => 'Mật khẩu xác nhận không khớp.',
        ]);

        if ($khachHang) {
            $khachHang->update([
                'HoTen' => $request->ho_ten,
                'SDienThoai' => $request->so_dien_thoai,
                'Email' => $request->email,
                'DiaChi' => $request->dia_chi,
            ]);
        }

        if ($request->filled('new_password')) {
            $user->PassWord = Hash::make($request->new_password);
            $user->save();
        }

        return back()->with('success', 'Hồ sơ cá nhân đã được cập nhật thành công!');
    }
}
