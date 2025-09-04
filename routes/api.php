<?php

use App\Http\Controllers\Api\CategoryController;
use Illuminate\Support\Facades\Route;

Route::apiResource('categories', CategoryController::class);
Route::post('categories/{id}/restore', [CategoryController::class, 'restore']);
Route::delete('categories/{id}/force', [CategoryController::class, 'forceDelete']);
Route::patch('categories/{category}/toggle', [CategoryController::class, 'toggle']);

