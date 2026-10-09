<?php

use App\Livewire\Items\CreateItem;
use App\Livewire\My\MyClaims;
use App\Livewire\My\MyItems;
use Livewire\Livewire;

dataset('halaman user', [
    'form lapor' => ['/items/create', CreateItem::class, 'Lapor Barang'],
    'laporan saya' => ['/my/items', MyItems::class, 'Laporan Saya'],
    'klaim saya' => ['/my/claims', MyClaims::class, 'Klaim Saya'],
]);

it('mengarahkan tamu ke login', function (string $path) {
    $this->get($path)->assertRedirect(route('login'));
})->with('halaman user');

it('menampilkan halaman untuk pengguna yang sudah login', function (string $path, string $component, string $heading) {
    session(['api_token' => 'token-palsu', 'user' => ['id' => 1, 'name' => 'Budi', 'role' => 'user']]);

    $this->get($path)
        ->assertOk()
        ->assertSee($heading);
})->with('halaman user');

it('merender komponen halaman user tanpa error saat login', function (string $path, string $component, string $heading) {
    session(['api_token' => 'token-palsu', 'user' => ['id' => 1, 'name' => 'Budi', 'role' => 'user']]);

    Livewire::test($component)->assertOk();
})->with('halaman user');

it('menautkan tombol aksi cepat ke halaman tujuannya', function () {
    session(['api_token' => 'token-palsu', 'user' => ['id' => 1, 'name' => 'Budi', 'role' => 'user']]);

    $this->get('/')
        ->assertOk()
        ->assertSee(route('items.create'))
        ->assertSee(route('my.items'))
        ->assertSee(route('my.claims'));
});
