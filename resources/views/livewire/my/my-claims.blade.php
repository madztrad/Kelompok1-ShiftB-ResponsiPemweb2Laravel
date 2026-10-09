{{-- Klaim yang diajukan pengguna beserta statusnya. --}}
<div
    class="mx-auto max-w-4xl px-6 py-10"
    x-data="{
        claims: [],
        loading: true,
        error: '',
        status: '',
        page: 1,
        lastPage: 1,
        claimBadge(s) { return s === 'accepted' ? 'bg-emerald-50 text-emerald-700' : (s === 'rejected' ? 'bg-red-50 text-red-700' : 'bg-amber-50 text-amber-700'); },
        claimLabel(s) { return s === 'accepted' ? 'Diterima' : (s === 'rejected' ? 'Ditolak' : 'Menunggu'); },
        formatDate(v) { return v ? new Date(v).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' }) : '-'; },
        async load() {
            this.loading = true;
            this.error = '';
            const q = new URLSearchParams({ page: this.page, per_page: 10 });
            if (this.status) q.set('status', this.status);
            const res = await api.get(`/my/claims?${q.toString()}`);
            if (res.ok) {
                this.claims = res.data.data || [];
                this.page = res.data.meta ? res.data.meta.current_page : 1;
                this.lastPage = res.data.meta ? res.data.meta.last_page : 1;
            } else {
                this.claims = [];
                this.error = 'Tidak bisa memuat klaim Anda. Coba muat ulang.';
            }
            this.loading = false;
        },
        filter() { this.page = 1; this.load(); },
    }"
    x-init="load()"
>
    <div class="flex flex-wrap items-start justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold">Klaim Saya</h1>
            <p class="mt-1 text-sm text-gray-600">Pantau status klaim yang Anda ajukan atas barang temuan.</p>
        </div>
        <a href="{{ route('items.index') }}" wire:navigate class="rounded-full border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-100">Jelajahi katalog</a>
    </div>

    <template x-if="error">
        <div class="mt-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700" x-text="error"></div>
    </template>

    <div class="mt-4">
        <select x-model="status" @change="filter()" class="rounded-lg border border-gray-300 px-3 py-2 text-sm">
            <option value="">Semua status</option>
            <option value="pending">Menunggu</option>
            <option value="accepted">Diterima</option>
            <option value="rejected">Ditolak</option>
        </select>
    </div>

    <div x-show="loading" class="mt-6 text-sm text-gray-500">Memuat...</div>

    <div class="mt-4 space-y-3" x-show="! loading" x-cloak>
        <template x-for="claim in claims" :key="claim.id">
            <div class="rounded-xl border border-gray-200 p-4">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <a :href="'/items/' + (claim.item ? claim.item.id : claim.item_id)" class="text-sm font-semibold text-indigo-600 hover:underline" x-text="claim.item ? claim.item.title : 'Barang #' + claim.item_id"></a>
                    <span class="rounded-full px-2.5 py-1 text-xs font-medium" :class="claimBadge(claim.status)" x-text="claimLabel(claim.status)"></span>
                </div>
                <p class="mt-2 whitespace-pre-line text-sm text-gray-700" x-text="claim.message"></p>
                <p class="mt-2 text-xs text-gray-500">
                    Diajukan <span x-text="formatDate(claim.created_at)"></span>
                    <template x-if="claim.reviewed_at">
                        <span> &middot; Ditinjau <span x-text="formatDate(claim.reviewed_at)"></span></span>
                    </template>
                </p>
            </div>
        </template>
        <template x-if="claims.length === 0">
            <p class="rounded-lg border border-dashed border-gray-300 px-4 py-8 text-center text-sm text-gray-500">Belum ada klaim. Klaim dapat diajukan dari halaman detail barang temuan.</p>
        </template>
    </div>

    <div class="mt-4 flex items-center justify-between text-sm" x-show="! loading && lastPage > 1" x-cloak>
        <button @click="page--; load()" :disabled="page <= 1" class="rounded-full border border-gray-300 px-4 py-2 disabled:opacity-40">Sebelumnya</button>
        <span class="text-gray-600">Halaman <span x-text="page"></span> dari <span x-text="lastPage"></span></span>
        <button @click="page++; load()" :disabled="page >= lastPage" class="rounded-full border border-gray-300 px-4 py-2 disabled:opacity-40">Berikutnya</button>
    </div>
</div>
