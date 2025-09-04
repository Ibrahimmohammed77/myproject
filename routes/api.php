<?php

use App\Http\Controllers\Api\CategoryController;
use Illuminate\Support\Facades\Route;

Route::prefix('categories')->group(function () {
    Route::get('/',           [CategoryController::class, 'index'])->name('categories.index');
    Route::post('/',          [CategoryController::class, 'store'])->name('categories.store');
    Route::get('/{category}', [CategoryController::class, 'show'])->name('categories.show');
    Route::match(['put', 'patch'], '/{category}', [CategoryController::class, 'update'])->name('categories.update');
    Route::delete('/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');

    // عمليات إضافية
    Route::post('/{id}/restore', [CategoryController::class, 'restore'])->name('categories.restore');
    Route::delete('/{id}/force',  [CategoryController::class, 'forceDelete'])->name('categories.force-delete');
    Route::patch('/{category}/toggle', [CategoryController::class, 'toggle'])->name('categories.toggle');
});
