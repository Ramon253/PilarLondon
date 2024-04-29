<?php

use App\Http\Controllers\AssignmentController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\LinkController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ResponseController;
use App\Http\Controllers\StudentController;
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

Route::controller(UserController::class)->group(function () {
    Route::get('user', 'show')->middleware('auth:sanctum');

    Route::post('user', 'store');
    Route::post('login', 'login');
    Route::post('login-token', 'login_token');

    Route::post('logout', 'logout')->middleware('auth:sanctum');
    Route::post('activate', 'activate')->middleware('auth:sanctum');
    Route::post('verify', 'verify')->middleware('auth:sanctum');
});


Route::controller(StudentController::class)->group(function () {
    Route::post('student', 'store')->middleware(['auth:sanctum']);

    Route::middleware(['auth:sanctum', 'student'])->group(function () {

        Route::get('dashboard', 'dashboard');
        Route::get('profile', 'show');


        Route::delete('student', 'destroy');

        Route::put('student', 'update');
    });
});


Route::controller(GroupController::class)->group(function () {
    Route::get('groups', 'index')->middleware(['auth:sanctum']);
    Route::get('group/{group}',  'show');
    Route::get('group/{group}/posts', 'showPosts');
    Route::get('group/{group}/assignments', 'showAssignments');

    Route::post('group/{group}', 'store');
    Route::post('group/{group}/join', 'join')->middleware(['auth:sanctum', 'student']);

    Route::delete('group/{group}', 'destroy');
    Route::delete('group/{group}/kick', 'kick')->middleware(['student']);
});

Route::controller(PostController::class)->group(function () {

    Route::get('posts', 'index');
    Route::get('post/{post}', 'show');

    Route::post('post', 'store');

    Route::delete('post/{post}', 'destroy');

    Route::put('post/{post}', 'update');
})->middleware(['auth:sanctum']);

Route::controller(AssignmentController::class)->group(function () {

    Route::get('assignments', 'index');
    Route::get('assignment/{assignment}', 'show')->middleware('student');

    Route::post('assignment', 'store');

    Route::delete('assignment/{assignment}', 'destroy');

    Route::put('assignment/{assignment}', 'update');
});


Route::controller(ResponseController::class)->group(function () {

    Route::get('solution/{solution}', 'show');

    Route::post('assignment/{assignment}/response', 'store');

    Route::put('solution/{solution}', 'update');
    Route::put('solution/{solution}/grade', 'grade');

    Route::delete('solution/{solution}', 'destroy');
});



Route::controller(FileController::class)->group(function () {

    Route::get('assignment/file/{assignment_file}', 'showAssignment');
    Route::get('post/file/{post_file}', 'showPost');
    Route::get('solution/file/{solution_file}', 'showSolution');

    Route::get('assignment/file/{assignment_file}/download', 'downloadAssignment');
    Route::get('post/file/{post_file}/download', 'downloadPost');
    Route::get('solution/file/{solution_file}/download', 'downloadSolution');

    Route::post('assignment/{assignment}/file', 'storeAssignment');
    Route::post('post/{post}/file', 'storePost');
    Route::post('solution/{solution}/file', 'storeSolution');

    Route::delete('assignment/file/{assignment_file}', 'destroyAssignment');
    Route::delete('post/file/{post_file}', 'destroyPost');
    Route::delete('solution/file/{solution_file}', 'destroySolution');
});

Route::controller(LinkController::class)->group(function () {

    Route::get('assignment/link/{assignment_link}', 'showAssignment');
    Route::get('post/link/{post_link}', 'showPost');
    Route::get('solution/link/{solution_link}', 'showSolution');

    Route::post('assignment/{assignment}/link', 'storeAssignment');
    Route::post('post/{post}/link', 'storePost');
    Route::post('solution/{solution}/link', 'storeSolution');

    Route::delete('assignment/link/{assignment_link}', 'destroyAssignment');
    Route::delete('post/link/{post_link}', 'destroyPost');
    Route::delete('solution/link/{solution_link}', 'destroySolution');
});

Route::controller(CommentController::class)->group(function () {

    Route::get('assignment/comment/{assignment_comment}', 'showAssignment');
    Route::get('post/comment/{post_comment}', 'showPost');
    Route::get('solution/comment/{solution_comment}', 'showSolution');

    Route::post('assignment/{assignment}/comment', 'storeAssignment');
    Route::post('post/{post}/comment', 'storePost')->middleware('auth:sanctum');
    Route::post('solution/{solution}/comment', 'storeSolution');

    Route::delete('assignment/comment/{assignment_comment}', 'destroyAssignment');
    Route::delete('post/comment/{post_comment}', 'destroyPost');
    Route::delete('solution/comment/{solution_comment}', 'destroySolution');

    Route::put('assignment/comment/{assignment_comment}', 'updateAssignment');
    Route::put('post/comment/{post_comment}', 'updatePost');
    Route::put('solution/comment/{solution_comment}', 'updateSolution');
});
