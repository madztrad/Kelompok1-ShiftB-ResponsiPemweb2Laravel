<div
    class="mx-auto max-w-6xl px-6 py-10"
    x-data="{
        loading: true,
        error: '',
        updatedAt: '',
        cards: { pending: null, approved: null, blocked: null, users: null },
        recent: [],
        trend: [],
        async load() {
            this.loading = true;
            this.error = '';
            try {
                // 7 hari terakhir (termasuk hari ini) sebagai deret tanggal YYYY-MM-DD.
                const days = [...Array(7)].map((_, i) => {
                    const d = new Date();
                    d.setDate(d.getDate() - (6 - i));
                    return d.toISOString().slice(0, 10);
                });

                // Kartu ringkasan: hitung total lewat meta.total dari permintaan 1 item.
                const [pending, approved, blocked, users, recentRes] = await Promise.all([
                    api.get('/admin/items?moderation_status=pending&per_page=1'),
                    api.get('/admin/items?moderation_status=approved&per_page=1'),
                    api.get('/admin/items?moderation_status=blocked&per_page=1'),
                    api.get('/admin/users?n=1'),
                    api.get('/admin/items?moderation_status=pending&per_page=5'),
                ]);
                const trendRes = await Promise.all(
                    days.map((d) => api.get(`/admin/items?date_from=${d}&date_to=${d}&per_page=1`)),
                );

                this.cards.pending = pending.data.meta?.total ?? 0;
                this.cards.approved = approved.data.meta?.total ?? 0;
                this.cards.blocked = blocked.data.meta?.total ?? 0;
                this.cards.users = users.data.meta?.total ?? 0;
                this.recent = recentRes.data.data ?? [];

                const counts = trendRes.map((res) => res.data.meta?.total ?? 0);
                const max = Math.max(1, ...counts);
                this.trend = days.map((d, i) => ({
                    date: d,
                    label: new Date(d).toLocaleDateString('id-ID', { day: 'numeric', month: 'short' }),
                    count: counts[i],
                    pct: Math.round((counts[i] / max) * 100),
                }));
                this.updatedAt = new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
            } catch (e) {
                this.error = 'Tidak bisa memuat ringkasan. Coba muat ulang.';
            } finally {
                this.loading = false;
            }
        },
    }"
    x-init="load()"
