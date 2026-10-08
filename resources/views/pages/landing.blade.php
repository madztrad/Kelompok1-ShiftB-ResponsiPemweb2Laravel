<x-layouts.app title="Beranda">
    {{-- Hero --}}
    <section class="mx-auto max-w-5xl px-6 py-20 text-center">
        <span class="inline-block rounded-full bg-emerald-50 px-3 py-1 text-xs font-medium text-emerald-700">
            Platform barang hilang &amp; temuan
        </span>

        <h1 class="mt-6 text-4xl font-bold tracking-tight sm:text-5xl">
            Hilang bukan berarti <span class="text-emerald-600">selesai.</span>
        </h1>

        <p class="mx-auto mt-4 max-w-xl leading-7 text-gray-600">
            Laporkan barang hilangmu atau barang temuanmu. Bantu orang lain menemukan barang mereka.
        </p>

        <div class="mt-8 flex flex-wrap justify-center gap-3">
            <a href="#" class="rounded-full bg-gray-900 px-6 py-3 text-sm font-semibold text-white hover:bg-gray-700">
                Lapor Barang
            </a>
            <a href="#cara-kerja" class="rounded-full border border-gray-300 px-6 py-3 text-sm font-semibold text-gray-700 hover:bg-gray-50">
                Lihat Cara Kerja
            </a>
        </div>

        <div class="mt-8 flex flex-wrap justify-center gap-x-6 gap-y-2 text-sm text-gray-500">
            <span>✅ Gratis selamanya</span>
            <span>✅ Laporan terverifikasi</span>
            <span>✅ Data kamu aman</span>
        </div>
    </section>

    {{-- Cara Kerja --}}
    <section id="cara-kerja" class="border-t border-gray-200 bg-gray-50">
        <div class="mx-auto max-w-5xl px-6 py-16">
            <h2 class="text-center text-3xl font-bold">Cara Kerja</h2>
            <p class="mt-2 text-center text-gray-600">Tiga langkah sederhana sampai barang kembali.</p>

            <div class="mt-10 grid gap-6 md:grid-cols-3">
                <div class="rounded-xl border border-gray-200 bg-white p-6">
                    <div class="text-2xl">✍️</div>
                    <h3 class="mt-3 font-semibold">1. Buat Laporan</h3>
                    <p class="mt-2 text-sm leading-6 text-gray-600">
                        Isi detail barang, lokasi, dan waktu kejadian.
                    </p>
                </div>

                <div class="rounded-xl border border-gray-200 bg-white p-6">
                    <div class="text-2xl">🔍</div>
                    <h3 class="mt-3 font-semibold">2. Tinjau &amp; Cocokkan</h3>
                    <p class="mt-2 text-sm leading-6 text-gray-600">
                        Tim memverifikasi laporan lalu mencocokkan dengan laporan serupa.
                    </p>
                </div>

                <div class="rounded-xl border border-gray-200 bg-white p-6">
                    <div class="text-2xl">🎉</div>
                    <h3 class="mt-3 font-semibold">3. Klaim &amp; Selesai</h3>
                    <p class="mt-2 text-sm leading-6 text-gray-600">
                        Ajukan klaim dengan bukti kepemilikan, lalu barang kembali ke pemiliknya.
                    </p>
                </div>
            </div>
        </div>
    </section>

    {{-- Kategori --}}
    <section id="kategori" class="border-t border-gray-200">
        <div class="mx-auto max-w-5xl px-6 py-16">
            <h2 class="text-center text-3xl font-bold">Kategori Barang</h2>
            <p class="mt-2 text-center text-gray-600">Barang apa yang hilang? Pilih kategori berikut.</p>

            <div class="mt-10 grid grid-cols-2 gap-4 sm:grid-cols-4">
                <a href="#" class="rounded-xl border border-gray-200 p-4 text-center hover:border-emerald-400 hover:bg-emerald-50/50">
                    <div class="text-2xl">💰</div>
                    <div class="mt-2 text-sm font-medium">Dompet &amp; Kartu</div>
                </a>
                <a href="#" class="rounded-xl border border-gray-200 p-4 text-center hover:border-emerald-400 hover:bg-emerald-50/50">
                    <div class="text-2xl">📱</div>
                    <div class="mt-2 text-sm font-medium">HP &amp; Elektronik</div>
                </a>
                <a href="#" class="rounded-xl border border-gray-200 p-4 text-center hover:border-emerald-400 hover:bg-emerald-50/50">
                    <div class="text-2xl">🎒</div>
                    <div class="mt-2 text-sm font-medium">Tas</div>
                </a>
                <a href="#" class="rounded-xl border border-gray-200 p-4 text-center hover:border-emerald-400 hover:bg-emerald-50/50">
                    <div class="text-2xl">🔑</div>
                    <div class="mt-2 text-sm font-medium">Kunci</div>
                </a>
                <a href="#" class="rounded-xl border border-gray-200 p-4 text-center hover:border-emerald-400 hover:bg-emerald-50/50">
                    <div class="text-2xl">📄</div>
                    <div class="mt-2 text-sm font-medium">Dokumen</div>
                </a>
                <a href="#" class="rounded-xl border border-gray-200 p-4 text-center hover:border-emerald-400 hover:bg-emerald-50/50">
                    <div class="text-2xl">👕</div>
                    <div class="mt-2 text-sm font-medium">Pakaian</div>
                </a>
                <a href="#" class="rounded-xl border border-gray-200 p-4 text-center hover:border-emerald-400 hover:bg-emerald-50/50">
                    <div class="text-2xl">📦</div>
                    <div class="mt-2 text-sm font-medium">Lainnya</div>
                </a>
                <a href="#" class="rounded-xl border border-dashed border-gray-300 p-4 text-center hover:border-emerald-400 hover:bg-emerald-50/50">
                    <div class="text-2xl">➕</div>
                    <div class="mt-2 text-sm font-medium">Lapor Sekarang</div>
                </a>
            </div>
        </div>
    </section>

    {{-- Ajakan Daftar --}}
    <section id="daftar" class="border-t border-gray-200 px-6 py-16">
        <div class="mx-auto max-w-5xl rounded-2xl bg-gray-900 px-6 py-12 text-center text-white">
            <h2 class="text-2xl font-bold sm:text-3xl">Siap melaporkan barangmu?</h2>
            <p class="mt-3 text-gray-300">Daftar gratis dan mulai sekarang juga.</p>
            <a href="#" class="mt-6 inline-block rounded-full bg-white px-6 py-3 text-sm font-semibold text-gray-900 hover:bg-gray-100">
                Daftar Gratis
            </a>
        </div>
    </section>
</x-layouts.app>