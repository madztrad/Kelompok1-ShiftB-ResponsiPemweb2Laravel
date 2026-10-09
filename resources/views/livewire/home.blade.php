{{-- Livewire butuh satu elemen root, karena itu dibungkus <div>. --}}
<div>
    @if (session('api_token'))
        {{-- ============================== DASHBOARD USER ============================== --}}
        <div
            class="mx-auto max-w-6xl px-6 py-10"
            x-data="{
                loading: true,
                error: '',
                stats: { items: null, pending: null, claims: null, accepted: null },
                recent: [],
                async load() {
                    this.loading = true;
                    this.error = '';
                    try {
                        const [items, pending, claims, accepted, latest] = await Promise.all([
                            api.get('/my/items?per_page=1'),
                            api.get('/my/items?moderation_status=pending&per_page=1'),
                            api.get('/my/claims?per_page=1'),
                            api.get('/my/claims?status=accepted&per_page=1'),
                            api.get('/items?per_page=6'),
                        ]);

                        this.stats.items = items.data.meta?.total ?? 0;
                        this.stats.pending = pending.data.meta?.total ?? 0;
                        this.stats.claims = claims.data.meta?.total ?? 0;
                        this.stats.accepted = accepted.data.meta?.total ?? 0;
                        this.recent = latest.data.data ?? [];
                    } catch (e) {
                        this.error = 'Tidak bisa memuat ringkasan. Coba muat ulang.';
                    } finally {
                        this.loading = false;
                    }
                },
            }"
            x-init="load()"
        >
            {{-- Sapaan --}}
            <div>
                <h1 class="text-2xl font-bold">Halo, {{ session('user.name') }}</h1>
                <p class="mt-1 text-sm text-gray-600">Ringkasan aktivitas laporanmu di Lost &amp; Found.</p>
            </div>

            <template x-if="error">
                <div class="mt-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700" x-text="error"></div>
            </template>

            <div x-show="loading" class="mt-6 text-sm text-gray-500">Memuat ringkasan...</div>

            {{-- Kartu statistik --}}
            <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4" x-show="! loading" x-cloak>
                <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-5">
                    <p class="text-sm font-medium text-emerald-700">Laporan Saya</p>
                    <p class="mt-2 text-3xl font-bold text-emerald-800" x-text="stats.items ?? '—'"></p>
                </div>

                <div class="rounded-xl border border-amber-200 bg-amber-50 p-5">
                    <p class="text-sm font-medium text-amber-700">Menunggu Moderasi</p>
                    <p class="mt-2 text-3xl font-bold text-amber-800" x-text="stats.pending ?? '—'"></p>
                </div>

                <div class="rounded-xl border border-indigo-200 bg-indigo-50 p-5">
                    <p class="text-sm font-medium text-indigo-700">Klaim Saya</p>
                    <p class="mt-2 text-3xl font-bold text-indigo-800" x-text="stats.claims ?? '—'"></p>
                </div>

                <div class="rounded-xl border border-sky-200 bg-sky-50 p-5">
                    <p class="text-sm font-medium text-sky-700">Klaim Diterima</p>
                    <p class="mt-2 text-3xl font-bold text-sky-800" x-text="stats.accepted ?? '—'"></p>
                </div>
            </div>

            {{-- Aksi cepat (belum aktif sampai halaman tujuannya dibuat) --}}
            <div class="mt-8" x-show="! loading" x-cloak>
                <h2 class="text-sm font-semibold text-gray-900">Aksi Cepat</h2>
                <div class="mt-3 grid gap-3 sm:grid-cols-3">
                    <button type="button" disabled title="Segera hadir" class="flex cursor-not-allowed items-center justify-between rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-left opacity-70">
                        <span class="text-sm font-medium text-gray-700">Lapor Barang</span>
                        <span class="rounded-full bg-gray-200 px-2 py-0.5 text-[10px] font-semibold uppercase tracking-wide text-gray-500">Segera hadir</span>
                    </button>
                    <button type="button" disabled title="Segera hadir" class="flex cursor-not-allowed items-center justify-between rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-left opacity-70">
                        <span class="text-sm font-medium text-gray-700">Laporan Saya</span>
                        <span class="rounded-full bg-gray-200 px-2 py-0.5 text-[10px] font-semibold uppercase tracking-wide text-gray-500">Segera hadir</span>
                    </button>
                    <button type="button" disabled title="Segera hadir" class="flex cursor-not-allowed items-center justify-between rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-left opacity-70">
                        <span class="text-sm font-medium text-gray-700">Klaim Saya</span>
                        <span class="rounded-full bg-gray-200 px-2 py-0.5 text-[10px] font-semibold uppercase tracking-wide text-gray-500">Segera hadir</span>
                    </button>
                </div>
            </div>

            {{-- Laporan approved terbaru --}}
            <div class="mt-8">
                <div class="flex flex-wrap items-end justify-between gap-2">
                    <div>
                        <h2 class="text-sm font-semibold text-gray-900">Laporan Terbaru</h2>
                        <p class="text-xs text-gray-400">Barang yang sudah disetujui admin.</p>
                    </div>
                    <a href="{{ route('items.index') }}" wire:navigate class="text-xs font-medium text-indigo-600 hover:underline">Lihat semua →</a>
                </div>

                <div class="mt-4 grid gap-4 sm:grid-cols-2 lg:grid-cols-3" x-show="! loading && recent.length > 0" x-cloak>
                    <template x-for="item in recent" :key="item.id">
                        <article class="overflow-hidden rounded-2xl border border-gray-200 bg-white">
                            <template x-if="item.photo_url">
                                <img :src="item.photo_url" :alt="item.title" class="h-40 w-full object-cover">
                            </template>
                            <template x-if="! item.photo_url">
                                <div class="flex h-40 w-full items-center justify-center bg-gray-50 text-4xl">📦</div>
                            </template>

                            <div class="p-5">
                                <span
                                    class="inline-block rounded-full px-3 py-1 text-xs font-medium"
                                    :class="item.type === 'lost' ? 'bg-red-50 text-red-700' : 'bg-emerald-50 text-emerald-700'"
                                    x-text="item.type === 'lost' ? 'Hilang' : 'Temuan'"
                                ></span>
                                <h3 class="mt-2 font-semibold" x-text="item.title"></h3>
                                <p class="mt-1 text-sm text-gray-600" x-text="item.category?.name ?? 'Tanpa kategori'"></p>

                                <div class="mt-3 space-y-1 text-xs text-gray-500">
                                    <p>📍 <span x-text="item.location || '-'"></span></p>
                                    <p>🗓️ <span x-text="item.event_date || '-'"></span></p>
                                </div>
                            </div>
                        </article>
                    </template>
                </div>

                <p class="mt-4 rounded-xl border border-dashed border-gray-300 py-10 text-center text-sm text-gray-500" x-show="! loading && recent.length === 0" x-cloak>
                    Belum ada laporan yang dipublikasikan.
                </p>
            </div>
        </div>
    @else
        {{-- ============================== LANDING TAMU ============================== --}}
        @include('pages.partials.guest-landing')
    @endif
</div>
