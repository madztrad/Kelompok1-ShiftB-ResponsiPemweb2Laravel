<?php

use App\Livewire\Items\ShowItem;
use Livewire\Livewire;

it('menampilkan halaman detail barang untuk publik', function () {
    $this->get('/items/5')
        ->assertOk()
        ->assertSee('Detail Barang');
});

it('meneruskan id barang ke komponen detail', function () {
    Livewire::test(ShowItem::class, ['item' => 5])
        ->assertOk()
        ->assertSet('itemId', 5);
});
