<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\UpdatePasswordController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerifyEmailController;

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

Route::middleware('guest')->group(function () {
    Route::post('/register', [RegisteredUserController::class, 'store'])->name('register');
    
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])
        ->name('login');

    Route::post('/login', [AuthenticatedSessionController::class, 'store']);

    Route::get('/forgot-password', [PasswordResetLinkController::class, 'create'])
        ->name('password.request');

    Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])
        ->name('password.email');

    Route::get('/reset-password/{token}', [NewPasswordController::class, 'create'])
        ->name('password.reset');

    Route::post('/reset-password', [NewPasswordController::class, 'store'])
        ->name('password.update');
});

Route::middleware('auth')->group(function () {
    Route::get('/verify-email', EmailVerificationPromptController::class)
        ->name('verification.notice');

    Route::get('/verify-email/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    Route::post('/email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('verification.send');

    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');
});

Route::group(['middleware' => ['auth']], function () {
    Route::get('/password/edit', [UpdatePasswordController::class, 'edit'])->name('password.edit');
    
    Route::put('/password/update', [UpdatePasswordController::class, 'update'])->name('password.update');
});

Route::post('/confirm-password', [ConfirmablePasswordController::class, 'store'])
    ->middleware(['auth'])
    ->name('password.confirm');

// Custom password confirmation route
Route::post('/custom-confirm-password', 'CustomConfirmPasswordController@handle')
    ->middleware(['auth'])
    ->name('custom.password.confirm');
