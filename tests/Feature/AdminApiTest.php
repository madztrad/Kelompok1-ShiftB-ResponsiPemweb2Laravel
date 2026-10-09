<?php

use App\Livewire\Admin\ModerationQueue;
use App\Models\Category;
use App\Models\Item;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Laravel\Sanctum\Sanctum;
use Livewire\Livewire;

function actingAsAdmin(): User
{
    $admin = User::factory()->admin()->create();
    Sanctum::actingAs($admin, ['user', 'admin']);

    return $admin;
}

function actingAsUser(): User
{
    $user = User::factory()->create();
    Sanctum::actingAs($user, ['user']);

    return $user;
}

it('menolak user biasa mengakses endpoint admin', function () {
    actingAsUser();

    $this->getJson('/api/admin/items')->assertForbidden();
    $this->getJson('/api/admin/users')->assertForbidden();
});

it('menolak akses admin tanpa token', function () {
    $this->getJson('/api/admin/items')->assertUnauthorized();
});

it('menyetujui laporan pending lewat moderasi', function () {
    $admin = actingAsAdmin();
    $item = Item::factory()->pending()->create();

    $this->patchJson("/api/admin/items/{$item->id}/moderation", ['status' => 'approved'])
        ->assertOk()
        ->assertJsonPath('data.moderation_status', 'approved');

    expect($item->fresh()->moderated_by)->toBe($admin->id);
});

it('wajib menyertakan alasan saat memblokir', function () {
    actingAsAdmin();
    $item = Item::factory()->pending()->create();

    $this->patchJson("/api/admin/items/{$item->id}/moderation", ['status' => 'blocked'])
        ->assertUnprocessable()
        ->assertJsonValidationErrors('reason');

    $this->patchJson("/api/admin/items/{$item->id}/moderation", [
        'status' => 'blocked',
        'reason' => 'Foto tidak jelas.',
    ])->assertOk()->assertJsonPath('data.moderation_status', 'blocked');

    expect($item->fresh()->blocked_reason)->toBe('Foto tidak jelas.');
});

it('laporan buatan admin langsung approved', function () {
    actingAsAdmin();
    $category = Category::factory()->create();

    $this->postJson('/api/admin/items', [
        'category_id' => $category->id,
        'title' => 'Dompet ditemukan',
        'description' => 'Dompet cokelat di kantin.',
        'type' => 'found',
        'location' => 'Kantin',
        'event_date' => now()->toDateString(),
        'photo' => UploadedFile::fake()->image('dompet.jpg'),
    ])->assertCreated()->assertJsonPath('data.moderation_status', 'approved');
});

it('mencabut token saat role pengguna diubah', function () {
    actingAsAdmin();
    $user = User::factory()->create();
    $token = $user->createToken('api-token', ['user'])->plainTextToken;

    $this->patchJson("/api/admin/users/{$user->id}", ['role' => 'admin'])->assertOk();

    expect($user->fresh()->tokens()->count())->toBe(0);
    expect($user->fresh()->role->value)->toBe('admin');
});

it('melarang admin mengubah role dan menghapus akun sendiri', function () {
    $admin = actingAsAdmin();

    $this->patchJson("/api/admin/users/{$admin->id}", ['role' => 'user'])->assertForbidden();
    $this->deleteJson("/api/admin/users/{$admin->id}")->assertForbidden();
});

it('menolak hapus kategori yang masih dipakai laporan', function () {
    actingAsAdmin();
    $item = Item::factory()->approved()->create();

    $this->deleteJson("/api/admin/categories/{$item->category_id}")->assertConflict();
});

it('hanya admin yang bisa membuka halaman admin', function () {
    Livewire::test(ModerationQueue::class)->assertRedirect(route('login'));

    session(['api_token' => 'token-palsu', 'user' => ['id' => 1, 'name' => 'User', 'role' => 'user']]);
    Livewire::test(ModerationQueue::class)->assertRedirect(route('home'));

    session(['api_token' => 'token-palsu', 'user' => ['id' => 1, 'name' => 'Admin', 'role' => 'admin']]);
    Livewire::test(ModerationQueue::class)->assertOk();
});
