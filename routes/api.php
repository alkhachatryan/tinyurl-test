<?php

use App\Http\Controllers\AuthController;

use App\Http\Controllers\CategoriesController;
use App\Http\Controllers\ProductController;
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

// TODO: get VIEW actions out from AUTH middleware
Route::group(['middleware' => 'auth:api'], function () {
    Route::group(['prefix' => 'categories'], function () {
        Route::get('/', [CategoriesController::class, 'index']);
        Route::post('/', [CategoriesController::class, 'create']);
    });

    Route::group(['prefix' => 'products'], function () {
        Route::get('/', [ProductController::class, 'index']);
        Route::post('/', [ProductController::class, 'create']);
        Route::put('/{productId}', [ProductController::class, 'update'])->where('productId', '[0-9]+');
        Route::patch('/{productId}/delete', [ProductController::class, 'delete'])->where('productId', '[0-9]+');
        Route::patch('/{productId}/restore', [ProductController::class, 'restore'])->where('productId', '[0-9]+');
    });
});
