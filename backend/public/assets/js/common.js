/**
 * Bookstore Common Utilities & Global Helpers (2026 Edition)
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
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer);
                toast.addEventListener('mouseleave', Swal.resumeTimer);
            }
        });
        Toast.fire({
            icon: type,
            title: message
        });
    } else {
        alert(message);
    }
}

// Cập nhật số lượng giỏ hàng trên header badge
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

// Cập nhật trạng thái người dùng đăng nhập trên Navbar
function updateNavbarAuth() {
    const navAuth = document.getElementById('navbarAuth');
    if (!navAuth) return;

    const user = api.getUser();
    const token = api.getToken();

    if (token && user) {
        const role = localStorage.getItem('bookstore_role') || 'customer';
        const isAdmin = (role === 'admin');
        const adminItem = isAdmin 
            ? `<li><a class="dropdown-item text-danger fw-bold" href="/admin/index.html"><i class="bi bi-speedometer2 me-2"></i>Trang Quản Trị Admin</a></li><li><hr class="dropdown-divider"></li>` 
            : '';

        navAuth.innerHTML = `
            <div class="dropdown">
                <button class="btn btn-outline-light dropdown-toggle d-flex align-items-center gap-2 rounded-pill px-3 py-2 border-secondary" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="font-size: 0.88rem; font-weight: 600;">
                    <div style="width: 26px; height: 26px; border-radius: 50%; background: linear-gradient(135deg, #3b82f6, #1d4ed8); display: flex; align-items: center; justify-content: center; color: #fff; font-size: 0.75rem; font-weight: 800;">
                        ${(user.Username || 'U').charAt(0).toUpperCase()}
                    </div>
                    <span>${user.Username}</span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-3 mt-2" style="min-width: 210px; font-size: 0.9rem;">
                    <li class="px-3 py-2 border-bottom bg-light rounded-top">
                        <small class="text-muted d-block" style="font-size: 0.75rem;">Xin chào,</small>
                        <strong class="text-dark">${user.Username}</strong>
                        ${isAdmin ? '<span class="badge bg-danger ms-1" style="font-size: 0.65rem;">ADMIN</span>' : ''}
                    </li>
                    ${adminItem}
                    <li><a class="dropdown-item py-2" href="/profile.html"><i class="bi bi-person me-2 text-primary"></i>Hồ sơ cá nhân</a></li>
                    <li><a class="dropdown-item py-2" href="/orders.html"><i class="bi bi-bag-check me-2 text-success"></i>Lịch sử đơn hàng</a></li>
                    <li><a class="dropdown-item py-2" href="/cart.html"><i class="bi bi-cart3 me-2 text-warning"></i>Giỏ hàng của tôi</a></li>
                    <li><hr class="dropdown-divider my-1"></li>
                    <li><button class="dropdown-item py-2 text-danger fw-semibold" onclick="logout()"><i class="bi bi-box-arrow-right me-2"></i>Đăng xuất</button></li>
                </ul>
            </div>
        `;
    } else {
        navAuth.innerHTML = `
            <div class="d-flex align-items-center gap-2">
                <a href="/login.html" class="btn btn-outline-light btn-sm rounded-pill px-3 py-2 fw-semibold text-nowrap" style="font-size: 0.85rem;">
                    <i class="bi bi-box-arrow-in-right me-1"></i>Đăng nhập
                </a>
                <a href="/login.html?tab=register" class="btn btn-warning btn-sm text-dark fw-bold rounded-pill px-3 py-2 text-nowrap" style="font-size: 0.85rem;">
                    <i class="bi bi-person-plus me-1"></i>Đăng ký
                </a>
            </div>
        `;
    }
}

async function logout() {
    try {
        await api.post('/auth/logout', {});
    } catch (e) {
        // bỏ qua nếu lỗi kết nối
    }
    api.clearAuth();
    showToast('Đã đăng xuất tài khoản thành công!', 'info');
    setTimeout(() => {
        window.location.href = '/index.html';
    }, 500);
}

document.addEventListener('DOMContentLoaded', () => {
    updateNavbarAuth();
    updateCartCount();
});
