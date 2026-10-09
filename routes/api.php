<?php

use App\Http\Controllers\Api\Admin\ItemController as AdminItemController;
use App\Http\Controllers\Api\Admin\UserController as AdminUserController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ClaimController;
use App\Http\Controllers\Api\ItemController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes (prefix otomatis: /api)
|--------------------------------------------------------------------------
*/

// ---------------------------------------------------------------- Auth
Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login'])->middleware('throttle:10,1');

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::get('me', [AuthController::class, 'me']);
    });
});

// -------------------------------------------------- Publik (beranda)
Route::get('categories', [CategoryController::class, 'index']);
Route::get('items', [ItemController::class, 'index']);
Route::get('items/{item}', [ItemController::class, 'show'])->whereNumber('item');

// ------------------------------------------- Pengguna login (ability: user)
Route::middleware(['auth:sanctum', 'abilities:user'])->group(function () {
    Route::get('my/items', [ItemController::class, 'mine']);
    Route::get('my/claims', [ClaimController::class, 'mine']);

    Route::post('items', [ItemController::class, 'store']);
    Route::put('items/{item}', [ItemController::class, 'update'])->whereNumber('item');
    Route::delete('items/{item}', [ItemController::class, 'destroy'])->whereNumber('item');

    Route::get('items/{item}/claims', [ClaimController::class, 'indexForItem'])->whereNumber('item');
    Route::post('items/{item}/claims', [ClaimController::class, 'store'])->whereNumber('item');
    Route::patch('claims/{claim}', [ClaimController::class, 'update'])->whereNumber('claim');
});

// ----------------------------------------------- Admin (ability: admin)
Route::prefix('admin')->middleware(['auth:sanctum', 'abilities:admin'])->group(function () {
    Route::patch('items/{item}/moderation', [AdminItemController::class, 'moderate'])->whereNumber('item');
    Route::apiResource('items', AdminItemController::class)->names('admin.items');

    Route::apiResource('users', AdminUserController::class)->names('admin.users');

    Route::apiResource('categories', CategoryController::class)
        ->except(['index', 'show'])
        ->names('admin.categories');
});
