<div
    class="mx-auto max-w-5xl px-6 py-10"
    x-data="{
        items: [],
        loading: true,
        notice: '',
        error: '',
        blockingId: null,
        reason: '',
        async load() {
            this.loading = true;
            this.error = '';
            const res = await api.get('/admin/items?moderation_status=pending&per_page=50');
            this.items = res.data.data || [];
            this.loading = false;
        },
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
</div>
