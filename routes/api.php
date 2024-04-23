<?php

use App\Http\Controllers\AssignmentController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ResponseController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\student;
use App\Http\Middleware\studentGroup;
use App\Mail\auth;
use App\Models\Assignment;
use App\Models\Group;
use App\Models\Student_group;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

use function Pest\Laravel\json;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::controller(UserController::class)->group(function(){
    Route::get('dashboard', 'dashboard');
    Route::get('user', 'show')->middleware('auth:sanctum');
    Route::post('user', 'store');
    Route::post('login', 'login');
    Route::post('login-token', 'login_token');
    Route::post('logout', 'logout')->middleware('auth:sanctum');
});

Route::controller(GroupController::class)->group(function(){
    Route::get('groups', 'index')->middleware(['auth:sanctum']);
    Route::get('group/{group}',  'show');
    Route::get('group/{group}/posts', 'getPosts');
});

Route::controller(PostController::class)->group(function ()  {
   Route::post('post', 'store');
   Route::get('posts', 'index');
   Route::get('post/{post}', 'show');
   Route::get('post/{post}/comments', 'getComments');
   Route::get('post/{post}/file/{file}', 'getFile');
   Route::get('post/{post}/file', 'getFiles');



   Route::put('post/{post}', 'update');
   Route::delete('post/{post}', 'destroy');

   Route::post('post/{post}/file', 'storeFile');
   Route::post('post/{post}/link', 'storeLink');

   Route::delete('post/file/{file}','destroyFile');
   Route::delete('post/link/{link}','destroyLink');

})->middleware(['auth:sanctum']);

Route::controller(AssignmentController::class)->group(function(){
    Route::get('assignments', 'index')->middleware('student');
});

Route::controller(ResponseController::class)->group(function () {
    Route::get('assignment/{assignment}/response', 'index');
});



