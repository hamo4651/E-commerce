<?php

use App\Http\Controllers\API\EmailVerificationNotificationController;

use App\Http\Controllers\API\VerifyEmailController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\GoogleAuthController;
    // ///////////// google routes////////////////
Route::get('auth/google', [GoogleAuthController::class, 'redirectToGoogle'])->name('google.login');
Route::get('auth/google/callback', [GoogleAuthController::class, 'handleGoogleCallback']);
/////////////////////



////////////verify-email//////////////
Route::get('/verify-email/{id}/{hash}', VerifyEmailController::class)
    ->middleware(['auth:sanctum', 'signed', 'throttle:6,1'])
    ->name('verification.verify');

Route::post('/email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
    ->middleware(['auth:sanctum', 'throttle:6,1'])
    ->name('verification.send');

/////////////////////////////

 