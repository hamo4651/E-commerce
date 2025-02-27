<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\CategoryController;
use App\Http\Controllers\API\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\CartController;
use App\Http\Controllers\API\OrderController;
use App\Http\Controllers\API\PaymentController;

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
//////////////////// Product Routes //////////////
Route::apiResource('products', ProductController::class);

//////////////////    Cart Routes    ////////////// 

Route::middleware('auth:sanctum')->group(function () {
   
Route::post('/cart', [CartController::class, 'addToCart']); 
Route::get('/cart', [CartController::class, 'getCartItems']); 
Route::put('/cart/{id}', [CartController::class, 'updateCartItem']); 
Route::delete('/cart/{id}', [CartController::class, 'removeCartItem']); 
Route::post('/cart/clear', [CartController::class, 'clearCart']); 
});

/////////////////////

////////////////    Order Routes    //////////////
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/orders', [OrderController::class, 'store']);
    Route::get('/orders', [OrderController::class, 'index']);
    Route::get('/orders/{id}', [OrderController::class, 'show']);
    Route::put('/orders/{id}/status', [OrderController::class, 'updateStatus']);
});
///////////////////////////// 


 ///////////// Payment Routes //////////////

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/stripe/payment', [PaymentController::class, 'stripePayment']);
    Route::post('/paypal/payment', [PaymentController::class, 'paypalPayment']);
    Route::post('/paypal/capture', [PaymentController::class, 'paypalCapture']);
});