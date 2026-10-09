{{-- Detail laporan barang + form pengajuan klaim.
     Data diambil dari API via helper window.api (Bearer token dari session). --}}
<div
    class="mx-auto max-w-4xl px-6 py-10"
    x-data="{
        itemId: {{ $itemId }},
        viewerId: @js(session('user.id')),
        isAdmin: @js(session('user.role') === 'admin'),
        loggedIn: @js((bool) session('api_token')),
        item: null,
        loading: true,
        notFound: false,
        error: '',
        claimMessage: '',
        claimSubmitting: false,
        claimError: '',
        claimSuccess: '',
        async load() {
            this.loading = true;
            this.error = '';
            this.notFound = false;
            const res = await api.get(`/items/${this.itemId}`);
            if (res.status === 404) {
                this.notFound = true;
            } else if (res.ok) {
                this.item = res.data.data;
            } else {
                this.error = 'Tidak bisa memuat detail barang. Coba muat ulang.';
            }
            this.loading = false;
        },
        get isOwner() {
            return !!(this.item && this.item.reporter && this.viewerId && this.item.reporter.id === this.viewerId);
        },
        get canClaim() {
            return this.loggedIn && this.item && ! this.isOwner
                && this.item.moderation_status === 'approved'
                && ! this.item.is_resolved
                && ! this.claimSuccess;
        },
        get claimNotice() {
            if (! this.item) return '';
            if (this.item.is_resolved) return 'Barang ini sudah dikembalikan, klaim ditutup.';
            if (this.item.moderation_status !== 'approved') return 'Laporan ini belum disetujui admin.';

            return '';
        },
        typeLabel(t) { return t === 'found' ? 'Temuan' : 'Hilang'; },
        typeBadge(t) { return t === 'found' ? 'bg-emerald-50 text-emerald-700' : 'bg-red-50 text-red-700'; },
        statusLabel(s) { return s === 'approved' ? 'Disetujui' : (s === 'blocked' ? 'Diblokir' : 'Menunggu'); },
        statusBadge(s) { return s === 'approved' ? 'bg-emerald-50 text-emerald-700' : (s === 'blocked' ? 'bg-red-50 text-red-700' : 'bg-amber-50 text-amber-700'); },
        async submitClaim() {
            this.claimError = '';
            this.claimSuccess = '';
            if (this.claimMessage.trim().length < 10) {
                this.claimError = 'Pesan minimal 10 karakter.';
                return;
            }
            this.claimSubmitting = true;
            const res = await api.post(`/items/${this.itemId}/claims`, { message: this.claimMessage.trim() });
            this.claimSubmitting = false;
            if (res.status === 201) {
                this.claimSuccess = 'Klaim diajukan. Menunggu keputusan pemilik laporan.';
                this.claimMessage = '';
            } else if (res.status === 422) {
                this.claimError = (res.data.errors && res.data.errors.message && res.data.errors.message[0]) || 'Periksa pesan klaim.';
            } else if (res.status === 403 || res.status === 404 || res.status === 409) {
                this.claimError = res.data.message || 'Klaim tidak dapat diajukan.';
            } else {
                this.claimError = 'Gagal mengajukan klaim. Coba lagi.';
            }
        },
    }"
    x-init="load()"
