<?php

use App\Livewire\Admin\Dashboard;
use App\Livewire\Admin\ManageCategories;
use App\Livewire\Admin\ManageItems;
use App\Livewire\Admin\ManageUsers;
use App\Livewire\Admin\ModerationQueue;
use App\Livewire\Login;
use App\Livewire\Register;
use Illuminate\Support\Facades\Route;

Route::view('/', 'pages.landing')->name('home');
Route::livewire('/items', 'browse-items')->name('items.index');

Route::get('/login', Login::class)->name('login');
Route::get('/register', Register::class)->name('register');


Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/', Dashboard::class)->name('index');
    Route::get('/moderasi', ModerationQueue::class)->name('moderation');
    Route::get('/items', ManageItems::class)->name('items');
    Route::get('/users', ManageUsers::class)->name('users');
    Route::get('/categories', ManageCategories::class)->name('categories');
});


Route::post('/logout', function () {
    // Hapus token dari session saja. Tidak memanggil API logout dari sini
    // karena bisa deadlock (deadlock self-call di php artisan serve).
    session()->flush();

    return redirect()->route('home');
})->name('logout');
