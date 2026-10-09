/**
 * Helper komunikasi ke REST API admin.
 *
 * Semua halaman admin memakai fetch() ke API (Jalur B: FE tidak akses DB
 * langsung). Pola header + token + tangani-401 identik di semua halaman,
 * jadi dijadikan satu helper di sini agar tiap view tinggal panggil:
 *
 *   const res = await api.get('/admin/items?per_page=15');
 *   if (res.ok) this.items = res.data.data;
 *
 * Base URL & token disuntik layout lewat window.__apiBase / window.__apiToken.
 */

function redirectToLogin() {
    window.location.href = '/login';
}

function buildOptions(method, body) {
    const headers = {
        Accept: 'application/json',
        Authorization: `Bearer ${window.__apiToken ?? ''}`,
    };
    const options = { method, headers };

    // FormData: biarkan browser mengatur Content-Type (boundary multipart).
    if (body instanceof FormData) {
        options.body = body;
    } else if (body !== undefined) {
        headers['Content-Type'] = 'application/json';
        options.body = JSON.stringify(body);
    }

    return options;
}

/**
 * Panggil API. Selalu mengembalikan { ok, status, data } — TIDAK melempar,
 * supaya pemanggil bisa menangani error dengan if/else biasa.
 * Status 401 langsung mengalihkan ke halaman login.
 */
async function request(method, path, body) {
    const res = await fetch(`${window.__apiBase ?? '/api'}${path}`, buildOptions(method, body));

    if (res.status === 401) {
        redirectToLogin();

        return { ok: false, status: 401, data: {} };
    }

    // 204 No Content tidak punya body JSON.
    const data = res.status === 204 ? {} : await res.json().catch(() => ({}));

    return { ok: res.ok, status: res.status, data };
}

/**
 * Konfirmasi yang aman dari "orphan click": saat navigasi SPA (wire:navigate)
 * dimulai, sisa gesture mouseup/click bisa bocor ke tombol di halaman baru dan
 * memicu confirm() tak sengaja. Kita tolak confirm yang terjadi tak lama
 * setelah navigasi mulai.
 */
const NAV_GUARD_MS = 600;

document.addEventListener('livewire:navigate', () => {
    window.__navGuardUntil = Date.now() + NAV_GUARD_MS;
});

function safeConfirm(message) {
    if (Date.now() < (window.__navGuardUntil ?? 0)) {
        return false;
    }

    return window.confirm(message);
}

window.api = {
    get: (path) => request('GET', path),
    post: (path, body) => request('POST', path, body),
    put: (path, body) => request('PUT', path, body),
    patch: (path, body) => request('PATCH', path, body),
    delete: (path) => request('DELETE', path),
    confirm: safeConfirm,
};