>
    <a href="{{ route('items.index') }}" wire:navigate class="text-sm text-gray-500 hover:text-gray-900">&larr; Kembali ke katalog</a>

    <div x-show="loading" class="mt-6 text-sm text-gray-500">Memuat detail...</div>

    <template x-if="notFound">
        <div class="mt-6 rounded-xl border border-dashed border-gray-300 py-14 text-center text-sm text-gray-500">
            Barang tidak ditemukan atau belum dipublikasikan.
        </div>
    </template>

    <template x-if="error">
        <div class="mt-6 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700" x-text="error"></div>
    </template>

    <div x-show="item && ! loading" x-cloak>
        <div class="mt-6 grid gap-6 md:grid-cols-2">
            {{-- Foto --}}
            <div>
                <template x-if="item && item.photo_url">
                    <img :src="item.photo_url" :alt="item.title" class="h-72 w-full rounded-2xl border border-gray-200 object-cover">
                </template>
                <template x-if="item && ! item.photo_url">
                    <div class="flex h-72 w-full items-center justify-center rounded-2xl border border-gray-200 bg-gray-50 text-xs text-gray-400">Tanpa foto</div>
                </template>
            </div>

            {{-- Info --}}
            <div>
                <div class="flex flex-wrap items-center gap-2">
                    <span class="inline-block rounded-full px-3 py-1 text-xs font-medium" :class="typeBadge(item.type)" x-text="typeLabel(item.type)"></span>
                    <span x-show="item.is_resolved" x-cloak class="inline-block rounded-full bg-gray-100 px-3 py-1 text-xs font-medium text-gray-600">Selesai</span>
                    <template x-if="isOwner || isAdmin">
                        <span class="inline-block rounded-full px-3 py-1 text-xs font-medium" :class="statusBadge(item.moderation_status)" x-text="statusLabel(item.moderation_status)"></span>
                    </template>
                </div>

                <h1 class="mt-3 text-2xl font-bold" x-text="item.title"></h1>

                <dl class="mt-4 grid grid-cols-3 gap-x-3 gap-y-2 text-sm">
                    <dt class="text-gray-500">Kategori</dt>
                    <dd class="col-span-2" x-text="item.category ? item.category.name : '-'"></dd>

                    <dt class="text-gray-500">Lokasi</dt>
                    <dd class="col-span-2" x-text="item.location || '-'"></dd>

                    <dt class="text-gray-500">Tanggal</dt>
                    <dd class="col-span-2" x-text="item.event_date || '-'"></dd>

                    <dt class="text-gray-500">Pelapor</dt>
                    <dd class="col-span-2" x-text="item.reporter ? item.reporter.name : '-'"></dd>

                    <dt class="text-gray-500">Klaim masuk</dt>
                    <dd class="col-span-2" x-text="item.claims_count ?? 0"></dd>
                </dl>
            </div>
        </div>

        <div class="mt-6">
            <h2 class="text-sm font-semibold text-gray-900">Deskripsi</h2>
            <p class="mt-2 whitespace-pre-line text-sm leading-6 text-gray-800" x-text="item.description || '-'"></p>
        </div>

        <template x-if="isOwner && item.blocked_reason">
            <div class="mt-6 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                Alasan diblokir: <span x-text="item.blocked_reason"></span>
            </div>
        </template>

        {{-- Area klaim --}}
        <div class="mt-8 rounded-2xl border border-gray-200 p-6">
            <h2 class="text-sm font-semibold text-gray-900">Klaim barang ini</h2>

            {{-- Tamu --}}
            <div x-show="! loggedIn" class="mt-3">
                <p class="text-sm text-gray-600">Masuk terlebih dahulu untuk mengajukan klaim dan bukti kepemilikan.</p>
                <a href="{{ route('login') }}" class="mt-3 inline-block rounded-full bg-gray-900 px-5 py-2 text-sm font-semibold text-white hover:bg-gray-700">Masuk untuk klaim</a>
            </div>

            {{-- Pemilik --}}
            <p x-show="loggedIn && isOwner" x-cloak class="mt-3 text-sm text-gray-600">Ini laporan Anda. Klaim dari pengguna lain dapat Anda putuskan di halaman Laporan Saya.</p>

            {{-- Bukan pemilik, tidak bisa klaim (resolved / belum approved) --}}
            <p x-show="loggedIn && ! isOwner && ! canClaim && ! claimSuccess && claimNotice" x-cloak class="mt-3 text-sm text-gray-600" x-text="claimNotice"></p>

            {{-- Form klaim --}}
            <div x-show="canClaim" x-cloak class="mt-3">
                <label class="mb-1 block text-sm font-medium" for="claim-message">Pesan klaim / ciri kepemilikan</label>
                <textarea id="claim-message" x-model="claimMessage" rows="4" maxlength="1000" placeholder="Jelaskan ciri kepemilikan atau bukti bahwa barang ini milik Anda (min. 10 karakter)." class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm"></textarea>
                <button @click="submitClaim()" :disabled="claimSubmitting" class="mt-3 rounded-full bg-gray-900 px-5 py-2 text-sm font-semibold text-white hover:bg-gray-700 disabled:opacity-50">
                    <span x-show="! claimSubmitting">Ajukan Klaim</span>
                    <span x-show="claimSubmitting">Mengirim...</span>
                </button>
            </div>

            <template x-if="claimError">
                <div class="mt-3 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700" x-text="claimError"></div>
            </template>
            <template x-if="claimSuccess">
                <div class="mt-3 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700" x-text="claimSuccess"></div>
            </template>
        </div>
    </div>
</div>
