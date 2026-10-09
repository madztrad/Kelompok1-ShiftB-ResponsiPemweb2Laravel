<div
    class="mx-auto max-w-5xl px-6 py-10"
    x-data="{
        items: [],
        loading: true,
        notice: '',
        error: '',
        status: '',
        search: '',
        page: 1,
        lastPage: 1,
        detail: null,
        detailLoading: false,
        categories: [],
        formOpen: false,
        formEditId: null,
        formErrors: {},
        form: { title: '', description: '', type: 'lost', location: '', event_date: '', category_id: '', photo: null },
        get formTitle() { return this.formEditId ? 'Ubah laporan' : 'Tambah laporan'; },
        badge(s) {
            return s === 'approved' ? 'bg-emerald-50 text-emerald-700' : (s === 'blocked' ? 'bg-red-50 text-red-700' : 'bg-amber-50 text-amber-700');
        },
        label(s) {
            return s === 'approved' ? 'Disetujui' : (s === 'blocked' ? 'Diblokir' : 'Menunggu');
        },
        async loadCategories() {
            const res = await api.get('/categories');
            if (res.ok) { this.categories = res.data.data || []; }
        },
        openCreate() {
            this.formEditId = null;
            this.formErrors = {};
            this.form = { title: '', description: '', type: 'lost', location: '', event_date: '', category_id: '', photo: null };
            this.formOpen = true;
        },
        openEdit(item) {
            this.formEditId = item.id;
            this.formErrors = {};
            this.form = {
                title: item.title || '', description: item.description || '', type: item.type || 'lost',
                location: item.location || '', event_date: item.event_date || '',
                category_id: item.category ? item.category.id : '', photo: null,
            };
            this.formOpen = true;
        },
        closeForm() { this.formOpen = false; },
        async submitForm() {
            this.notice = '';
            this.error = '';
            this.formErrors = {};
            const fd = new FormData();
            fd.append('title', this.form.title);
            fd.append('description', this.form.description);
            fd.append('type', this.form.type);
            fd.append('location', this.form.location);
            fd.append('event_date', this.form.event_date);
            fd.append('category_id', this.form.category_id);
            if (this.form.photo) fd.append('photo', this.form.photo);
            if (this.formEditId) fd.append('_method', 'PUT');
            const path = this.formEditId ? `/admin/items/${this.formEditId}` : '/admin/items';
            const res = await api.post(path, fd);
            if (res.ok || res.status === 201) {
                this.formOpen = false;
                this.notice = this.formEditId ? 'Laporan diperbarui.' : 'Laporan dibuat dan langsung disetujui.';
                this.load();
            } else if (res.status === 422) {
                this.formErrors = res.data.errors || {};
                this.error = res.data.message || 'Periksa input.';
            } else {
                this.error = res.data.message || 'Gagal menyimpan laporan.';
            }
        },
        async load() {
            this.loading = true;
            this.error = '';
            const q = new URLSearchParams({ page: this.page, per_page: 15 });
            if (this.status) q.set('moderation_status', this.status);
            if (this.search.trim()) q.set('search', this.search.trim());
            const res = await api.get(`/admin/items?${q.toString()}`);
            this.items = res.data.data || [];
            this.page = res.data.meta ? res.data.meta.current_page : 1;
            this.lastPage = res.data.meta ? res.data.meta.last_page : 1;
            this.loading = false;
        },
        filter() { this.page = 1; this.load(); },
        async openDetail(id) {
            this.detailLoading = true;
            this.detail = { id };
            this.error = '';
            const res = await api.get(`/admin/items/${id}`);
            if (res.ok) {
                this.detail = res.data.data;
            } else {
                this.detail = null;
                this.error = 'Gagal memuat detail laporan.';
            }
            this.detailLoading = false;
        },
        closeDetail() { this.detail = null; },
        async destroy(id) {
            if (! api.confirm('Hapus laporan ini permanen?')) return;
            this.notice = '';
            this.error = '';
            const res = await api.delete(`/admin/items/${id}`);
            if (res.ok) {
                this.notice = 'Laporan dihapus.';
                this.load();
            } else {
                this.error = res.data.message || 'Gagal menghapus laporan.';
            }
        },
    }"
    x-init="load(); loadCategories()"
    @keydown.escape.window="closeDetail(); closeForm()"
