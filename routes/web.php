<?php

use App\Livewire\Admin\Dashboard;
use App\Livewire\Admin\ManageCategories;
use App\Livewire\Admin\ManageItems;
use App\Livewire\Admin\ManageUsers;
use App\Livewire\Admin\ModerationQueue;
use App\Livewire\Home;
use App\Livewire\Items\BrowseItems;
use App\Livewire\Items\ShowItem;
use App\Livewire\Login;
use App\Livewire\Register;
use Illuminate\Support\Facades\Route;

// Beranda: dashboard untuk user login, landing page untuk tamu.
Route::get('/', Home::class)->name('home');

Route::get('/items', BrowseItems::class)->name('items.index');
Route::get('/items/{item}', ShowItem::class)->whereNumber('item')->name('items.show');

Route::get('/login', Login::class)->name('login');
Route::get('/register', Register::class)->name('register');

Route::prefix('admin')->name('admin')->group(function () {
    Route::get('/', Dashboard::class)->name('');
    Route::get('/moderasi', ModerationQueue::class)->name('.moderation');
    Route::get('/items', ManageItems::class)->name('.items');
    Route::get('/users', ManageUsers::class)->name('.users');
    Route::get('/categories', ManageCategories::class)->name('.categories');
});

Route::post('/logout', function () {
    // Hapus token dari session saja. Tidak memanggil API logout dari sini
    // karena bisa deadlock (deadlock self-call di php artisan serve).
    session()->flush();

    return redirect()->route('home');
})->name('logout');
