<?php

use App\Livewire\Login;
use App\Livewire\Register;
use Livewire\Livewire;

it('mengirim event coba-login setelah validasi lolos', function () {
    Livewire::test(Login::class)
        ->set('email', 'user@lostfound.test')
        ->set('password', 'password')
        ->call('login')
        ->assertHasNoErrors()
        ->assertDispatched('coba-login');
});

it('menyimpan token di session setelah fetch API sukses', function () {
    Livewire::test(Login::class)
        ->call('simpanToken', 'token-abc-123', ['id' => 1, 'name' => 'User Test'])
        ->assertRedirect(route('home'));

    expect(session('api_token'))->toBe('token-abc-123')
        ->and(session('user.name'))->toBe('User Test');
});

it('mengarahkan admin ke portal admin setelah login', function () {
    Livewire::test(Login::class)
        ->call('simpanToken', 'token-admin-123', ['id' => 1, 'name' => 'Admin', 'role' => 'admin'])
        ->assertRedirect(route('admin.index'));
});

it('menampilkan pesan error dari API', function () {
    Livewire::test(Login::class)
        ->call('terimaApiError', 'Email atau password salah.')
        ->assertSet('error', 'Email atau password salah.');
});

it('menolak input kosong sebelum memanggil API', function () {
    Livewire::test(Login::class)
        ->set('email', '')
        ->set('password', '')
        ->call('login')
        ->assertHasErrors(['email', 'password'])
        ->assertNotDispatched('coba-login');
});

it('mengirim event coba-daftar setelah validasi register lolos', function () {
    Livewire::test(Register::class)
        ->set('name', 'User Test')
        ->set('email', 'user@lostfound.test')
        ->set('password', 'password123')
        ->set('password_confirmation', 'password123')
        ->call('register')
        ->assertHasNoErrors()
        ->assertDispatched('coba-daftar');
});

it('menampilkan error per field dari balasan 422 API', function () {
    Livewire::test(Register::class)
        ->call('terimaApiError', ['email' => ['Email ini sudah terdaftar.']])
        ->assertHasErrors(['email' => 'Email ini sudah terdaftar.']);
});
