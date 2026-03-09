<?php

use App\Http\Controllers\Back\AdminController;
use App\Http\Controllers\Back\BackHomeController;
use App\Http\Controllers\Back\RolesController;
use App\Http\Controllers\Back\UserController;
use App\Http\Controllers\Front\HomeController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

## ********** Home **********##
Route::prefix('front')->name('front.')->group(function(){
    Route::get('/', HomeController::class)->name('index')->middleware('auth');
    // Auth Routes
});

    require __DIR__.'/auth.php';

## ********** Back **********##
Route::prefix('back')->name('back.')->group(function(){
    Route::get('/', BackHomeController::class)->name('index')->middleware('admin');
    // Auth Routes

    ## ********** Admins **********##
    Route::resource('admins', AdminController::class)->except(['show']);
    ## ********** Roles **********##
    Route::resource('roles', RolesController::class);
    ## ********** Users **********##
    Route::resource('users', UserController::class);
    require __DIR__.'/adminAuth.php';
});
