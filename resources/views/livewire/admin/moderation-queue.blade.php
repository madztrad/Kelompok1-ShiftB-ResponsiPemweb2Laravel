<div
    class="mx-auto max-w-5xl px-6 py-10"
    x-data="{
        items: [],
        loading: true,
        notice: '',
        error: '',
        blockingId: null,
        reason: '',
        detail: null,
        detailLoading: false,
        async load() {
            this.loading = true;
            this.error = '';
            const res = await api.get('/admin/items?moderation_status=pending&per_page=50');
            this.items = res.data.data || [];
            this.loading = false;
        },
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
        async approve(id) {
            this.notice = '';
            this.error = '';
            const res = await api.patch(`/admin/items/${id}/moderation`, { status: 'approved' });
            if (res.ok) {
                this.items = this.items.filter((i) => i.id !== id);
                this.notice = 'Laporan disetujui dan tampil di beranda.';
            } else {
                this.error = res.data.message || 'Gagal menyetujui laporan.';
            }
        },
        async block(id) {
            this.notice = '';
            this.error = '';
            if (! this.reason.trim()) {
                this.error = 'Alasan pemblokiran wajib diisi.';
                return;
            }
            const res = await api.patch(`/admin/items/${id}/moderation`, { status: 'blocked', reason: this.reason });
            if (res.ok) {
                this.items = this.items.filter((i) => i.id !== id);
                this.blockingId = null;
                this.reason = '';
                this.notice = 'Laporan diblokir.';
            } else {
                this.error = res.data.message || 'Gagal memblokir laporan.';
            }
        },
    }"
    x-init="load()"
>
    <div>
        <h1 class="text-2xl font-bold">Moderasi Laporan</h1>
        <p class="mt-1 text-sm text-gray-600">Setujui laporan agar tampil di beranda, atau blokir beserta alasannya.</p>
    </div>

    <template x-if="notice">
        <div class="mt-4 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700" x-text="notice"></div>
    </template>
    <template x-if="error">
        <div class="mt-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700" x-text="error"></div>
    </template>

    <div x-show="loading" class="mt-6 text-sm text-gray-500">Memuat antrean...</div>

    <div x-show="! loading && items.length === 0" class="mt-6 rounded-xl border border-gray-200 bg-gray-50 px-6 py-10 text-center text-sm text-gray-600">
        Antrean kosong. Semua laporan sudah dimoderasi. 🎉
    </div>

    <div class="mt-6 space-y-4" x-show="! loading && items.length > 0">
        <template x-for="item in items" :key="item.id">
            <article class="rounded-xl border border-gray-200 bg-white p-5">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div>
                        <h2 class="font-semibold" x-text="item.title"></h2>
                        <p class="mt-1 text-sm text-gray-600" x-text="item.description"></p>
                        <p class="mt-2 text-xs text-gray-500">
                            <span x-text="item.reporter ? item.reporter.name : '-'"></span>
                            · <span x-text="item.location"></span>
                            · <span x-text="item.event_date"></span>
                        </p>
                    </div>
                    <span class="rounded-full bg-amber-50 px-3 py-1 text-xs font-medium text-amber-700">pending</span>
                </div>

                <div class="mt-4 flex flex-wrap gap-2" x-show="blockingId !== item.id">
                    <button @click="openDetail(item.id)" class="rounded-full border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">Detail</button>
                    <button @click="approve(item.id)" class="rounded-full bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-500">Setujui</button>
                    <button @click="blockingId = item.id; reason = ''" class="rounded-full border border-red-300 px-4 py-2 text-sm font-semibold text-red-700 hover:bg-red-50">Blokir</button>
                </div>

                <div class="mt-4" x-show="blockingId === item.id">
                    <label class="mb-1 block text-sm font-medium">Alasan pemblokiran (wajib)</label>
                    <textarea x-model="reason" rows="2" placeholder="Contoh: foto tidak jelas / data tidak valid" class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-red-400 focus:outline-none"></textarea>
                    <div class="mt-2 flex gap-2">
                        <button @click="block(item.id)" class="rounded-full bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-500">Kirim Blokir</button>
                        <button @click="blockingId = null" class="rounded-full border border-gray-300 px-4 py-2 text-sm hover:bg-gray-50">Batal</button>
                    </div>
                </div>
            </article>
        </template>
    </div>

    <div
        x-show="detail"
        x-cloak
        class="fixed inset-0 z-50 flex items-start justify-center overflow-y-auto bg-black/40 p-4 sm:items-center"
        @click.self="closeDetail()"
        @keydown.escape.window="closeDetail()"
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
                    <template x-if="! detail.photo_url">
                        <p class="mt-4 rounded-xl bg-gray-50 px-4 py-6 text-center text-xs text-gray-400">Tanpa foto</p>
                    </template>

                    <dl class="mt-4 grid grid-cols-3 gap-x-3 gap-y-2 text-sm">
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
                    </dl>

                    <div class="mt-3">
                        <p class="text-sm text-gray-500">Deskripsi</p>
                        <p class="mt-1 whitespace-pre-line text-sm text-gray-800" x-text="detail.description || '-'"></p>
                    </div>

                    <p class="mt-4 text-xs text-gray-400" x-text="'Dibuat ' + new Date(detail.created_at).toLocaleString('id-ID')"></p>

                    <div class="mt-5 flex flex-wrap justify-end gap-2">
                        <button @click="approve(detail.id); closeDetail()" class="rounded-full bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-500">Setujui</button>
                        <button @click="closeDetail(); blockingId = detail.id; reason = ''" class="rounded-full border border-red-300 px-4 py-2 text-sm font-semibold text-red-700 hover:bg-red-50">Blokir</button>
                    </div>
                </div>
            </template>
        </div>
    </div>
</div>
