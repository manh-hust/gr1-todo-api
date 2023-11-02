<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\TaskController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\NotificationController;
use Illuminate\Support\Facades\Auth;

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

Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);

    Route::get('/google', [AuthController::class, 'redirectToGoogle']);
    Route::get('/google/callback', [AuthController::class, 'handleGoogleCallback']);

    Route::post('/forgot-password', [AuthController::class, 'sendForgotPasswordEmail'])->name('password.request');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.reset');

    Route::middleware(['auth:sanctum', 'abilities:user'])->group(
        function () {
            Route::get('/user/info', [UserController::class, 'getUser']);
            Route::post('/logout', [AuthController::class, 'logout']);
            Route::post('/change-password', [AuthController::class, 'changePassword']);
        }
    );

    Route::prefix('admin')->group(
        function () {
            Route::post('/login', [AuthController::class, 'adminLogin']);

            Route::middleware(['auth:sanctum', 'abilities:admin'])->group(
                function () {
                    Route::post('/logout', [AuthController::class, 'logout']);
                    Route::get('/info', [UserController::class, 'getUser']);
                }
            );
        }
    );
});

Route::prefix('tasks')->group(
    function () {
        Route::middleware('auth:sanctum')->group(
            function () {
        Route::get('/sharing', [TaskController::class, 'getSharingTasks']);
        Route::get('/{type}', [TaskController::class, 'getTasks']);
        Route::get('/{id}', [TaskController::class, 'getTask']);

        Route::post('/', [TaskController::class, 'createTask']);
        Route::post('/{id}/edit', [TaskController::class, 'updateTask']);
        Route::post('/{id}/status', [TaskController::class, 'updateStatus']);
        Route::post('/{id}/delete', [TaskController::class, 'deleteTask']);

        Route::post('/{id}/members', [TaskController::class, 'addMember']);
        Route::delete('/{id}/members/{memberId}', [TaskController::class, 'removeMember']);
            }
        );
    }
);

Route::get('/members', [UserController::class, 'getMembers']);

Route::prefix('/notifications')->group(
    function () {
        Route::middleware('auth:sanctum')->group(
            function () {
                Route::get('/', [NotificationController::class, 'getNotifications']);
                Route::post('/', [NotificationController::class, 'createNotification']);
                Route::post('/{id}', [NotificationController::class, 'updateNotification']);
                Route::delete('/{id}', [NotificationController::class, 'deleteNotification']);
            }
        );
    }
);

Route::prefix('tags')->group(function () {
    Route::get('/', [TagController::class, 'getTags']);
    Route::post('/', [TagController::class, 'createTag']);
    Route::post('/{id}', [TagController::class, 'updateTag']);
    Route::delete('/{id}', [TagController::class, 'deleteTag']);
});