<?php

use App\Http\Controllers\AssignmentController;
use App\Http\Controllers\FileController;
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


   Route::post('post/{post}/link', 'storeLink');
   
   Route::delete('post/{post}', 'destroy');
   Route::delete('post/link/{link}','destroyLink');
   
   Route::put('post/{post}', 'update');

})->middleware(['auth:sanctum']);

Route::controller(AssignmentController::class)->group(function(){
    
    Route::get('assignments', 'index');
    Route::get('assignment/{assignment}', 'show');

    Route::post('assignment', 'store');
    Route::post('assignment/{assignment}/link', 'storeLink');
    Route::post('assignment/{assignment}/comment', 'storeComment');

    Route::delete('assignment/{assignment}', 'destroy');
    Route::delete('assignment/link/{assignment_link}', 'destroyLink');
    Route::delete('assignment/comment/{assignment_comment}', 'destroyComment');

    Route::put('assignment/comment/{assignment_comment}', 'updateComment');
    Route::put('assignment/{assignment}', 'update');

});

Route::controller(ResponseController::class)->group(function () {
    Route::get('assignment/{assignment}/response', 'index');
});


Route::controller(FileController::class)->group(function ()  {

    Route::post('assignment/{assignment}/file', 'storeAssignment');
    Route::post('post/{post}/file', 'storePost');
    Route::post('solution/{solution}/file', 'storeSolution');
    
    Route::delete('assignment/file/{assignment_file}', 'destroyAssignment');
    Route::delete('post/file/{post_file}', 'destroyPost');
    Route::delete('solution/file/{solution_file}', 'destroySolution');

    
});