>
    <div class="flex flex-wrap items-start justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold">Semua Laporan</h1>
            <p class="mt-1 text-sm text-gray-600">Lihat, saring, tambah, ubah, dan hapus laporan.</p>
        </div>
        <button @click="openCreate()" class="rounded-full bg-gray-900 px-4 py-2 text-sm font-semibold text-white hover:bg-gray-700">+ Tambah laporan</button>
    </div>

    <template x-if="notice">
        <div class="mt-4 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700" x-text="notice"></div>
    </template>
    <template x-if="error">
        <div class="mt-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700" x-text="error"></div>
    </template>

    <div class="mt-4 flex flex-wrap gap-2">
        <select x-model="status" @change="filter()" class="rounded-lg border border-gray-300 px-3 py-2 text-sm">
            <option value="">Semua status</option>
            <option value="pending">Pending</option>
            <option value="approved">Approved</option>
            <option value="blocked">Blocked</option>
        </select>
        <input x-model="search" @keydown.enter="filter()" placeholder="Cari judul / lokasi..." class="min-w-52 flex-1 rounded-lg border border-gray-300 px-3 py-2 text-sm" />
        <button @click="filter()" class="rounded-lg bg-gray-900 px-4 py-2 text-sm font-semibold text-white hover:bg-gray-700">Cari</button>
    </div>

    <div x-show="loading" class="mt-6 text-sm text-gray-500">Memuat...</div>

    <div class="mt-4 overflow-x-auto rounded-xl border border-gray-200" x-show="! loading">
        <table class="w-full min-w-[640px] text-left text-sm">
            <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                <tr>
                    <th class="px-4 py-3">Judul</th>
                    <th class="px-4 py-3">Pelapor</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3">Selesai</th>
                    <th class="px-4 py-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <template x-for="item in items" :key="item.id">
                    <tr class="border-t border-gray-100">
                        <td class="px-4 py-3 font-medium" x-text="item.title"></td>
                        <td class="px-4 py-3 text-gray-600" x-text="item.reporter ? item.reporter.name : '-'"></td>
                        <td class="px-4 py-3"><span class="rounded-full px-2.5 py-1 text-xs font-medium" :class="badge(item.moderation_status)" x-text="item.moderation_status"></span></td>
                        <td class="px-4 py-3 text-gray-600" x-text="item.is_resolved ? 'Ya' : 'Belum'"></td>
                        <td class="px-4 py-3 text-right">
                            <button @click="openDetail(item.id)" class="rounded-full border border-gray-300 px-3 py-1.5 text-xs font-semibold text-gray-700 hover:bg-gray-100">Detail</button>
                            <button @click="openEdit(item)" class="rounded-full border border-gray-300 px-3 py-1.5 text-xs font-semibold text-gray-700 hover:bg-gray-100">Edit</button>
                            <button @click="destroy(item.id)" class="rounded-full border border-red-300 px-3 py-1.5 text-xs font-semibold text-red-700 hover:bg-red-50">Hapus</button>
                        </td>
                    </tr>
                </template>
                <template x-if="items.length === 0">
                    <tr><td colspan="5" class="px-4 py-8 text-center text-gray-500">Tidak ada laporan.</td></tr>
                </template>
            </tbody>
        </table>
    </div>

    <div class="mt-4 flex items-center justify-between text-sm" x-show="! loading && lastPage > 1">
        <button @click="page--; load()" :disabled="page <= 1" class="rounded-full border border-gray-300 px-4 py-2 disabled:opacity-40">← Sebelumnya</button>
        <span class="text-gray-600">Halaman <span x-text="page"></span> dari <span x-text="lastPage"></span></span>
        <button @click="page++; load()" :disabled="page >= lastPage" class="rounded-full border border-gray-300 px-4 py-2 disabled:opacity-40">Berikutnya →</button>
    </div>

    <div
        x-show="detail"
        x-cloak
        class="fixed inset-0 z-50 flex items-start justify-center overflow-y-auto bg-black/40 p-4 sm:items-center"
        @click.self="closeDetail()"
    >
        <div class="w-full max-w-lg rounded-2xl bg-white p-6 shadow-xl" @click.stop>
            <template x-if="detailLoading">
                <p class="text-sm text-gray-500">Memuat detail...</p>
            </template>

            <template x-if="! detailLoading && detail">
                <div>
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <h2 class="text-lg font-bold" x-text="detail.title"></h2>
                            <p class="mt-0.5 text-xs uppercase tracking-wide text-gray-500" x-text="detail.type === 'found' ? 'Barang temuan' : 'Barang hilang'"></p>
                        </div>
                        <button @click="closeDetail()" class="text-gray-400 hover:text-gray-700" aria-label="Tutup">&times;</button>
                    </div>

                    <template x-if="detail.photo_url">
                        <img :src="detail.photo_url" alt="" class="mt-4 max-h-56 w-full rounded-xl object-cover" />
                    </template>

                    <dl class="mt-4 grid grid-cols-3 gap-x-3 gap-y-2 text-sm">
                        <dt class="text-gray-500">Status</dt>
                        <dd class="col-span-2"><span class="rounded-full px-2.5 py-1 text-xs font-medium" :class="badge(detail.moderation_status)" x-text="label(detail.moderation_status)"></span></dd>

                        <dt class="text-gray-500">Kategori</dt>
                        <dd class="col-span-2" x-text="detail.category ? detail.category.name : '-'"></dd>

                        <dt class="text-gray-500">Pelapor</dt>
                        <dd class="col-span-2" x-text="detail.reporter ? detail.reporter.name : '-'"></dd>

                        <dt class="text-gray-500">Lokasi</dt>
                        <dd class="col-span-2" x-text="detail.location || '-'"></dd>

                        <dt class="text-gray-500">Tanggal</dt>
                        <dd class="col-span-2" x-text="detail.event_date || '-'"></dd>

                        <dt class="text-gray-500">Klaim</dt>
                        <dd class="col-span-2" x-text="detail.claims_count ?? 0"></dd>

                        <dt class="text-gray-500">Selesai</dt>
                        <dd class="col-span-2" x-text="detail.is_resolved ? 'Ya' : 'Belum'"></dd>

                        <template x-if="detail.blocked_reason">
                            <div class="contents">
                                <dt class="text-gray-500">Alasan blokir</dt>
                                <dd class="col-span-2 text-red-700" x-text="detail.blocked_reason"></dd>
                            </div>
                        </template>
                    </dl>

                    <div class="mt-3">
                        <p class="text-gray-500 text-sm">Deskripsi</p>
                        <p class="mt-1 whitespace-pre-line text-sm text-gray-800" x-text="detail.description || '-'"></p>
                    </div>

                    <p class="mt-4 text-xs text-gray-400" x-text="'Dibuat ' + new Date(detail.created_at).toLocaleString('id-ID')"></p>
                </div>
            </template>
        </div>
    </div>

    <div
        x-show="formOpen"
        x-cloak
        class="fixed inset-0 z-50 flex items-start justify-center overflow-y-auto bg-black/40 p-4 sm:items-center"
        @click.self="closeForm()"
    >
        <div class="w-full max-w-lg rounded-2xl bg-white p-6 shadow-xl" @click.stop>
            <div class="flex items-start justify-between gap-4">
                <h2 class="text-lg font-bold" x-text="formTitle"></h2>
                <button @click="closeForm()" class="text-gray-400 hover:text-gray-700" aria-label="Tutup">&times;</button>
            </div>

            <template x-if="formEditId">
                <p class="mt-1 text-xs text-gray-500">Status moderasi tidak berubah lewat form ini.</p>
            </template>

            <div class="mt-4 grid gap-3">
                <div>
                    <label class="mb-1 block text-sm font-medium">Judul</label>
                    <input x-model="form.title" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm" />
                    <template x-if="formErrors.title"><p class="mt-1 text-xs text-red-600" x-text="formErrors.title[0]"></p></template>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="mb-1 block text-sm font-medium">Tipe</label>
                        <select x-model="form.type" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                            <option value="lost">Barang hilang</option>
                            <option value="found">Barang temuan</option>
                        </select>
                        <template x-if="formErrors.type"><p class="mt-1 text-xs text-red-600" x-text="formErrors.type[0]"></p></template>
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium">Kategori</label>
                        <select x-model="form.category_id" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                            <option value="">Pilih kategori</option>
                            <template x-for="c in categories" :key="c.id">
                                <option :value="c.id" x-text="c.name"></option>
                            </template>
                        </select>
                        <template x-if="formErrors.category_id"><p class="mt-1 text-xs text-red-600" x-text="formErrors.category_id[0]"></p></template>
                    </div>
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium">Lokasi</label>
                    <input x-model="form.location" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm" />
                    <template x-if="formErrors.location"><p class="mt-1 text-xs text-red-600" x-text="formErrors.location[0]"></p></template>
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium">Tanggal kejadian</label>
                    <input x-model="form.event_date" type="date" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm" />
                    <template x-if="formErrors.event_date"><p class="mt-1 text-xs text-red-600" x-text="formErrors.event_date[0]"></p></template>
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium">Deskripsi</label>
                    <textarea x-model="form.description" rows="3" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm"></textarea>
                    <template x-if="formErrors.description"><p class="mt-1 text-xs text-red-600" x-text="formErrors.description[0]"></p></template>
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium">
                        Foto
                        <span class="font-normal text-gray-500" x-text="formEditId ? '(biarkan kosong jika tidak diganti)' : '(wajib)'"></span>
                    </label>
                    <input type="file" accept="image/*" @change="form.photo = $event.target.files[0]" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm" />
                    <template x-if="formErrors.photo"><p class="mt-1 text-xs text-red-600" x-text="formErrors.photo[0]"></p></template>
                </div>
            </div>

            <div class="mt-5 flex justify-end gap-2">
                <button @click="closeForm()" class="rounded-full border border-gray-300 px-4 py-2 text-sm hover:bg-gray-50">Batal</button>
                <button @click="submitForm()" class="rounded-full bg-gray-900 px-4 py-2 text-sm font-semibold text-white hover:bg-gray-700">Simpan</button>
            </div>
        </div>
    </div>
</div>
