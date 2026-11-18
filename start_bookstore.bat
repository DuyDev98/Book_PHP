@echo off
chcp 65001 > nul
echo ========================================================
echo    KHỞI ĐỘNG HỆ THỐNG WEBSITE BÁN SÁCH BOOKSTORE
echo ========================================================
echo.

set "PHP_DIR=C:\Users\Owner\AppData\Local\Microsoft\WinGet\Packages\PHP.PHP.8.2_Microsoft.Winget.Source_8wekyb3d8bbwe"
set "MYSQL_DIR=C:\Program Files\MySQL\MySQL Server 8.4\bin"
set "PATH=%PHP_DIR%;%MYSQL_DIR%;%PATH%"

echo 1. Kiểm tra & Khởi động MySQL Server...
start "MySQL Server" /min "%MYSQL_DIR%\mysqld.exe" --datadir="C:\ProgramData\MySQL\data" --console

timeout /t 2 > nul

echo 2. Khởi động Laravel Backend API (Port 8000)...
start "Laravel Backend API" /min cmd /k "cd /d A:\bookPHP\backend && php artisan serve --port=8000"

timeout /t 2 > nul

echo 3. Khởi động Frontend Web Server (Port 3000)...
start "Frontend Web Client" /min cmd /k "cd /d A:\bookPHP\frontend && php -S 127.0.0.1:3000"

timeout /t 1 > nul

echo.
echo ========================================================
echo [HOÀN TẤT] Hệ thống đã sẵn sàng hoạt động:
echo - Website Khách Hàng: http://127.0.0.1:3000
echo - Trang Quản Trị Admin: http://127.0.0.1:3000/admin/index.html
echo - Backend RESTful API: http://127.0.0.1:8000/api
echo.
echo Tài khoản đăng nhập mẫu:
echo + Quản trị viên (Admin): admin / 123456
echo + Khách hàng (Customer): khachhang1 / 123456
echo ========================================================
echo.
start http://127.0.0.1:3000
pause
