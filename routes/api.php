<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\CategoryController;
use App\Http\Controllers\API\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
//     return $request->user();
// });


////Auth Routes //////////
Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::group(["middleware"=>"auth:sanctum"],function(){
    Route::get( '/user', [AuthController::class, 'profile']);
    Route::get( '/logout', [AuthController::class, 'logout']);
});
////////
// ////////// Category Routes /////////////////////

Route::apiResource('categories', CategoryController::class);
Route::apiResource('products', ProductController::class);