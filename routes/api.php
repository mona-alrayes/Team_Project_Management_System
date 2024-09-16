<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\NoteController;
use App\Http\Controllers\Api\V1\TaskController;
use App\Http\Controllers\Api\V1\UserController;
use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\V1\ProjectController;
use App\Http\Controllers\Api\V1\ProjectUserController;

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

// Auth routes for login, register, logout, and token refresh
Route::controller(AuthController::class)->group(function () {
    Route::post('login', 'login');
    Route::post('register', 'register');
    Route::post('logout', 'logout');
    Route::post('refresh', 'refresh');
});

// Admin-only routes
Route::group(['middleware' => ['auth:api', 'SystemRole:admin']], function () {
    Route::apiResource('users', UserController::class)->only(['store', 'update', 'delete']);
    Route::put('users/{id}/restore', [UserController::class, 'restoreUser']);
    Route::apiResource('projects', ProjectController::class)->except(['showMyProjectTasks', 'restoreTask']);
    Route::put('projects/{id}/restore', [ProjectController::class, 'restoreProject']);
    Route::apiResource('projects.users', ProjectUserController::class)
        ->only(['store', 'update', 'destroy', 'index']);
});
// User-only routes for task status change
Route::group(['middleware' => 'auth:api'], function () {
    Route::apiResource('projects.tasks', TaskController::class);
    Route::get('projects/tasks', [ProjectController::class, 'showMyProjectTasks']);
    Route::patch('projects/{project}/tasks/{task}/changeStatus', [TaskController::class, 'updateByAssignedUser']);
    Route::post('projects/{project}/tasks/{task}/restore', [TaskController::class, 'restoreTask']);
    Route::apiResource('notes', NoteController::class)->except(['restoreTask', 'show']);
    Route::put('notes/{id}/restore', [NoteController::class, 'restoreProject']);
    Route::get('notes/{Task_id}', [NoteController::class, 'show']);
});
Route::apiResource('users', UserController::class)->except(['store', 'update', 'delete']);

// Route::post('projects/{projectId}/users', [ProjectUserController::class, 'addUserToProject']);
// Route::delete('projects/{projectId}/users/{userId}', [ProjectUserController::class, 'removeUserFromProject']);
// Route::put('projects/{projectId}/users/{userId}', [ProjectUserController::class, 'updateUserInProject']);  //change user role in the project
// Route::get('projects/{projectId}/users', [ProjectUserController::class, 'showUsersInProject']);

