<div
    class="mx-auto max-w-md px-6 py-16"
    x-data="{
        busy: false,
        async kirim() {
            busy = true;
            try {
                const res = await fetch('{{ url('/api/auth/login') }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                    body: JSON.stringify({ email: $wire.email, password: $wire.password }),
                });
                const data = await res.json();

                if (res.ok) {
                    await $wire.call('simpanToken', data.data.token, data.data.user);
                } else {
                    await $wire.call('terimaApiError', data.message || 'Login gagal, coba lagi.');
                }
            } catch (e) {
                await $wire.call('terimaApiError', 'Tidak bisa terhubung ke server.');
            } finally {
                busy = false;
            }
        },
    }"
    @coba-login.window="kirim()"
>
    <div class="rounded-2xl border border-gray-200 p-8">
        <h1 class="text-2xl font-bold">Masuk</h1>
        <p class="mt-1 text-sm text-gray-600">
            Belum punya akun?
            <a href="{{ route('register') }}" class="font-medium text-emerald-600 hover:underline">Daftar di sini</a>
        </p>

        @if ($error)
            <div class="mt-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                {{ $error }}
            </div>
        @endif

        <form wire:submit="login" class="mt-6 space-y-4">
            <div>
                <label for="email" class="mb-1 block text-sm font-medium">Email</label>
                <input
                    id="email"
                    type="email"
                    wire:model="email"
                    placeholder="email@contoh.com"
                    class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-emerald-500 focus:outline-none"
                />
                @error('email') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="password" class="mb-1 block text-sm font-medium">Password</label>
                <input
                    id="password"
                    type="password"
                    wire:model="password"
                    placeholder="••••••••"
                    class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-emerald-500 focus:outline-none"
                />
                @error('password') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <button
                type="submit"
                class="w-full rounded-full bg-gray-900 px-4 py-3 text-sm font-semibold text-white hover:bg-gray-700 disabled:opacity-60"
                wire:loading.attr="disabled"
                :disabled="busy"
            >
                <span x-show="!busy" wire:loading.remove wire:target="login">Masuk</span>
                <span wire:loading wire:target="login">Memeriksa...</span>
                <span x-show="busy">Memproses...</span>
            </button>
        </form>

        <p class="mt-6 text-center text-xs text-gray-400">
            Akun contoh: user@lostfound.test / password
        </p>
    </div>
</div>
