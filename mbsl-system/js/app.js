/**
 * MBSL Insurance - Global App Utilities
 * Dark Mode, Auth Guard, Toast Notifications, Navigation, API Helpers
 */

// ── API Base ───────────────────────────────────────────────
// නිවැරදි කළා: PolicyController.php -> Policy.php
export const API_BASE_URL = 'api/controllers/Policy.php';

// ── Auth Guard ───────────────────────────────────────────────
export function requireAuth() {
    if (localStorage.getItem('mbsl_auth') !== 'true') {
        window.location.replace('index.html');
        return false;
    }
    return true;
}

export function getUser() {
    try {
        return JSON.parse(localStorage.getItem('mbsl_user') || '{}');
    } catch {
        return {};
    }
}

export function logout() {
    localStorage.removeItem('mbsl_auth');
    localStorage.removeItem('mbsl_user');
    window.location.href = 'index.html';
}

// ── API Helpers ─────────────────────────────────────────────
export async function apiRequest(url, options = {}) {
    const response = await fetch(url, {
        credentials: 'include',
        headers: {
            'Content-Type': 'application/json',
            ...(options.headers || {})
        },
        ...options
    });

    const text = await response.text();

    let data;
    try {
        data = text ? JSON.parse(text) : {};
    } catch {
        throw new Error('Invalid API response. Check PHP error.');
    }

    if (!response.ok || data.success === false) {
        throw new Error(data.message || data.error || 'API request failed');
    }

    return data;
}

export async function getPolicies() {
    // නිවැරදි කළා: ?action=list එකතු කළා
    const data = await apiRequest(`${API_BASE_URL}?action=list`, {
        method: 'GET'
    });

    return data.policies || data.data?.policies || data.data || [];
}

export async function createPolicy(policyData) {
    // නිවැරදි කළා: ?action=create එකතු කළා
    return await apiRequest(`${API_BASE_URL}?action=create`, {
        method: 'POST',
        body: JSON.stringify(policyData)
    });
}

export async function updatePolicy(id, policyData) {
    // නිවැරදි කළා: ?action=update සහ id එකතු කළා
    return await apiRequest(`${API_BASE_URL}?action=update&id=${id}`, {
        method: 'PUT',
        body: JSON.stringify(policyData)
    });
}

export async function deletePolicy(id) {
    // නිවැරදි කළා: ?action=delete සහ id එකතු කළා
    return await apiRequest(`${API_BASE_URL}?action=delete&id=${id}`, {
        method: 'DELETE'
    });
}

// ── Dark Mode ─────────────────────────────────────────────────
export function applyTheme(theme) {
    document.documentElement.setAttribute('data-theme', theme);

    const btn = document.getElementById('themeToggle');
    if (btn) {
        btn.innerHTML = theme === 'dark'
            ? '<i class="fas fa-sun"></i>'
            : '<i class="fas fa-moon"></i>';

        btn.title = theme === 'dark'
            ? 'Switch to Light Mode'
            : 'Switch to Light Mode';
    }
}

export function initThemeToggle() {
    const saved = localStorage.getItem('mbsl_theme') || 'light';
    applyTheme(saved);

    const btn = document.getElementById('themeToggle');
    if (btn) {
        btn.addEventListener('click', () => {
            const current = document.documentElement.getAttribute('data-theme');
            const next = current === 'dark' ? 'light' : 'dark';

            localStorage.setItem('mbsl_theme', next);
            applyTheme(next);
        });
    }
}

// ── Toast Notifications ────────────────────────────────────────
const TOAST_ICONS = {
    success: 'fas fa-circle-check',
    error: 'fas fa-circle-xmark',
    warning: 'fas fa-triangle-exclamation',
    info: 'fas fa-circle-info',
};

function getOrCreateToastContainer() {
    let c = document.getElementById('toastContainer');

    if (!c) {
        c = document.createElement('div');
        c.id = 'toastContainer';
        c.style.cssText =
            'position:fixed;top:24px;right:24px;z-index:9999;display:flex;flex-direction:column;gap:10px;';
        document.body.appendChild(c);
    }

    return c;
}

