<?php

use App\Http\Controllers\GroupController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\student;
use App\Http\Middleware\studentGroup;
use App\Mail\auth;
use App\Models\Group;
use App\Models\Student_group;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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
    Route::post('user', 'store');
    Route::post('login', 'login');
    Route::post('logout', 'logout');
});

Route::controller(GroupController::class)->group(function(){
    Route::get('groups', 'index');
    Route::get('group/{group}',  'show')->middleware(['auth:sanctum', 'studentGroup:group']);
    
});


Route::middleware('auth')->get('/user', function (Request $request) {
    return $request->user();
});




