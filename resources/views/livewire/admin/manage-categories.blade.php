<div
    class="mx-auto max-w-3xl px-6 py-10"
    x-data="{
        categories: [],
        loading: true,
        notice: '',
        error: '',
        name: '',
        editingId: null,
        editName: '',
        async load() {
            this.loading = true;
            const res = await api.get('/categories');
            this.categories = res.data.data || [];
            this.loading = false;
        },
        async create() {
            this.notice = '';
            this.error = '';
            if (! this.name.trim()) { this.error = 'Nama kategori wajib diisi.'; return; }
            const res = await api.post('/admin/categories', { name: this.name.trim() });
            if (res.status === 201) {
                this.name = '';
                this.notice = 'Kategori ditambahkan.';
                this.load();
            } else {
                this.error = (res.data.errors && res.data.errors.name) ? res.data.errors.name[0] : (res.data.message || 'Gagal menambah kategori.');
            }
        },
        async save(id) {
            this.notice = '';
            this.error = '';
            if (! this.editName.trim()) { this.error = 'Nama kategori wajib diisi.'; return; }
            const res = await api.put(`/admin/categories/${id}`, { name: this.editName.trim() });
            if (res.ok) {
                this.editingId = null;
                this.notice = 'Kategori diperbarui.';
                this.load();
            } else {
                this.error = (res.data.errors && res.data.errors.name) ? res.data.errors.name[0] : (res.data.message || 'Gagal memperbarui kategori.');
            }
        },
        async destroy(id) {
            if (! api.confirm('Hapus kategori ini?')) return;
            this.notice = '';
            this.error = '';
            const res = await api.delete(`/admin/categories/${id}`);
            if (res.status === 204) {
                this.notice = 'Kategori dihapus.';
                this.load();
            } else {
                this.error = res.data.message || 'Gagal menghapus kategori.';
            }
        },
    }"
    x-init="load()"
>
    <div>
        <h1 class="text-2xl font-bold">Kelola Kategori</h1>
        <p class="mt-1 text-sm text-gray-600">Kategori dipakai pada form lapor dan filter beranda.</p>
    </div>

    <template x-if="notice">
        <div class="mt-4 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700" x-text="notice"></div>
    </template>
    <template x-if="error">
        <div class="mt-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700" x-text="error"></div>
    </template>

    <div class="mt-6 flex gap-2">
        <input x-model="name" @keydown.enter="create()" placeholder="Nama kategori baru..." class="flex-1 rounded-lg border border-gray-300 px-3 py-2 text-sm" />
        <button @click="create()" class="rounded-lg bg-gray-900 px-4 py-2 text-sm font-semibold text-white hover:bg-gray-700">Tambah</button>
    </div>

    <div x-show="loading" class="mt-6 text-sm text-gray-500">Memuat...</div>

    <ul class="mt-4 divide-y divide-gray-100 rounded-xl border border-gray-200" x-show="! loading">
        <template x-for="c in categories" :key="c.id">
            <li class="flex items-center justify-between gap-3 px-4 py-3">
                <template x-if="editingId !== c.id">
                    <span class="font-medium" x-text="c.name"></span>
                </template>
                <template x-if="editingId === c.id">
                    <span class="flex flex-1 gap-2">
                        <input x-model="editName" class="flex-1 rounded-lg border border-gray-300 px-3 py-1.5 text-sm" />
                        <button @click="save(c.id)" class="rounded-full bg-emerald-600 px-3 py-1.5 text-xs font-semibold text-white">Simpan</button>
                        <button @click="editingId = null" class="rounded-full border border-gray-300 px-3 py-1.5 text-xs">Batal</button>
                    </span>
                </template>
                <span class="flex gap-2" x-show="editingId !== c.id">
                    <button @click="editingId = c.id; editName = c.name" class="rounded-full border border-gray-300 px-3 py-1.5 text-xs font-semibold hover:bg-gray-50">Ubah</button>
                    <button @click="destroy(c.id)" class="rounded-full border border-red-300 px-3 py-1.5 text-xs font-semibold text-red-700 hover:bg-red-50">Hapus</button>
                </span>
            </li>
        </template>
        <template x-if="categories.length === 0">
            <li class="px-4 py-8 text-center text-sm text-gray-500">Belum ada kategori.</li>
        </template>
    </ul>
</div>
