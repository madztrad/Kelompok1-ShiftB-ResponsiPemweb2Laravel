<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ $title ?? 'Beranda' }} - Lost &amp; Found</title>

        <link rel="icon" href="/favicon.ico" sizes="any">

        @fonts
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-white text-gray-900">
        {{-- Navbar --}}
        <header class="border-b border-gray-200">
            <div class="mx-auto flex h-16 max-w-5xl items-center justify-between px-6">
                <a href="{{ route('home') }}" class="font-bold">🔍 Lost &amp; Found</a>

                <nav class="hidden gap-6 text-sm text-gray-600 md:flex">
                    <a href="#cara-kerja" class="hover:text-gray-900">Cara Kerja</a>
                    <a href="#kategori" class="hover:text-gray-900">Kategori</a>
                    <a href="#daftar" class="hover:text-gray-900">Gabung</a>
                </nav>

                <div class="flex gap-2 text-sm">
                    <a href="#" class="rounded-full px-4 py-2 hover:bg-gray-100">Masuk</a>
                    <a href="#" class="rounded-full bg-gray-900 px-4 py-2 text-white hover:bg-gray-700">Daftar</a>
                </div>
            </div>
        </header>

        <main>
            {{ $slot }}
        </main>

        {{-- Footer --}}
        <footer class="border-t border-gray-200">
            <div class="mx-auto flex max-w-5xl flex-col justify-between gap-2 px-6 py-8 text-sm text-gray-500 sm:flex-row">
                <p>&copy; 2026 Lost &amp; Found — Praktikum Pemrograman Web 2.</p>
                <p>Dibuat dengan Laravel &amp; Livewire.</p>
            </div>
        </footer>
    </body>
</html>
