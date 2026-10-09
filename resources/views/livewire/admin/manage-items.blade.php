<div
    class="mx-auto max-w-5xl px-6 py-10"
    x-data="{
        base: '{{ url('/') }}',
        token: @js(session('api_token')),
        items: [],
        loading: true,
        notice: '',
        error: '',
        status: '',
        search: '',
        page: 1,
        lastPage: 1,
        badge(s) {
            return s === 'approved' ? 'bg-emerald-50 text-emerald-700' : (s === 'blocked' ? 'bg-red-50 text-red-700' : 'bg-amber-50 text-amber-700');
        },
        async load() {
            this.loading = true;
            this.error = '';
            const q = new URLSearchParams({ page: this.page, per_page: 15 });
            if (this.status) q.set('moderation_status', this.status);
            if (this.search.trim()) q.set('search', this.search.trim());
            const res = await fetch(`${this.base}/api/admin/items?${q.toString()}`, {
                headers: { 'Accept': 'application/json', 'Authorization': 'Bearer ' + this.token },
            });
            if (res.status === 401) { window.location.href = '{{ route('login') }}'; return; }
            const data = await res.json();
            this.items = data.data || [];
            this.page = data.meta ? data.meta.current_page : 1;
            this.lastPage = data.meta ? data.meta.last_page : 1;
            this.loading = false;
        },
        filter() { this.page = 1; this.load(); },
        async destroy(id) {
            if (! confirm('Hapus laporan ini permanen?')) return;
            this.notice = '';
            this.error = '';
            const res = await fetch(`${this.base}/api/admin/items/${id}`, {
                method: 'DELETE',
                headers: { 'Accept': 'application/json', 'Authorization': 'Bearer ' + this.token },
            });
            if (res.status === 401) { window.location.href = '{{ route('login') }}'; return; }
            if (res.ok) {
                this.notice = 'Laporan dihapus.';
                this.load();
            } else {
                const data = await res.json().catch(() => ({}));
                this.error = data.message || 'Gagal menghapus laporan.';
            }
        },
    }"
    x-init="load()"
>
    <div>
        <h1 class="text-2xl font-bold">Semua Laporan</h1>
        <p class="mt-1 text-sm text-gray-600">Lihat, saring, dan hapus laporan apa pun.</p>
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
</div>
