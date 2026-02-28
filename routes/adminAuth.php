<?php

use App\Http\Controllers\AuthAdmin\AuthenticatedSessionController;
use App\Http\Controllers\AuthAdmin\ConfirmablePasswordController;
use App\Http\Controllers\AuthAdmin\EmailVerificationNotificationController;
use App\Http\Controllers\AuthAdmin\EmailVerificationPromptController;
use App\Http\Controllers\AuthAdmin\NewPasswordController;
use App\Http\Controllers\AuthAdmin\PasswordController;
use App\Http\Controllers\AuthAdmin\PasswordResetLinkController;
use App\Http\Controllers\AuthAdmin\RegisteredUserController;
use App\Http\Controllers\AuthAdmin\VerifyEmailController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('register', [RegisteredUserController::class, 'create'])
                ->name('register');

    Route::post('register', [RegisteredUserController::class, 'store']);

    Route::get('login', [AuthenticatedSessionController::class, 'create'])
                ->name('login');

    Route::post('login', [AuthenticatedSessionController::class, 'store']);

    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])
                ->name('password.request');

    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])
                ->name('password.email');

    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])
                ->name('password.reset');

    Route::post('reset-password', [NewPasswordController::class, 'store'])
                ->name('password.store');
});

Route::middleware('admin')->group(function () {
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
                ->name('logout');
});
