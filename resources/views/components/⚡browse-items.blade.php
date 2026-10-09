
<?php

use Livewire\Component;

new class extends Component
{
    //
};
?>

<div
    class="mx-auto max-w-7xl px-6 py-10"
    x-data="{
        items: [],
        categories: [],
        search: '',
        type: '',
        categoryId: '',
        loading: true,
        error: '',
        timer: null,

        async init() {
            await this.loadCategories();
            await this.loadItems();
        },

        async loadCategories() {
            try {
                const res = await fetch('/api/categories', {
                    headers: { 'Accept': 'application/json' }
                });
                const data = await res.json();
                if (res.ok) {
                    this.categories = data.data ?? [];
                }
            } catch (e) {
                console.error('Gagal memuat kategori', e);
            }
        },

        async loadItems() {
            this.loading = true;
            this.error = '';

            try {
                const params = new URLSearchParams();

                if (this.search.trim()) params.set('search', this.search.trim());
                if (this.type) params.set('type', this.type);
                if (this.categoryId) params.set('category_id', this.categoryId);

                const res = await fetch('/api/items?' + params.toString(), {
                    headers: { 'Accept': 'application/json' }
                });

                const data = await res.json();

                if (!res.ok) {
                    throw new Error(data.message || 'Gagal mengambil data barang.');
                }

                this.items = data.data ?? [];
            } catch (e) {
                this.error = e.message || 'Tidak bisa terhubung ke server.';
            } finally {
                this.loading = false;
            }
        },

        cariNanti() {
            clearTimeout(this.timer);
            this.timer = setTimeout(() => this.loadItems(), 350);
        }
    }"
>
    <div class="mb-8">
        <p class="text-sm font-semibold uppercase tracking-wider text-emerald-600">
            Lost & Found
        </p>
        <h1 class="mt-2 text-3xl font-bold text-gray-900">
            Katalog Barang
        </h1>
        <p class="mt-2 text-gray-600">
            Cari barang hilang atau temukan barang yang dilaporkan orang lain.
        </p>
    </div>

    <div class="mb-8 grid gap-4 rounded-2xl border border-gray-200 bg-white p-5 md:grid-cols-3">
        <div>
            <label for="search" class="mb-2 block text-sm font-medium text-gray-700">
                Cari barang
            </label>
            <input
                id="search"
                type="search"
                x-model="search"
                @input="cariNanti()"
                placeholder="Nama atau deskripsi barang..."
                class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-emerald-500 focus:outline-none"
            >
        </div>

        <div>
            <label for="type" class="mb-2 block text-sm font-medium text-gray-700">
                Jenis laporan
            </label>
            <select
                id="type"
                x-model="type"
                @change="loadItems()"
                class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm focus:border-emerald-500 focus:outline-none"
            >
                <option value="">Semua jenis</option>
                <option value="lost">Barang hilang</option>
                <option value="found">Barang ditemukan</option>
            </select>
        </div>

        <div>
            <label for="category" class="mb-2 block text-sm font-medium text-gray-700">
                Kategori
            </label>
            <select
                id="category"
                x-model="categoryId"
                @change="loadItems()"
                class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm focus:border-emerald-500 focus:outline-none"
            >
                <option value="">Semua kategori</option>
                <template x-for="category in categories" :key="category.id">
                    <option :value="category.id" x-text="category.name"></option>
                </template>
            </select>
        </div>
    </div>

    <div x-show="loading" class="py-12 text-center text-gray-500">
        Memuat data barang...
    </div>

    <div
        x-cloak
        x-show="error"
        class="mb-6 rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-700"
        x-text="error"
    ></div>

    <div
        x-cloak
        x-show="!loading && !error && items.length === 0"
        class="rounded-2xl border border-dashed border-gray-300 py-16 text-center"
    >
        <h2 class="text-lg font-semibold text-gray-800">Barang tidak ditemukan</h2>
        <p class="mt-2 text-sm text-gray-500">
            Coba gunakan kata kunci atau filter yang berbeda.
        </p>
    </div>

    <div x-cloak x-show="!loading && !error && items.length > 0"
         class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
        <template x-for="item in items" :key="item.id">
            <article class="overflow-hidden rounded-2xl border border-gray-200 bg-white transition hover:shadow-md">
                <div class="flex h-48 items-center justify-center bg-gray-100">
                    <template x-if="item.photo_url">
                        <img
                            :src="item.photo_url"
                            :alt="item.title"
                            class="h-full w-full object-cover"
                        >
                    </template>
                    <template x-if="!item.photo_url">
                        <span class="text-sm text-gray-400">Tidak ada foto</span>
                    </template>
                </div>

                <div class="p-5">
                    <div class="mb-3 flex flex-wrap gap-2">
                        <span
                            class="rounded-full px-3 py-1 text-xs font-semibold"
                            :class="item.type === 'lost'
                                ? 'bg-red-100 text-red-700'
                                : 'bg-emerald-100 text-emerald-700'"
                            x-text="item.type === 'lost' ? 'Barang hilang' : 'Barang ditemukan'"
                        ></span>

                        <span
                            x-show="item.category"
                            class="rounded-full bg-gray-100 px-3 py-1 text-xs text-gray-600"
                            x-text="item.category?.name"
                        ></span>
                    </div>

                    <h2 class="text-lg font-bold text-gray-900" x-text="item.title"></h2>

                    <p
                        class="mt-2 text-sm leading-6 text-gray-600"
                        x-text="item.description || 'Tidak ada deskripsi.'"
                    ></p>

                    <div class="mt-4 space-y-2 text-sm text-gray-500">
                        <p>
                            <span class="font-medium">Lokasi:</span>
                            <span x-text="item.location || '-'"></span>
                        </p>
                        <p>
                            <span class="font-medium">Tanggal:</span>
                            <span x-text="item.event_date || '-'"></span>
                        </p>
                    </div>
                </div>
            </article>
        </template>
    </div>
</div>