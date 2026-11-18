/**
 * Các hàm tiện ích dùng chung cho Bookstore
 */

function formatMoney(amount) {
    if (amount === null || amount === undefined || isNaN(amount)) return '0 đ';
    return new Intl.NumberFormat('vi-VN').format(amount) + ' đ';
}

function formatDate(dateStr) {
    if (!dateStr) return '';
    const d = new Date(dateStr);
    return d.toLocaleString('vi-VN', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
}

function showToast(message, type = 'success') {
    if (typeof Swal !== 'undefined') {
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true
        });
        Toast.fire({
            icon: type,
            title: message
        });
    } else {
        alert(message);
    }
}

// Cập nhật số lượng giỏ hàng trên header
async function updateCartCount() {
    const badge = document.getElementById('cartBadge');
    if (!badge) return;

    if (!api.getToken()) {
        badge.textContent = '0';
        return;
    }

    try {
        const res = await api.get('/cart');
        if (res.success && res.data) {
            badge.textContent = res.data.total_items || 0;
        }
    } catch (e) {
        badge.textContent = '0';
    }
}

// Cập nhật trạng thái đăng nhập trên Navbar
function updateNavbarAuth() {
    const navAuth = document.getElementById('navbarAuth');
    if (!navAuth) return;

    const user = api.getUser();
    const token = api.getToken();

    if (token && user) {
        const role = localStorage.getItem('bookstore_role') || 'customer';
        const adminPill = (role === 'admin') 
            ? `<a class="dropdown-item text-danger fw-bold" href="/admin/index.html"><i class="bi bi-speedometer2 me-2"></i>Trang Quản Trị Admin</a><hr class="dropdown-divider">` 
            : '';

        navAuth.innerHTML = `
            <div class="dropdown">
                <button class="btn btn-outline-light dropdown-toggle d-flex align-items-center gap-2" type="button" data-bs-toggle="dropdown">
                    <i class="bi bi-person-circle"></i>
                    <span>${user.Username}</span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow">
                    ${adminPill}
                    <li><a class="dropdown-item" href="/profile.html"><i class="bi bi-person me-2"></i>Hồ sơ cá nhân</a></li>
                    <li><a class="dropdown-item" href="/orders.html"><i class="bi bi-bag-check me-2"></i>Lịch sử đơn hàng</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><button class="dropdown-item text-danger" onclick="logout()"><i class="bi bi-box-arrow-right me-2"></i>Đăng xuất</button></li>
                </ul>
            </div>
        `;
    } else {
        navAuth.innerHTML = `
            <a href="/login.html" class="btn btn-outline-light me-2"><i class="bi bi-box-arrow-in-right me-1"></i>Đăng nhập</a>
            <a href="/login.html?tab=register" class="btn btn-warning text-dark fw-semibold"><i class="bi bi-person-plus me-1"></i>Đăng ký</a>
        `;
    }
}

async function logout() {
    try {
        await api.post('/auth/logout', {});
    } catch (e) {
        // bỏ qua nếu lỗi mạng
    }
    api.clearAuth();
    showToast('Đã đăng xuất thành công!', 'info');
    setTimeout(() => {
        window.location.href = '/index.html';
    }, 600);
}

document.addEventListener('DOMContentLoaded', () => {
    updateNavbarAuth();
    updateCartCount();
});
