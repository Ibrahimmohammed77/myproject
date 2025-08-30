<?php

use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware('auth')->get('products', [ProductController::class, 'index']);

Route::prefix('products/')->as('products.')->middleware('auth')->group(function () {

    Route::get('create', [ProductController::class, 'create'])->name('create');

    Route::post('', [ProductController::class, 'store'])->name('store');

    Route::get('', [ProductController::class, 'index'])->name('index');
});
require __DIR__ . '/auth.php';