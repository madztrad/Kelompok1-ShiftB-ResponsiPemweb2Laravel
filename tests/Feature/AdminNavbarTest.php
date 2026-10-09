<?php

use App\Livewire\Admin\Dashboard;
use App\Livewire\Admin\ManageItems;
use App\Models\User;
use Livewire\Livewire;

it('menandai tautan navbar admin yang sedang aktif', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->withSession(['api_token' => 'token-admin', 'user' => ['id' => $admin->id, 'name' => $admin->name, 'role' => 'admin']])
        ->get(route('admin.items'))
        ->assertOk()
        ->assertSee('href="'.route('admin.items').'"', false)
        ->assertSee('bg-gray-100 text-gray-900', false);
});

it('menampilkan dashboard sebagai halaman utama portal admin', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->withSession(['api_token' => 'token-admin', 'user' => ['id' => $admin->id, 'name' => $admin->name, 'role' => 'admin']])
        ->get(route('admin.index'))
        ->assertOk()
        ->assertSee('Dashboard Admin');
});

it('memisahkan rute moderasi dari dashboard', function () {
    expect(route('admin.index'))->toEndWith('/admin')
        ->and(route('admin.moderation'))->toEndWith('/admin/moderasi');
});

it('melindungi semua halaman admin dari non-admin', function () {
    Livewire::test(Dashboard::class)->assertRedirect(route('login'));
});

it('menyediakan tombol detail laporan di halaman kelola laporan', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->withSession(['api_token' => 'token-admin', 'user' => ['id' => $admin->id, 'name' => $admin->name, 'role' => 'admin']])
        ->get(route('admin.items'))
        ->assertOk()
        ->assertSee('openDetail(item.id)', false)
        ->assertSee('/admin/items/', false);

    Livewire::test(ManageItems::class)
        ->assertSee('Detail');
});

it('menyediakan tombol tambah dan edit laporan', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->withSession(['api_token' => 'token-admin', 'user' => ['id' => $admin->id, 'name' => $admin->name, 'role' => 'admin']])
        ->get(route('admin.items'))
        ->assertOk()
        ->assertSee('+ Tambah laporan')
        ->assertSee('openEdit(item)', false)
        ->assertSee('submitForm()', false);

    Livewire::test(ManageItems::class)->assertSee('Tambah laporan');
});

it('menyediakan tombol edit data pengguna', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->withSession(['api_token' => 'token-admin', 'user' => ['id' => $admin->id, 'name' => $admin->name, 'role' => 'admin']])
        ->get(route('admin.users'))
        ->assertOk()
        ->assertSee('openEdit(u)', false)
        ->assertSee('saveEdit()', false);
});
