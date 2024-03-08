<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/


Route::view('/login', 'user.login');

Route::controller(UserController::class)->group(function () {
    Route::get('/' , 'index');
    Route::post('/login', 'login')->name('login');
    Route::post('/logout', 'logout');
    Route::post('/verify', 'verify');
    Route::get('/verify', 'getVerify');
});

Route::resources([
    'user' => UserController::class,
]);