export function showToast(type = 'info', title = '', message = '', duration = 4500) {
    const container = getOrCreateToastContainer();
    const icon = TOAST_ICONS[type] || TOAST_ICONS.info;

    const toast = document.createElement('div');
    toast.className = `toast ${type}`;
    toast.style.cssText = `
        display:flex;
        align-items:flex-start;
        gap:12px;
        padding:14px 18px;
        border-radius:0.75rem;
        box-shadow:0 8px 24px rgba(0,0,0,0.15);
        min-width:280px;
        max-width:380px;
        animation:slideIn 0.3s ease;
        position:relative;
        background:${type === 'success' ? '#f0fdf4' : type === 'error' ? '#fef2f2' : type === 'warning' ? '#fffbeb' : '#eff6ff'};
        border-left:4px solid ${type === 'success' ? '#10b981' : type === 'error' ? '#ef4444' : type === 'warning' ? '#f59e0b' : '#3b82f6'};
    `;

    toast.innerHTML = `
        <i class="${icon}" style="font-size:1.1rem;margin-top:2px;color:${type === 'success' ? '#10b981' : type === 'error' ? '#ef4444' : type === 'warning' ? '#f59e0b' : '#3b82f6'}"></i>
        <div style="flex:1">
            <div style="font-weight:700;font-size:0.88rem;color:#1e293b;margin-bottom:2px">${title}</div>
            <div style="font-size:0.82rem;color:#64748b">${message}</div>
        </div>
        <button onclick="this.parentElement.remove()" style="position:absolute;top:10px;right:12px;background:none;border:none;cursor:pointer;color:#94a3b8;font-size:0.9rem">
            <i class="fas fa-xmark"></i>
        </button>
    `;

    container.appendChild(toast);

    const removeTimer = setTimeout(() => {
        toast.style.animation = 'slideOut 0.3s ease forwards';
        setTimeout(() => toast.remove(), 300);
    }, duration);

    toast.querySelector('button').addEventListener('click', () => {
        clearTimeout(removeTimer);
        toast.remove();
    });

    return toast;
}

// ── Spinner Helpers ────────────────────────────────────────────
export function setLoading(btn, loading = true) {
    if (!btn) return;

    if (loading) {
        btn.disabled = true;
        btn.dataset.originalText = btn.innerHTML;
        btn.innerHTML = `
            <span style="display:inline-block;width:16px;height:16px;border:2px solid rgba(255,255,255,0.4);border-top-color:#fff;border-radius:50%;animation:spin 0.7s linear infinite;vertical-align:middle;margin-right:8px"></span>
            Processing...
        `;
    } else {
        btn.disabled = false;
        btn.innerHTML = btn.dataset.originalText || btn.innerHTML;
    }
}

// ── Format Helpers ─────────────────────────────────────────────
export function formatCurrency(amount, currency = 'USD') {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency
    }).format(Number(amount || 0));
}

export function formatDate(dateStr) {
    if (!dateStr) return '-';

    return new Date(dateStr).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
}

export function getInitials(name = '') {
    return name
        .split(' ')
        .map(n => n[0])
        .join('')
        .substring(0, 2)
        .toUpperCase();
}

// ── Active Nav Link ────────────────────────────────────────────
export function setActiveNav() {
    const current = window.location.pathname.split('/').pop();

    document.querySelectorAll('.nav-link').forEach(link => {
        const href = link.getAttribute('href');
        link.classList.toggle('active', href === current);
    });
}

// ── Global Search ──────────────────────────────────────────────
export function initGlobalSearch(searchId = 'globalSearch') {
    const el = document.getElementById(searchId);
    if (!el) return;

    el.addEventListener('keydown', e => {
        if (e.key === 'Enter' && e.target.value.trim()) {
            window.location.href = `policies.html?q=${encodeURIComponent(e.target.value.trim())}`;
        }
    });
}

// ── Role UI ────────────────────────────────────────────────────
export function enforceRoleUI() {
    const user = getUser();
    const role = user.role || 'User';

    if (role !== 'Admin') {
        document.querySelectorAll('.new-policy-btn, #editPolicyBtn, .btn-edit, .admin-only').forEach(el => {
            el.style.setProperty('display', 'none', 'important');
        });
    }
}

// ── Init all globals ────────────────────────────────────────────
export function initApp() {
    if (!requireAuth()) return;

    initThemeToggle();
    setActiveNav();
    initGlobalSearch();

    const user = getUser();

    if (user.name) {
        const initials = getInitials(user.name);
        const avatarEl = document.getElementById('userAvatar');

        if (avatarEl) {
            avatarEl.textContent = initials;
        }
    }

    enforceRoleUI();
}

// ── CSS Animations ─────────────────────────────────────────────
if (!document.getElementById('mbsl-app-styles')) {
    const style = document.createElement('style');
    style.id = 'mbsl-app-styles';
    style.textContent = `
        @keyframes slideIn {
            from { transform:translateX(100%); opacity:0; }
            to { transform:translateX(0); opacity:1; }
        }

        @keyframes slideOut {
            from { transform:translateX(0); opacity:1; }
            to { transform:translateX(100%); opacity:0; }
        }

        @keyframes spin {
            to { transform:rotate(360deg); }
        }
    `;
    document.head.appendChild(style);
}