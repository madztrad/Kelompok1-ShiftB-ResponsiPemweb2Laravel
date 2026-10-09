{{-- Form lapor barang baru. Dikirim multipart ke POST /api/items. --}}
<div
    class="mx-auto max-w-2xl px-6 py-10"
    x-data="{
        categories: [],
        form: { title: '', description: '', type: 'lost', location: '', event_date: '', category_id: '', photo: null },
        errors: {},
        submitting: false,
        generalError: '',
        async loadCategories() {
            const res = await api.get('/categories');
            if (res.ok) { this.categories = res.data.data || []; }
        },
        fieldError(name) { return this.errors[name] ? this.errors[name][0] : ''; },
        async submit() {
            this.errors = {};
            this.generalError = '';
            this.submitting = true;
            const fd = new FormData();
            fd.append('title', this.form.title);
            fd.append('description', this.form.description);
            fd.append('type', this.form.type);
            fd.append('location', this.form.location);
            fd.append('event_date', this.form.event_date);
            fd.append('category_id', this.form.category_id);
            if (this.form.photo) fd.append('photo', this.form.photo);
            const res = await api.post('/items', fd);
            this.submitting = false;
            if (res.status === 201) {
                window.location.href = '/my/items';
            } else if (res.status === 422) {
                this.errors = res.data.errors || {};
                this.generalError = res.data.message || 'Periksa kembali input Anda.';
            } else {
                this.generalError = res.data.message || 'Gagal menyimpan laporan. Coba lagi.';
            }
        },
    }"
    x-init="loadCategories()"
>
    <a href="{{ route('home') }}" wire:navigate class="text-sm text-gray-500 hover:text-gray-900">&larr; Kembali ke beranda</a>

    <h1 class="mt-4 text-2xl font-bold">Lapor Barang</h1>
    <p class="mt-1 text-sm text-gray-600">Isi detail barang yang hilang atau ditemukan. Laporan akan diperiksa admin sebelum dipublikasikan.</p>

    <template x-if="generalError">
        <div class="mt-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700" x-text="generalError"></div>
    </template>

    <div class="mt-6 grid gap-4">
        <div>
            <label class="mb-1 block text-sm font-medium">Judul</label>
            <input x-model="form.title" maxlength="120" placeholder="Mis. Dompet kulit hitam" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm" />
            <p x-show="fieldError('title')" class="mt-1 text-xs text-red-600" x-text="fieldError('title')"></p>
        </div>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div>
                <label class="mb-1 block text-sm font-medium">Tipe</label>
                <select x-model="form.type" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                    <option value="lost">Barang hilang</option>
                    <option value="found">Barang temuan</option>
                </select>
                <p x-show="fieldError('type')" class="mt-1 text-xs text-red-600" x-text="fieldError('type')"></p>
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium">Kategori</label>
                <select x-model="form.category_id" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                    <option value="">Pilih kategori</option>
                    <template x-for="c in categories" :key="c.id">
                        <option :value="c.id" x-text="c.name"></option>
                    </template>
                </select>
                <p x-show="fieldError('category_id')" class="mt-1 text-xs text-red-600" x-text="fieldError('category_id')"></p>
            </div>
        </div>

        <div>
            <label class="mb-1 block text-sm font-medium">Lokasi</label>
            <input x-model="form.location" maxlength="150" placeholder="Mis. Kantin Gedung A" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm" />
            <p x-show="fieldError('location')" class="mt-1 text-xs text-red-600" x-text="fieldError('location')"></p>
        </div>

        <div>
            <label class="mb-1 block text-sm font-medium">Tanggal kejadian</label>
            <input x-model="form.event_date" type="date" max="{{ now()->toDateString() }}" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm" />
            <p x-show="fieldError('event_date')" class="mt-1 text-xs text-red-600" x-text="fieldError('event_date')"></p>
        </div>

        <div>
            <label class="mb-1 block text-sm font-medium">Deskripsi</label>
            <textarea x-model="form.description" rows="5" maxlength="2000" placeholder="Jelaskan ciri-ciri barang, waktu, dan kondisi saat ditemukan/dihilangkan." class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm"></textarea>
            <p x-show="fieldError('description')" class="mt-1 text-xs text-red-600" x-text="fieldError('description')"></p>
        </div>

        <div>
            <label class="mb-1 block text-sm font-medium">Foto <span class="font-normal text-gray-500">(jpg/png/webp, maks 2 MB)</span></label>
            <input type="file" accept="image/jpeg,image/png,image/webp" @change="form.photo = $event.target.files[0]" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm" />
            <p x-show="fieldError('photo')" class="mt-1 text-xs text-red-600" x-text="fieldError('photo')"></p>
        </div>
    </div>

    <div class="mt-6 flex justify-end gap-2">
        <a href="{{ route('home') }}" wire:navigate class="rounded-full border border-gray-300 px-5 py-2 text-sm hover:bg-gray-50">Batal</a>
        <button @click="submit()" :disabled="submitting" class="rounded-full bg-gray-900 px-5 py-2 text-sm font-semibold text-white hover:bg-gray-700 disabled:opacity-50">
            <span x-show="! submitting">Kirim Laporan</span>
            <span x-show="submitting">Mengirim...</span>
        </button>
    </div>
</div>