>
    <div class="flex flex-wrap items-end justify-between gap-2">
        <div>
            <h1 class="text-2xl font-bold">Dashboard Admin</h1>
            <p class="mt-1 text-sm text-gray-600">Ringkasan laporan dan pengguna Lost &amp; Found.</p>
        </div>
        <p class="text-xs text-gray-400" x-show="updatedAt" x-cloak>Diperbarui <span x-text="updatedAt"></span></p>
    </div>

    <template x-if="error">
        <div class="mt-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700" x-text="error"></div>
    </template>

    <div x-show="loading" class="mt-6 text-sm text-gray-500">Memuat ringkasan...</div>

    {{-- Kartu ringkasan --}}
    <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4" x-show="! loading" x-cloak>
        <a href="{{ route('admin.moderation') }}" class="group rounded-xl border border-amber-200 bg-amber-50 p-5 transition hover:border-amber-300 hover:shadow-sm">
            <div class="flex items-center justify-between">
                <p class="text-sm font-medium text-amber-700">Menunggu moderasi</p>
                <span class="text-lg">⏳</span>
            </div>
            <p class="mt-2 text-3xl font-bold text-amber-800" x-text="cards.pending ?? '—'"></p>
            <p class="mt-1 text-xs text-amber-600 group-hover:underline">Tinjau sekarang →</p>
        </a>
        <a href="{{ route('admin.items') }}" class="group rounded-xl border border-emerald-200 bg-emerald-50 p-5 transition hover:border-emerald-300 hover:shadow-sm">
            <div class="flex items-center justify-between">
                <p class="text-sm font-medium text-emerald-700">Laporan disetujui</p>
                <span class="text-lg">✅</span>
            </div>
            <p class="mt-2 text-3xl font-bold text-emerald-800" x-text="cards.approved ?? '—'"></p>
            <p class="mt-1 text-xs text-emerald-600 group-hover:underline">Lihat semua →</p>
        </a>
        <a href="{{ route('admin.items') }}" class="group rounded-xl border border-red-200 bg-red-50 p-5 transition hover:border-red-300 hover:shadow-sm">
            <div class="flex items-center justify-between">
                <p class="text-sm font-medium text-red-700">Laporan diblokir</p>
                <span class="text-lg">🚫</span>
            </div>
            <p class="mt-2 text-3xl font-bold text-red-800" x-text="cards.blocked ?? '—'"></p>
            <p class="mt-1 text-xs text-red-600 group-hover:underline">Lihat semua →</p>
        </a>
        <a href="{{ route('admin.users') }}" class="group rounded-xl border border-gray-200 bg-gray-50 p-5 transition hover:border-gray-300 hover:shadow-sm">
            <div class="flex items-center justify-between">
                <p class="text-sm font-medium text-gray-700">Total pengguna</p>
                <span class="text-lg">👥</span>
            </div>
            <p class="mt-2 text-3xl font-bold text-gray-900" x-text="cards.users ?? '—'"></p>
            <p class="mt-1 text-xs text-gray-500 group-hover:underline">Kelola →</p>
        </a>
    </div>

    <div class="mt-6 grid gap-6 lg:grid-cols-5" x-show="! loading" x-cloak>
        {{-- Grafik tren 7 hari --}}
        <div class="rounded-xl border border-gray-200 p-5 lg:col-span-2">
            <h2 class="text-sm font-semibold text-gray-900">Laporan masuk — 7 hari terakhir</h2>
            <p class="mt-1 text-xs text-gray-500">Jumlah laporan dibuat per hari.</p>
            <div class="mt-5 flex h-40 items-end gap-2">
                <template x-for="bar in trend" :key="bar.date">
                    <div class="flex flex-1 flex-col items-center gap-1">
                        <span class="text-xs font-semibold text-gray-700" x-text="bar.count"></span>
                        <div class="flex w-full flex-1 items-end">
                            <div class="w-full rounded-t bg-indigo-500" :style="`height: ${Math.max(bar.pct, 2)}%`" :title="`${bar.count} laporan`"></div>
                        </div>
                        <span class="text-[10px] text-gray-400" x-text="bar.label"></span>
                    </div>
                </template>
            </div>
        </div>

        {{-- Laporan terbaru menunggu moderasi --}}
        <div class="rounded-xl border border-gray-200 p-5 lg:col-span-3">
            <div class="flex items-center justify-between">
                <h2 class="text-sm font-semibold text-gray-900">Menunggu moderasi terbaru</h2>
                <a href="{{ route('admin.moderation') }}" class="text-xs text-indigo-600 hover:underline">Lihat semua →</a>
            </div>
            <template x-if="recent.length === 0">
                <p class="mt-4 text-sm text-gray-500">Tidak ada laporan yang menunggu moderasi. 🎉</p>
            </template>
            <div class="mt-3 divide-y divide-gray-100">
                <template x-for="item in recent" :key="item.id">
                    <div class="flex items-center justify-between gap-4 py-3">
                        <div class="min-w-0">
                            <p class="truncate text-sm font-medium text-gray-900" x-text="item.title"></p>
                            <p class="mt-0.5 text-xs text-gray-500">
                                <span x-text="item.type === 'lost' ? 'Hilang' : 'Temuan'"></span>
                                · <span x-text="item.reporter?.name ?? 'Tanpa nama'"></span>
                                · <span x-text="item.category?.name ?? 'Tanpa kategori'"></span>
                            </p>
                        </div>
                        <span class="shrink-0 text-xs text-gray-400" x-text="item.created_at ? new Date(item.created_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' }) : ''"></span>
                    </div>
                </template>
            </div>
        </div>
    </div>
</div>
