{{-- Laporan milik pengguna: lihat, ubah, hapus, dan putuskan klaim masuk. --}}
<div
    class="mx-auto max-w-5xl px-6 py-10"
    x-data="{
        items: [],
        categories: [],
        loading: true,
        error: '',
        notice: '',
        status: '',
        page: 1,
        lastPage: 1,
        formOpen: false,
        formEditId: null,
        formErrors: {},
        form: { title: '', description: '', type: 'lost', location: '', event_date: '', category_id: '', photo: null },
        claimsOpen: false,
        claimsItem: null,
        claims: [],
        claimsLoading: false,
        claimsError: '',
        reviewingId: null,
        statusLabel(s) { return s === 'approved' ? 'Disetujui' : (s === 'blocked' ? 'Diblokir' : 'Menunggu'); },
        statusBadge(s) { return s === 'approved' ? 'bg-emerald-50 text-emerald-700' : (s === 'blocked' ? 'bg-red-50 text-red-700' : 'bg-amber-50 text-amber-700'); },
        claimBadge(s) { return s === 'accepted' ? 'bg-emerald-50 text-emerald-700' : (s === 'rejected' ? 'bg-red-50 text-red-700' : 'bg-amber-50 text-amber-700'); },
        claimLabel(s) { return s === 'accepted' ? 'Diterima' : (s === 'rejected' ? 'Ditolak' : 'Menunggu'); },
        typeLabel(t) { return t === 'found' ? 'Temuan' : 'Hilang'; },
        canEdit(item) { return item.moderation_status !== 'blocked' && ! item.is_resolved; },
        canDelete(item) { return ! item.is_resolved; },
        async loadCategories() {
            const res = await api.get('/categories');
            if (res.ok) { this.categories = res.data.data || []; }
        },
        async load() {
            this.loading = true;
            this.error = '';
            const q = new URLSearchParams({ page: this.page, per_page: 10 });
            if (this.status) q.set('moderation_status', this.status);
            const res = await api.get(`/my/items?${q.toString()}`);
            if (res.ok) {
                this.items = res.data.data || [];
                this.page = res.data.meta ? res.data.meta.current_page : 1;
                this.lastPage = res.data.meta ? res.data.meta.last_page : 1;
            } else {
                this.items = [];
                this.error = 'Tidak bisa memuat laporan Anda. Coba muat ulang.';
            }
            this.loading = false;
        },
        filter() { this.page = 1; this.load(); },
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
            this.formErrors = {};
            this.error = '';
            const fd = new FormData();
            fd.append('title', this.form.title);
            fd.append('description', this.form.description);
            fd.append('type', this.form.type);
            fd.append('location', this.form.location);
            fd.append('event_date', this.form.event_date || '');
            fd.append('category_id', this.form.category_id);
            if (this.form.photo) fd.append('photo', this.form.photo);
            fd.append('_method', 'PUT');
            const res = await api.post(`/items/${this.formEditId}`, fd);
            if (res.ok) {
                this.formOpen = false;
                this.notice = 'Laporan diperbarui dan menunggu moderasi ulang.';
                this.load();
            } else if (res.status === 422) {
                this.formErrors = res.data.errors || {};
            } else {
                this.error = res.data.message || 'Gagal memperbarui laporan.';
            }
        },
        async destroy(id) {
            if (! api.confirm('Hapus laporan ini secara permanen?')) return;
            this.notice = '';
            this.error = '';
            const res = await api.delete(`/items/${id}`);
            if (res.ok) {
                this.notice = 'Laporan dihapus.';
                this.load();
            } else {
                this.error = res.data.message || 'Gagal menghapus laporan.';
            }
        },
        async openClaims(item) {
            this.claimsItem = item;
            this.claimsOpen = true;
            this.claims = [];
            this.claimsError = '';
            this.claimsLoading = true;
            const res = await api.get(`/items/${item.id}/claims?per_page=50`);
            if (res.ok) {
                this.claims = res.data.data || [];
            } else {
                this.claimsError = 'Gagal memuat klaim masuk.';
            }
            this.claimsLoading = false;
        },
        closeClaims() { this.claimsOpen = false; this.claimsItem = null; },
        async review(claim, status) {
            const verb = status === 'accepted' ? 'Terima' : 'Tolak';
            if (! api.confirm(`${verb} klaim dari ${claim.claimant ? claim.claimant.name : 'pengguna'}?`)) return;
            this.reviewingId = claim.id;
            this.claimsError = '';
            const res = await api.patch(`/claims/${claim.id}`, { status });
            this.reviewingId = null;
            if (res.ok) {
                this.notice = `Klaim ${status === 'accepted' ? 'diterima. Barang ditandai selesai.' : 'ditolak.'}`;
                if (this.claimsItem) { await this.openClaims(this.claimsItem); }
                await this.load();
            } else {
                this.claimsError = res.data.message || 'Gagal memproses klaim.';
            }
        },
    }"
    x-init="load(); loadCategories()"
    @keydown.escape.window="closeForm(); closeClaims()"
