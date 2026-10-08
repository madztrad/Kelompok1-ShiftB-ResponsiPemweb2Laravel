<?php

use App\Livewire\Login;
use App\Livewire\Register;
use Illuminate\Support\Facades\Route;

Route::view('/', 'pages.landing')->name('home');

Route::get('/login', Login::class)->name('login');
Route::get('/register', Register::class)->name('register');

Route::post('/logout', function () {
    // Hapus token dari session saja. Tidak memanggil API logout dari sini
    // karena bisa deadlock (deadlock self-call di php artisan serve).
    session()->flush();

    return redirect()->route('home');
})->name('logout');
