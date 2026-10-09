<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ $title ?? 'Beranda' }} - Lost &amp; Found</title>

        <link rel="icon" href="/favicon.ico" sizes="any">

        @fonts
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        {{-- Base URL & token API disuntik ke helper window.api (resources/js/app.js). --}}
        <script>
            window.__apiBase = @js(url('/api'));
            window.__apiToken = @js(session('api_token'));
        </script>
    </head>
    <body class="bg-white text-gray-900">
        {{-- Navbar --}}
        <header class="border-b border-gray-200">
            <div class="mx-auto flex h-16 max-w-5xl items-center justify-between px-6">
                <a href="{{ route('home') }}" class="font-bold">🔍 Lost &amp; Found</a>

                @if (session('user.role') === 'admin')
                    <nav class="hidden gap-1 text-sm text-gray-600 md:flex">
                        <a href="{{ route('admin') }}" wire:navigate @class(['rounded-full px-3 py-2 hover:text-gray-900', 'bg-gray-100 text-gray-900' => request()->routeIs('admin')])>Dashboard</a>
                        <a href="{{ route('admin.moderation') }}" wire:navigate @class(['rounded-full px-3 py-2 hover:text-gray-900', 'bg-gray-100 text-gray-900' => request()->routeIs('admin.moderation')])>Moderasi</a>
                        <a href="{{ route('admin.items') }}" wire:navigate @class(['rounded-full px-3 py-2 hover:text-gray-900', 'bg-gray-100 text-gray-900' => request()->routeIs('admin.items')])>Laporan</a>
                        <a href="{{ route('admin.users') }}" wire:navigate @class(['rounded-full px-3 py-2 hover:text-gray-900', 'bg-gray-100 text-gray-900' => request()->routeIs('admin.users')])>Pengguna</a>
                        <a href="{{ route('admin.categories') }}" wire:navigate @class(['rounded-full px-3 py-2 hover:text-gray-900', 'bg-gray-100 text-gray-900' => request()->routeIs('admin.categories')])>Kategori</a>
                    </nav>
                @elseif (session('api_token'))
                    <nav class="hidden gap-6 text-sm text-gray-600 md:flex">
                        <a href="{{ route('home') }}" wire:navigate @class(['hover:text-gray-900', 'font-semibold text-gray-900' => request()->routeIs('home')])>Beranda</a>
                        <a href="{{ route('items.index') }}" wire:navigate @class(['hover:text-gray-900', 'font-semibold text-gray-900' => request()->routeIs('items.index')])>Katalog</a>
                    </nav>
                @else
                    <nav class="hidden gap-6 text-sm text-gray-600 md:flex">
                        <a href="{{ route('items.index') }}" wire:navigate @class(['hover:text-gray-900', 'font-semibold text-gray-900' => request()->routeIs('items.index')])>Katalog</a>
                        <a href="{{ route('home') }}#cara-kerja" class="hover:text-gray-900">Cara Kerja</a>
                        <a href="{{ route('home') }}#kategori" class="hover:text-gray-900">Kategori</a>
                        <a href="{{ route('home') }}#daftar" class="hover:text-gray-900">Gabung</a>
                    </nav>
                @endif

                @if (session('api_token'))
                    <div class="flex items-center gap-3 text-sm">
                        <span class="hidden text-gray-600 sm:inline">Halo, {{ session('user.name') }}</span>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="rounded-full border border-gray-300 px-4 py-2 text-gray-700 hover:bg-gray-50">
                                Keluar
                            </button>
                        </form>
                    </div>
                @else
                    <div class="flex gap-2 text-sm">
                        <a href="{{ route('login') }}" class="rounded-full px-4 py-2 hover:bg-gray-100">Masuk</a>
                        <a href="{{ route('register') }}" class="rounded-full bg-gray-900 px-4 py-2 text-white hover:bg-gray-700">Daftar</a>
                    </div>
                @endif
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
