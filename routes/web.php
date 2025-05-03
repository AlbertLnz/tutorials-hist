<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\YoutubeController;

Route::get('/', function () {
    return view('welcome');
});

// VIEWS
Route::view('register', 'register')->name('register');
Route::get('login', function () {
    return view('login');
})->name('login');
// Route::view('dashboard', view: 'dashboard')->middleware('auth')->name(name: 'dashboard');


Route::post('register', [AuthController::class, 'register'])->name('register.store');
Route::post('login/email', [AuthController::class, 'login_email'])->middleware('throttle:5,1')->name('login.email.attempt');
Route::post('login/username', [AuthController::class, 'login_username'])->middleware('throttle:5,1')->name('login.username.attempt');
Route::post('logout', [AuthController::class, 'logout'])->name('logout');

Route::post('yt', [YoutubeController::class, 'post_new_yt_video'])->name('yt.url.attempt');
Route::get('dashboard', [YoutubeController::class, 'get_all_user_videos'])->middleware('auth')->name('dashboard');
