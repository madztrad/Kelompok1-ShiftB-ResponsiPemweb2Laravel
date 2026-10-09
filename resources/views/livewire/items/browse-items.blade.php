{{-- Katalog barang publik: menelusuri laporan yang sudah disetujui admin.
     Data diambil dari API via helper window.api (Bearer token dari session). --}}
<div
    class="mx-auto max-w-6xl px-6 py-10"
    x-data="{
        items: [],
        categories: [],
        search: '',
        type: '',
        categoryId: '',
        page: 1,
        lastPage: 1,
        total: 0,
        loading: true,
        error: '',
        searchTimer: null,
        async loadCategories() {
            const res = await api.get('/categories');
            if (res.ok) { this.categories = res.data.data || []; }
        },
        async load() {
            this.loading = true;
            this.error = '';
            const q = new URLSearchParams({ page: this.page, per_page: 12 });
            if (this.search.trim()) q.set('search', this.search.trim());
            if (this.type) q.set('type', this.type);
            if (this.categoryId) q.set('category_id', this.categoryId);
            const res = await api.get(`/items?${q.toString()}`);
            if (res.ok) {
                this.items = res.data.data || [];
                this.page = res.data.meta ? res.data.meta.current_page : 1;
                this.lastPage = res.data.meta ? res.data.meta.last_page : 1;
                this.total = res.data.meta ? res.data.meta.total : this.items.length;
            } else {
                this.items = [];
                this.error = 'Tidak bisa memuat katalog. Coba muat ulang.';
            }
            this.loading = false;
        },
        filter() { this.page = 1; this.load(); },
        searchDebounced() {
            clearTimeout(this.searchTimer);
            this.searchTimer = setTimeout(() => this.filter(), 350);
        },
        reset() { this.search = ''; this.type = ''; this.categoryId = ''; this.filter(); },
        goTo(target) {
            if (target < 1 || target > this.lastPage || target === this.page) return;
            this.page = target;
            this.load();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        },
        typeLabel(t) { return t === 'found' ? 'Temuan' : 'Hilang'; },
        typeBadge(t) { return t === 'found' ? 'bg-emerald-50 text-emerald-700' : 'bg-red-50 text-red-700'; },
    }"
    x-init="load(); loadCategories()"
>
    <div class="flex flex-wrap items-end justify-between gap-2">
        <div>
            <h1 class="text-2xl font-bold">Katalog Barang</h1>
            <p class="mt-1 text-sm text-gray-600">Telusuri laporan barang hilang dan temuan yang sudah disetujui.</p>
        </div>
        <p class="text-xs text-gray-400" x-show="! loading" x-cloak><span x-text="total"></span> laporan</p>
    </div>

    {{-- Filter --}}
    <div class="mt-6 flex flex-col gap-2 sm:flex-row sm:flex-wrap sm:items-center">
        <input
            x-model="search"
            @input="searchDebounced()"
            placeholder="Cari judul / lokasi..."
            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm sm:w-auto sm:min-w-64 sm:flex-1"
        />
        <select x-model="type" @change="filter()" class="rounded-lg border border-gray-300 px-3 py-2 text-sm">
            <option value="">Semua tipe</option>
            <option value="lost">Barang hilang</option>
            <option value="found">Barang temuan</option>
        </select>
        <select x-model="categoryId" @change="filter()" class="rounded-lg border border-gray-300 px-3 py-2 text-sm">
            <option value="">Semua kategori</option>
            <template x-for="c in categories" :key="c.id">
                <option :value="c.id" x-text="c.name"></option>
            </template>
        </select>
        <button @click="reset()" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">Reset</button>
    </div>

    <template x-if="error">
        <div class="mt-6 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700" x-text="error"></div>
    </template>

    <div x-show="loading" class="mt-6 text-sm text-gray-500">Memuat katalog...</div>

    {{-- Grid kartu --}}
    <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-3" x-show="! loading && items.length > 0" x-cloak>
        <template x-for="item in items" :key="item.id">
            <article class="overflow-hidden rounded-2xl border border-gray-200 bg-white">
                <template x-if="item.photo_url">
                    <img :src="item.photo_url" :alt="item.title" class="h-44 w-full object-cover">
                </template>
                <template x-if="! item.photo_url">
                    <div class="flex h-44 w-full items-center justify-center bg-gray-50 text-xs text-gray-400">Tanpa foto</div>
                </template>

                <div class="p-5">
                    <div class="flex items-center gap-2">
                        <span class="inline-block rounded-full px-3 py-1 text-xs font-medium" :class="typeBadge(item.type)" x-text="typeLabel(item.type)"></span>
                        <span x-show="item.is_resolved" x-cloak class="inline-block rounded-full bg-gray-100 px-3 py-1 text-xs font-medium text-gray-600">Selesai</span>
                    </div>
                    <h2 class="mt-2 font-semibold" x-text="item.title"></h2>
                    <p class="mt-1 text-sm text-gray-600" x-text="item.category ? item.category.name : 'Tanpa kategori'"></p>

                    <dl class="mt-3 space-y-1 text-xs text-gray-500">
                        <div class="flex gap-1">
                            <dt class="font-medium">Lokasi:</dt>
                            <dd x-text="item.location || '-'"></dd>
                        </div>
                        <div class="flex gap-1">
                            <dt class="font-medium">Tanggal:</dt>
                            <dd x-text="item.event_date || '-'"></dd>
                        </div>
                    </dl>
                </div>
            </article>
        </template>
    </div>

    {{-- Kosong --}}
    <p class="mt-6 rounded-xl border border-dashed border-gray-300 py-14 text-center text-sm text-gray-500" x-show="! loading && ! error && items.length === 0" x-cloak>
        Tidak ada laporan yang cocok dengan pencarian.
    </p>

    {{-- Pagination --}}
    <div class="mt-6 flex items-center justify-between text-sm" x-show="! loading && lastPage > 1" x-cloak>
        <button @click="goTo(page - 1)" :disabled="page <= 1" class="rounded-full border border-gray-300 px-4 py-2 disabled:opacity-40">Sebelumnya</button>
        <span class="text-gray-600">Halaman <span x-text="page"></span> dari <span x-text="lastPage"></span></span>
        <button @click="goTo(page + 1)" :disabled="page >= lastPage" class="rounded-full border border-gray-300 px-4 py-2 disabled:opacity-40">Berikutnya</button>
    </div>
</div>
