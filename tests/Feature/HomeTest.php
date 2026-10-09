<?php

use App\Livewire\Home;
use Livewire\Livewire;

it('menampilkan landing page untuk tamu di beranda', function () {
    $this->get('/')
        ->assertOk()
        ->assertSee('Hilang bukan berarti');
});

it('menampilkan dashboard user untuk pengguna yang sudah login', function () {
    session(['api_token' => 'token-palsu', 'user' => ['id' => 1, 'name' => 'Budi', 'role' => 'user']]);

    $this->get('/')
        ->assertOk()
        ->assertSee('Halo, Budi')
        ->assertSee('Aksi Cepat');
});

it('tidak menampilkan landing tamu di dalam dashboard user', function () {
    session(['api_token' => 'token-palsu', 'user' => ['id' => 1, 'name' => 'Budi', 'role' => 'user']]);

    $this->get('/')->assertOk()->assertDontSee('Daftar Gratis');
});

it('merender komponen Home tanpa error untuk tamu', function () {
    Livewire::test(Home::class)->assertOk();
});
