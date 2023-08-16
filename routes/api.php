<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\TaskController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TagController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('auth')->group(function () {
    Route::get('/google', [AuthController::class, 'redirectToGoogle']);
    Route::get('/google/callback', [AuthController::class, 'handleGoogleCallback']);

    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);

    Route::middleware('auth:sanctum')->group(
        function () {
            Route::post('/logout', [AuthController::class, 'logout']);
            Route::get('/user', [UserController::class, 'getUser']);
        }
    );
});

Route::prefix('tasks')->group(function () {
    Route::middleware('auth:sanctum')->group(
        function () {
            Route::get('/', [TaskController::class, 'getTasks']);
            Route::get('/shared', [TaskController::class, 'getSharedTasks']);
            Route::get('/history', [TaskController::class, 'getHistoryTasks']);
            Route::get('/{id}', [TaskController::class, 'getTask']);

            Route::post('/', [TaskController::class, 'createTask']);
            Route::post('/{id}/edit', [TaskController::class, 'updateTask']);
            Route::post('/{id}/status', [TaskController::class, 'updateStatus']);
            Route::post('/{id}/delete', [TaskController::class, 'deleteTask']);

            Route::post('/{id}/members', [TaskController::class, 'addMember']);
            Route::delete('/{id}/members/{memberId}', [TaskController::class, 'removeMember']);
        }
    );
});


Route::get('/members', [UserController::class, 'getMembers']);

Route::prefix('tags')->group(function () {
    Route::get('/', [TagController::class, 'getTags']);
    Route::post('/', [TagController::class, 'createTag']);
    Route::post('/{id}', [TagController::class, 'updateTag']);
    Route::delete('/{id}', [TagController::class, 'deleteTag']);
});
