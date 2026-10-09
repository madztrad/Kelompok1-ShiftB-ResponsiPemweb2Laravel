<?php

use App\Models\User;

it('menandai tautan navbar admin yang sedang aktif', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->withSession(['api_token' => 'token-admin', 'user' => ['id' => $admin->id, 'name' => $admin->name, 'role' => 'admin']])
        ->get(route('admin.items'))
        ->assertOk()
        ->assertSee('href="'.route('admin.items').'"', false)
        ->assertSee('bg-gray-100 text-gray-900', false);
});