>
    <div class="flex flex-wrap items-start justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold">Laporan Saya</h1>
            <p class="mt-1 text-sm text-gray-600">Kelola laporan barang Anda dan putuskan klaim yang masuk.</p>
        </div>
        <a href="{{ route('items.create') }}" wire:navigate class="rounded-full bg-gray-900 px-4 py-2 text-sm font-semibold text-white hover:bg-gray-700">+ Lapor barang</a>
    </div>

    <template x-if="notice">
        <div class="mt-4 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700" x-text="notice"></div>
    </template>
    <template x-if="error">
        <div class="mt-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700" x-text="error"></div>
    </template>

    <div class="mt-4">
        <select x-model="status" @change="filter()" class="rounded-lg border border-gray-300 px-3 py-2 text-sm">
            <option value="">Semua status</option>
            <option value="pending">Menunggu</option>
            <option value="approved">Disetujui</option>
            <option value="blocked">Diblokir</option>
        </select>
    </div>

    <div x-show="loading" class="mt-6 text-sm text-gray-500">Memuat...</div>

    <div class="mt-4 overflow-x-auto rounded-xl border border-gray-200" x-show="! loading" x-cloak>
        <table class="w-full min-w-[720px] text-left text-sm">
            <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                <tr>
                    <th class="px-4 py-3">Judul</th>
                    <th class="px-4 py-3">Tipe</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3">Klaim</th>
                    <th class="px-4 py-3">Selesai</th>
                    <th class="px-4 py-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <template x-for="item in items" :key="item.id">
                    <tr class="border-t border-gray-100">
                        <td class="px-4 py-3 font-medium" x-text="item.title"></td>
                        <td class="px-4 py-3 text-gray-600" x-text="typeLabel(item.type)"></td>
                        <td class="px-4 py-3"><span class="rounded-full px-2.5 py-1 text-xs font-medium" :class="statusBadge(item.moderation_status)" x-text="statusLabel(item.moderation_status)"></span></td>
                        <td class="px-4 py-3">
                            <button @click="openClaims(item)" class="text-xs font-semibold text-indigo-600 hover:underline" x-text="'Lihat (' + (item.claims_count ?? 0) + ')'"></button>
                        </td>
                        <td class="px-4 py-3 text-gray-600" x-text="item.is_resolved ? 'Ya' : 'Belum'"></td>
                        <td class="px-4 py-3 text-right">
                            <a :href="'/items/' + item.id" class="rounded-full border border-gray-300 px-3 py-1.5 text-xs font-semibold text-gray-700 hover:bg-gray-100">Detail</a>
                            <template x-if="canEdit(item)">
                                <button @click="openEdit(item)" class="rounded-full border border-gray-300 px-3 py-1.5 text-xs font-semibold text-gray-700 hover:bg-gray-100">Edit</button>
                            </template>
                            <template x-if="canDelete(item)">
                                <button @click="destroy(item.id)" class="rounded-full border border-red-300 px-3 py-1.5 text-xs font-semibold text-red-700 hover:bg-red-50">Hapus</button>
                            </template>
                        </td>
                    </tr>
                </template>
                <template x-if="items.length === 0">
                    <tr><td colspan="6" class="px-4 py-8 text-center text-gray-500">Belum ada laporan. <a href="{{ route('items.create') }}" class="text-indigo-600 hover:underline">Buat laporan pertama</a>.</td></tr>
                </template>
            </tbody>
        </table>
    </div>

    <div class="mt-4 flex items-center justify-between text-sm" x-show="! loading && lastPage > 1" x-cloak>
        <button @click="page--; load()" :disabled="page <= 1" class="rounded-full border border-gray-300 px-4 py-2 disabled:opacity-40">Sebelumnya</button>
        <span class="text-gray-600">Halaman <span x-text="page"></span> dari <span x-text="lastPage"></span></span>
        <button @click="page++; load()" :disabled="page >= lastPage" class="rounded-full border border-gray-300 px-4 py-2 disabled:opacity-40">Berikutnya</button>
    </div>

    {{-- Modal Edit --}}
    <div x-show="formOpen" x-cloak class="fixed inset-0 z-50 flex items-start justify-center overflow-y-auto bg-black/40 p-4 sm:items-center" @click.self="closeForm()">
        <div class="w-full max-w-lg rounded-2xl bg-white p-6 shadow-xl" @click.stop>
            <div class="flex items-start justify-between gap-4">
                <h2 class="text-lg font-bold">Ubah laporan</h2>
                <button @click="closeForm()" class="text-gray-400 hover:text-gray-700" aria-label="Tutup">&times;</button>
            </div>
            <p class="mt-1 text-xs text-gray-500">Menyimpan perubahan akan mengembalikan status ke menunggu moderasi.</p>

            <div class="mt-4 grid gap-3">
                <div>
                    <label class="mb-1 block text-sm font-medium">Judul</label>
                    <input x-model="form.title" maxlength="120" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm" />
                    <p x-show="formErrors.title" class="mt-1 text-xs text-red-600" x-text="formErrors.title ? formErrors.title[0] : ''"></p>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="mb-1 block text-sm font-medium">Tipe</label>
                        <select x-model="form.type" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                            <option value="lost">Barang hilang</option>
                            <option value="found">Barang temuan</option>
                        </select>
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium">Kategori</label>
                        <select x-model="form.category_id" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                            <option value="">Pilih kategori</option>
                            <template x-for="c in categories" :key="c.id">
                                <option :value="c.id" x-text="c.name"></option>
                            </template>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium">Lokasi</label>
                    <input x-model="form.location" maxlength="150" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm" />
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium">Tanggal kejadian</label>
                    <input x-model="form.event_date" type="date" max="{{ now()->toDateString() }}" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm" />
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium">Deskripsi</label>
                    <textarea x-model="form.description" rows="3" maxlength="2000" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm"></textarea>
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium">Foto <span class="font-normal text-gray-500">(biarkan kosong jika tidak diganti)</span></label>
                    <input type="file" accept="image/jpeg,image/png,image/webp" @change="form.photo = $event.target.files[0]" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm" />
                </div>
            </div>

            <div class="mt-5 flex justify-end gap-2">
                <button @click="closeForm()" class="rounded-full border border-gray-300 px-4 py-2 text-sm hover:bg-gray-50">Batal</button>
                <button @click="submitForm()" class="rounded-full bg-gray-900 px-4 py-2 text-sm font-semibold text-white hover:bg-gray-700">Simpan</button>
            </div>
        </div>
    </div>

    {{-- Modal Klaim Masuk --}}
    <div x-show="claimsOpen" x-cloak class="fixed inset-0 z-50 flex items-start justify-center overflow-y-auto bg-black/40 p-4 sm:items-center" @click.self="closeClaims()">
        <div class="w-full max-w-lg rounded-2xl bg-white p-6 shadow-xl" @click.stop>
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h2 class="text-lg font-bold">Klaim masuk</h2>
                    <p class="mt-0.5 text-xs text-gray-500" x-text="claimsItem ? claimsItem.title : ''"></p>
                </div>
                <button @click="closeClaims()" class="text-gray-400 hover:text-gray-700" aria-label="Tutup">&times;</button>
            </div>

            <p x-show="claimsLoading" class="mt-4 text-sm text-gray-500">Memuat klaim...</p>
            <template x-if="claimsError">
                <div class="mt-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700" x-text="claimsError"></div>
            </template>

            <p x-show="! claimsLoading && claims.length === 0" class="mt-4 rounded-lg border border-dashed border-gray-300 px-4 py-6 text-center text-sm text-gray-500">Belum ada klaim untuk laporan ini.</p>

            <div class="mt-3 space-y-3">
                <template x-for="claim in claims" :key="claim.id">
                    <div class="rounded-xl border border-gray-200 p-4">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <p class="text-sm font-medium text-gray-900" x-text="claim.claimant ? claim.claimant.name : 'Tanpa nama'"></p>
                                <p class="text-xs text-gray-500" x-text="claim.claimant ? claim.claimant.email : ''"></p>
                            </div>
                            <span class="shrink-0 rounded-full px-2.5 py-1 text-xs font-medium" :class="claimBadge(claim.status)" x-text="claimLabel(claim.status)"></span>
                        </div>
                        <p class="mt-2 whitespace-pre-line text-sm text-gray-700" x-text="claim.message"></p>
                        <template x-if="claim.status === 'pending' && claimsItem && ! claimsItem.is_resolved">
                            <div class="mt-3 flex gap-2">
                                <button @click="review(claim, 'accepted')" :disabled="reviewingId === claim.id" class="rounded-full bg-emerald-600 px-4 py-1.5 text-xs font-semibold text-white hover:bg-emerald-700 disabled:opacity-50">Terima</button>
                                <button @click="review(claim, 'rejected')" :disabled="reviewingId === claim.id" class="rounded-full border border-red-300 px-4 py-1.5 text-xs font-semibold text-red-700 hover:bg-red-50 disabled:opacity-50">Tolak</button>
                            </div>
                        </template>
                    </div>
                </template>
            </div>
        </div>
    </div>
</div>
