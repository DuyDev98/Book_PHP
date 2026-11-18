<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user || strtolower($user->VaiTro) !== 'admin') {
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Truy cập bị từ chối. Bạn không có quyền Quản trị viên (Admin).',
                ], 403);
            }
            return redirect()->route('home')->with('error', 'Truy cập bị từ chối. Bạn không có quyền Quản trị viên (Admin).');
        }

        return $next($request);
    }
}
