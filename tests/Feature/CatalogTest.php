<?php

use App\Livewire\Items\BrowseItems;
use Livewire\Livewire;

it('menampilkan katalog barang untuk publik', function () {
    $this->get('/items')
        ->assertOk()
        ->assertSee('Katalog Barang');
});

it('menampilkan katalog barang untuk pengguna yang sudah login', function () {
    session(['api_token' => 'token-palsu', 'user' => ['id' => 1, 'name' => 'Budi', 'role' => 'user']]);

    $this->get('/items')
        ->assertOk()
        ->assertSee('Katalog Barang');
});

it('merender komponen BrowseItems tanpa error', function () {
    Livewire::test(BrowseItems::class)->assertOk();
});
