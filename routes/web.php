
<?php

use App\Livewire\Admin\Dashboard;
use App\Livewire\Admin\ManageCategories;
use App\Livewire\Admin\ManageItems;
use App\Livewire\Admin\ManageUsers;
use App\Livewire\Admin\ModerationQueue;
use App\Livewire\Home;
use App\Livewire\Items\BrowseItems;
use App\Livewire\Items\CreateItem;
use App\Livewire\Items\ShowItem;
use App\Livewire\Login;
use App\Livewire\My\MyClaims;
use App\Livewire\My\MyItems;
use App\Livewire\Register;
use Illuminate\Support\Facades\Route;

// Beranda
Route::get('/', Home::class)->name('home');

// Katalog dan laporan barang
Route::get('/items', BrowseItems::class)->name('items.index');
Route::get('/items/create', CreateItem::class)->name('items.create');
Route::get('/items/{item}', ShowItem::class)
    ->whereNumber('item')
    ->name('items.show');

// Halaman pengguna
Route::get('/my/items', MyItems::class)->name('my.items');
Route::get('/my/claims', MyClaims::class)->name('my.claims');

// Autentikasi
Route::get('/login', Login::class)->name('login');
Route::get('/register', Register::class)->name('register');

// Admin
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/', Dashboard::class)->name('index');
    Route::get('/moderasi', ModerationQueue::class)->name('moderation');
    Route::get('/items', ManageItems::class)->name('items');
    Route::get('/users', ManageUsers::class)->name('users');
    Route::get('/categories', ManageCategories::class)->name('categories');
});

// Logout
Route::post('/logout', function () {
    session()->flush();

    return redirect()->route('home');
})->name('logout');

