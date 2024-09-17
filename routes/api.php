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
     Route::post('projects/{projectId}/users', [ProjectUserController::class, 'addUserToProject']);
     Route::delete('projects/{projectId}/users/{userId}', [ProjectUserController::class, 'removeUserFromProject']);
     Route::put('projects/{projectId}/users/{userId}', [ProjectUserController::class, 'updateUserInProject']);  //change user role in the project
     Route::get('projects/{projectId}/users', [ProjectUserController::class, 'showUsersInProject']);

});
// User-only routes for task status change
Route::group(['middleware' => 'auth:api'], function () {
    Route::patch('projects/{project}/tasks/{task}/changeStatus', [TaskController::class, 'updateByAssignedUser']);
    Route::post('projects/{project}/tasks/{task}/restore', [TaskController::class, 'restoreTask']);
    Route::apiResource('notes', NoteController::class)->except(['restoreTask', 'show']);
    Route::put('notes/{id}/restore', [NoteController::class, 'restoreProject']);
    Route::get('notes/{Task_id}', [NoteController::class, 'show']);
});
Route::apiResource('users', UserController::class)->except(['store', 'update', 'delete']);



// --------- routes of project Controller --------------------------------------------
Route::group(['middleware' => 'auth:api'], function () {
// Custom Route - Get a user's project tasks
Route::get('projects/showMyTasks', [ProjectController::class, 'getMyProjectTasks'])->name('projects.getMyProjectTasks');
// Index - Get all projects
Route::get('projects', [ProjectController::class, 'index'])->name('projects.index');
// Show - Get a specific project by ID
Route::get('projects/{id}', [ProjectController::class, 'show'])->name(name: 'projects.show');

});

Route::group(['middleware' => ['auth:api', 'SystemRole:admin']], function () {
// Store - Create a new project
Route::post('projects', [ProjectController::class, 'store'])->name('projects.store');
// Update - Update a specific project by ID
Route::put('projects/{id}', [ProjectController::class, 'update'])->name('projects.update');
// Destroy - Delete a specific project by ID
Route::delete('projects/{id}', [ProjectController::class, 'destroy'])->name('projects.destroy');
// Restore - Restore a specific project by ID
Route::put('projects/{id}/restore', [ProjectController::class, 'restoreProject']);

});
//---------------------------------------------------------