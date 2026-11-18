/**
 * API Client wrapper cho Bookstore Hệ thống RESTful API
 */
const API_BASE_URL = 'http://127.0.0.1:8000/api';

const api = {
    getToken() {
        return localStorage.getItem('bookstore_token');
    },

    getUser() {
        const u = localStorage.getItem('bookstore_user');
        return u ? JSON.parse(u) : null;
    },

    setAuth(user, token, role) {
        localStorage.setItem('bookstore_user', JSON.stringify(user));
        localStorage.setItem('bookstore_token', token);
        localStorage.setItem('bookstore_role', role);
    },

    clearAuth() {
        localStorage.removeItem('bookstore_user');
        localStorage.removeItem('bookstore_token');
        localStorage.removeItem('bookstore_role');
    },

    async request(endpoint, options = {}) {
        const url = `${API_BASE_URL}${endpoint}`;
        const headers = options.headers || {};

        const token = this.getToken();
        if (token) {
            headers['Authorization'] = `Bearer ${token}`;
        }

        // Nếu không phải FormData thì gắn Content-Type JSON
        if (!(options.body instanceof FormData)) {
            headers['Content-Type'] = 'application/json';
            headers['Accept'] = 'application/json';
        } else {
            headers['Accept'] = 'application/json';
        }

        options.headers = headers;

        try {
            const res = await fetch(url, options);
            const data = await res.json().catch(() => ({}));

            if (!res.ok) {
                // Token hết hạn hoặc không hợp lệ
                if (res.status === 401) {
                    this.clearAuth();
                    if (window.location.pathname.includes('/admin/') || window.location.pathname.includes('profile') || window.location.pathname.includes('orders')) {
                        window.location.href = '/login.html';
                    }
                }
                const error = new Error(data.message || 'Có lỗi xảy ra khi gọi API');
                error.data = data;
                error.status = res.status;
                throw error;
            }

            return data;
        } catch (err) {
            console.error('API Error:', err);
            throw err;
        }
    },

    get(endpoint, params = {}) {
        const query = new URLSearchParams();
        for (const [key, val] of Object.entries(params)) {
            if (val !== undefined && val !== null && val !== '') {
                query.append(key, val);
            }
        }
        const qs = query.toString() ? `?${query.toString()}` : '';
        return this.request(`${endpoint}${qs}`, { method: 'GET' });
    },

    post(endpoint, body) {
        const isFormData = body instanceof FormData;
        return this.request(endpoint, {
            method: 'POST',
            body: isFormData ? body : JSON.stringify(body)
        });
    },

    put(endpoint, body) {
        const isFormData = body instanceof FormData;
        return this.request(endpoint, {
            method: 'PUT',
            body: isFormData ? body : JSON.stringify(body)
        });
    },

    delete(endpoint) {
        return this.request(endpoint, { method: 'DELETE' });
    }
};

window.api = api;
