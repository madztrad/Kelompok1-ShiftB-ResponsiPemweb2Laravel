<div
    class="mx-auto max-w-5xl px-6 py-10"
    x-data="{
        base: '{{ url('/') }}',
        token: @js(session('api_token')),
        users: [],
        loading: true,
        notice: '',
        error: '',
        formErrors: {},
        form: { name: '', email: '', password: '', role: 'user' },
        editingId: null,
        editRole: 'user',
        headers(json = true) {
            const h = { 'Accept': 'application/json', 'Authorization': 'Bearer ' + this.token };
            if (json) h['Content-Type'] = 'application/json';
            return h;
        },
        toLogin() { window.location.href = '{{ route('login') }}'; },
        async load() {
            this.loading = true;
            const res = await fetch(`${this.base}/api/admin/users?per_page=50`, { headers: this.headers(false) });
            if (res.status === 401) return this.toLogin();
            const data = await res.json();
            this.users = data.data || [];
            this.loading = false;
        },
        async create() {
            this.notice = '';
            this.error = '';
            this.formErrors = {};
            const res = await fetch(`${this.base}/api/admin/users`, {
                method: 'POST',
                headers: this.headers(),
                body: JSON.stringify(this.form),
            });
            if (res.status === 401) return this.toLogin();
            const data = await res.json().catch(() => ({}));
            if (res.status === 201) {
                this.form = { name: '', email: '', password: '', role: 'user' };
                this.notice = 'Pengguna dibuat.';
                this.load();
            } else if (res.status === 422) {
                this.formErrors = data.errors || {};
                this.error = data.message || 'Periksa input.';
            } else {
                this.error = data.message || 'Gagal membuat pengguna.';
            }
        },
        async saveRole(id) {
            this.notice = '';
            this.error = '';
            const res = await fetch(`${this.base}/api/admin/users/${id}`, {
                method: 'PUT',
                headers: this.headers(),
                body: JSON.stringify({ role: this.editRole }),
            });
            if (res.status === 401) return this.toLogin();
            const data = await res.json().catch(() => ({}));
            if (res.ok) {
                this.editingId = null;
                this.notice = 'Role diperbarui. Token pengguna dicabut, ia harus login ulang.';
                this.load();
            } else {
                this.error = data.message || 'Gagal memperbarui role.';
            }
        },
        async destroy(id) {
            if (! confirm('Hapus pengguna ini? Laporannya ikut terhapus.')) return;
            this.notice = '';
            this.error = '';
            const res = await fetch(`${this.base}/api/admin/users/${id}`, {
                method: 'DELETE',
                headers: this.headers(false),
            });
            if (res.status === 401) return this.toLogin();
            if (res.status === 204) {
                this.notice = 'Pengguna dihapus.';
                this.load();
            } else {
                const data = await res.json().catch(() => ({}));
                this.error = data.message || 'Gagal menghapus pengguna.';
            }
        },
    }"
    x-init="load()"
>
    <div>
        <h1 class="text-2xl font-bold">Kelola Pengguna</h1>
        <p class="mt-1 text-sm text-gray-600">Tambah pengguna, ubah role, atau hapus akun.</p>
    </div>

    <template x-if="notice">
        <div class="mt-4 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700" x-text="notice"></div>
    </template>
    <template x-if="error">
        <div class="mt-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700" x-text="error"></div>
    </template>

    <div class="mt-6 rounded-xl border border-gray-200 p-5">
        <h2 class="font-semibold">Tambah pengguna</h2>
        <div class="mt-3 grid gap-3 sm:grid-cols-2">
            <div>
                <input x-model="form.name" placeholder="Nama" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm" />
                <template x-if="formErrors.name"><p class="mt-1 text-xs text-red-600" x-text="formErrors.name[0]"></p></template>
            </div>
            <div>
                <input x-model="form.email" type="email" placeholder="Email" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm" />
                <template x-if="formErrors.email"><p class="mt-1 text-xs text-red-600" x-text="formErrors.email[0]"></p></template>
            </div>
            <div>
                <input x-model="form.password" type="password" placeholder="Password (min. 8)" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm" />
                <template x-if="formErrors.password"><p class="mt-1 text-xs text-red-600" x-text="formErrors.password[0]"></p></template>
            </div>
            <div class="flex gap-2">
                <select x-model="form.role" class="flex-1 rounded-lg border border-gray-300 px-3 py-2 text-sm">
                    <option value="user">user</option>
                    <option value="admin">admin</option>
                </select>
                <button @click="create()" class="rounded-lg bg-gray-900 px-4 py-2 text-sm font-semibold text-white hover:bg-gray-700">Tambah</button>
            </div>
        </div>
    </div>

    <div x-show="loading" class="mt-6 text-sm text-gray-500">Memuat...</div>

    <div class="mt-4 overflow-x-auto rounded-xl border border-gray-200" x-show="! loading">
        <table class="w-full min-w-[640px] text-left text-sm">
            <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                <tr>
                    <th class="px-4 py-3">Nama</th>
                    <th class="px-4 py-3">Email</th>
                    <th class="px-4 py-3">Role</th>
                    <th class="px-4 py-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <template x-for="u in users" :key="u.id">
                    <tr class="border-t border-gray-100">
                        <td class="px-4 py-3 font-medium" x-text="u.name"></td>
                        <td class="px-4 py-3 text-gray-600" x-text="u.email"></td>
                        <td class="px-4 py-3">
                            <template x-if="editingId !== u.id">
                                <span class="rounded-full px-2.5 py-1 text-xs font-medium" :class="u.role === 'admin' ? 'bg-violet-50 text-violet-700' : 'bg-gray-100 text-gray-700'" x-text="u.role"></span>
                            </template>
                            <template x-if="editingId === u.id">
                                <span class="flex gap-2">
                                    <select x-model="editRole" class="rounded-lg border border-gray-300 px-2 py-1 text-xs">
                                        <option value="user">user</option>
                                        <option value="admin">admin</option>
                                    </select>
                                    <button @click="saveRole(u.id)" class="rounded-full bg-emerald-600 px-3 py-1 text-xs font-semibold text-white">Simpan</button>
                                    <button @click="editingId = null" class="rounded-full border border-gray-300 px-3 py-1 text-xs">Batal</button>
                                </span>
                            </template>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <span class="flex justify-end gap-2">
                                <button @click="editingId = u.id; editRole = u.role" class="rounded-full border border-gray-300 px-3 py-1.5 text-xs font-semibold hover:bg-gray-50">Role</button>
                                <button @click="destroy(u.id)" class="rounded-full border border-red-300 px-3 py-1.5 text-xs font-semibold text-red-700 hover:bg-red-50">Hapus</button>
                            </span>
                        </td>
                    </tr>
                </template>
            </tbody>
        </table>
    </div>
</div>
