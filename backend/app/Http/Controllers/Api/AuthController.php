<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\KhachHang;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * Đăng ký tài khoản khách hàng mới
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'Username' => 'required|string|max:50|unique:user,Username',
            'PassWord' => 'required|string|min:6',
            'HoTen' => 'required|string|max:100',
            'Email' => 'nullable|email|max:100',
            'SDienThoai' => 'nullable|string|max:15',
            'DiaChi' => 'nullable|string|max:255',
        ], [
            'Username.required' => 'Vui lòng nhập tên đăng nhập.',
            'Username.unique' => 'Tên đăng nhập đã tồn tại trong hệ thống.',
            'PassWord.required' => 'Vui lòng nhập mật khẩu.',
            'PassWord.min' => 'Mật khẩu phải có ít nhất 6 ký tự.',
            'HoTen.required' => 'Vui lòng nhập họ và tên.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ.',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            return DB::transaction(function () use ($request) {
                // 1. Tạo bản ghi User
                $user = User::create([
                    'Username' => $request->Username,
                    'PassWord' => Hash::make($request->PassWord),
                    'VaiTro' => 'customer',
                ]);

                // 2. Tạo bản ghi KhachHang
                $khachHang = KhachHang::create([
                    'Username' => $user->Username,
                    'HoTen' => $request->HoTen,
                    'Email' => $request->Email,
                    'SDienThoai' => $request->SDienThoai,
                    'DiaChi' => $request->DiaChi,
                ]);

                $token = $user->createToken('auth_token')->plainTextToken;

                return response()->json([
                    'success' => true,
                    'message' => 'Đăng ký tài khoản thành công!',
                    'data' => [
                        'user' => $user,
                        'khachHang' => $khachHang,
                        'token' => $token,
                    ],
                ], 201);
            });
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi đăng ký tài khoản: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Đăng nhập hệ thống (User / Admin)
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'Username' => 'required|string',
            'PassWord' => 'required|string',
        ], [
            'Username.required' => 'Vui lòng nhập tên đăng nhập.',
            'PassWord.required' => 'Vui lòng nhập mật khẩu.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Vui lòng điền đầy đủ thông tin đăng nhập.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = User::with('khachHang')->where('Username', $request->Username)->first();

        if (!$user || !Hash::check($request->PassWord, $user->PassWord)) {
            return response()->json([
                'success' => false,
                'message' => 'Tên đăng nhập hoặc mật khẩu không chính xác.',
            ], 401);
        }

        // Tạo Sanctum Token
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Đăng nhập thành công!',
            'data' => [
                'user' => $user,
                'khachHang' => $user->khachHang,
                'token' => $token,
                'role' => $user->VaiTro,
            ],
        ], 200);
    }

    /**
     * Lấy thông tin user hiện tại qua Token
     */
    public function me(Request $request)
    {
        $user = $request->user()->load('khachHang');

        return response()->json([
            'success' => true,
            'data' => [
                'user' => $user,
                'khachHang' => $user->khachHang,
            ],
        ]);
    }

    /**
     * Cập nhật thông tin cá nhân khách hàng
     */
    public function updateProfile(Request $request)
    {
        $user = $request->user();
        $khachHang = $user->khachHang;

        if (!$khachHang) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy hồ sơ khách hàng.',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'HoTen' => 'required|string|max:100',
            'Email' => 'nullable|email|max:100',
            'SDienThoai' => 'nullable|string|max:15',
            'DiaChi' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $khachHang->update($request->only(['HoTen', 'Email', 'SDienThoai', 'DiaChi']));

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật thông tin thành công!',
            'data' => $khachHang,
        ]);
    }

    /**
     * Đăng xuất & hủy token
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Đăng xuất thành công!',
        ]);
    }
}
