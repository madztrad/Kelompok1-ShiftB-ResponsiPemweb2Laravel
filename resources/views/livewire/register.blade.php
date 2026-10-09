<div
    class="mx-auto max-w-md px-6 py-16"
    x-data="{
        busy: false,
        async kirim() {
            busy = true;
            try {
                const res = await fetch('{{ url('/api/auth/register') }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                    body: JSON.stringify({
                        name: $wire.name,
                        email: $wire.email,
                        password: $wire.password,
                        password_confirmation: $wire.password_confirmation,
                    }),
                });
                const data = await res.json();

                if (res.ok) {
                    await $wire.call('simpanToken', data.data.token, data.data.user);
                } else {
                    await $wire.call('terimaApiError', data.errors || {});
                }
            } catch (e) {
                await $wire.call('terimaApiError', { email: ['Tidak bisa terhubung ke server.'] });
            } finally {
                busy = false;
            }
        },
    }"
    @coba-daftar.window="kirim()"
>
    <div class="rounded-2xl border border-gray-200 p-8">
        <h1 class="text-2xl font-bold">Daftar</h1>
        <p class="mt-1 text-sm text-gray-600">
            Sudah punya akun?
            <a href="{{ route('login') }}" class="font-medium text-emerald-600 hover:underline">Masuk di sini</a>
        </p>

        <form wire:submit="register" class="mt-6 space-y-4">
            <div>
                <label for="name" class="mb-1 block text-sm font-medium">Nama</label>
                <input
                    id="name"
                    type="text"
                    wire:model="name"
                    placeholder="Nama lengkap"
                    class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-emerald-500 focus:outline-none"
                />
                @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

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
                    placeholder="Minimal 8 karakter"
                    class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-emerald-500 focus:outline-none"
                />
                @error('password') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="password_confirmation" class="mb-1 block text-sm font-medium">Ulangi Password</label>
                <input
                    id="password_confirmation"
                    type="password"
                    wire:model="password_confirmation"
                    placeholder="Ketik ulang password"
                    class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-emerald-500 focus:outline-none"
                />
                @error('password_confirmation') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <button
                type="submit"
                class="w-full rounded-full bg-gray-900 px-4 py-3 text-sm font-semibold text-white hover:bg-gray-700 disabled:opacity-60"
                wire:loading.attr="disabled"
                :disabled="busy"
            >
                <span x-show="!busy" wire:loading.remove wire:target="register">Daftar Sekarang</span>
                <span wire:loading wire:target="register">Memeriksa...</span>
                <span x-show="busy">Memproses...</span>
            </button>
        </form>
    </div>
</div>
