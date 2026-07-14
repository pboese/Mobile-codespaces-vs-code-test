<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('home')
        : view('welcome');
});

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {
    Route::get('/home', fn () => view('home'))->name('home');

    // Two-Factor Authentication management
    Route::get('/user/two-factor-authentication', fn () => view('auth.two-factor'))
        ->name('two-factor.index');
});
