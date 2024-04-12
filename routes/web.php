<?php

use App\Http\Controllers\StudentController;
use App\Http\Controllers\GroupController;
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
/*

Route::view('/login', 'user.login');
Route::view('/', 'welcome');


Route::controller(UserController::class)->group(function () {
    Route::post('login', 'login')->name('login');
    Route::post('/logout', 'logout')->middleware('auth');
    Route::post('/verify', 'verify');
    Route::get('/verify', 'getVerify')->middleware('auth');
});

Route::controller(StudentController::class)->middleware('auth')->group(function () {
    Route::get('student', 'show');
    Route::get('student/create', 'create');
    Route::post('student', 'store' );
    Route::get('student/edit', 'edit');
    Route::put('student', 'update' );
});

Route::controller(GroupController::class)->middleware('auth')->group(function (){
    Route::get('group', 'index');
    Route::post('group/{group}', 'store');
    Route::delete('group/{group}', 'destroy');
    Route::get('group/{group}', 'show');
});


Route::resources([
    'user' => UserController::class
]);
*/