<?php

use App\Http\Controllers\AuthController;

use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'auth', 'as' => 'auth.', 'controller' => AuthController::class], function () {
    Route::post('login', 'login')->name('login');
    Route::post('register', 'register')->name('register');
    Route::post('verify', 'verifyEmail')->name('verify-email');
    Route::post('forgot-password', 'forgotPassword')->name('forgot-password');
    Route::post('password-reset', 'passwordReset')->name('password-reset');

    Route::middleware('auth:api')->group(function () {
        Route::post('logout', 'logout')->name('logout');
        Route::post('refresh', 'refresh')->name('refresh');
        Route::get('me', 'me')->name('me');
    });
});
