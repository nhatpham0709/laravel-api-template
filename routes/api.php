<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\Auth\AuthenticatedSessionController;
use App\Http\Controllers\User\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\User\Auth\NewPasswordController;
use App\Http\Controllers\User\Auth\PasswordResetLinkController;
use App\Http\Controllers\User\Auth\RegisteredUserController;
use App\Http\Controllers\User\Auth\VerifyEmailController;
use Laravel\Sanctum\Http\Controllers\CsrfCookieController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::prefix('v1/users')->group(function () {
    Route::prefix('auth')->group(function () {
        Route::get('/profile', function (Request $request) {
            return $request->user();
        })->middleware('auth:sanctum');
    
        Route::post('/register', [RegisteredUserController::class, 'store'])
            ->middleware('guest');

        Route::post('/login', [AuthenticatedSessionController::class, 'store'])
            ->middleware('guest');

        Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])
            ->middleware('guest');

        Route::post('/reset-password', [NewPasswordController::class, 'store'])
            ->middleware('guest');

        Route::get('/verify-email/{id}/{hash}', [VerifyEmailController::class, '__invoke'])
            ->middleware(['auth', 'signed', 'throttle:6,1']);

        Route::post('/email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
            ->middleware(['auth', 'throttle:6,1']);

        Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
            ->middleware('auth');

        Route::get('/sanctum/csrf-cookie', [CsrfCookieController::class, 'show']);
    });
});
