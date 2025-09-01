<?php

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
Route::get("/categories", function () {
    return response()->json([
        "name" => "Al-Shami",
        "slug" => "al-shami",
        "description" => "A popular Middle Eastern restaurant",
        "is_active" => true
    ]);
});

Route::post("/categories", function (Request $request) {
  $user= User::create($request->all());

  return response()->json($user, 201);
});
