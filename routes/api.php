<?php

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
    // --------admin can create , delete and restore users accounts ---------------------------------------------------------
    Route::apiResource('users', UserController::class)->only(['store', 'update', 'delete']);
    Route::put('users/{id}/restore', [UserController::class, 'restoreUser']);
    //----------------------------------------------------------------------------------------------------------------------
    //------- admin can add users to projects , remove them or update them to pivot table -----------------------------------
    Route::post('projects/{projectId}/users', [ProjectUserController::class, 'addUserToProject']);
    Route::delete('projects/{projectId}/users/{userId}', [ProjectUserController::class, 'removeUserFromProject']);
    Route::put('projects/{projectId}/users/{userId}', [ProjectUserController::class, 'updateUserInProject']);  //change user role in the project
    Route::get('projects/{projectId}/users', [ProjectUserController::class, 'showUsersInProject']);
    //------------------------------------------------------------------------------------------------------------------------
    //--------- admin can add , update or delete or restore projects ---------------------------------------------------
    Route::apiResource('projects', ProjectController::class)->only(['store', 'update', 'delete']);
    Route::put('projects/{id}/restore', [ProjectController::class, 'restoreProject']);
    //-----------------------------------------------------------------------------------------------------------------------
});
// User-only routes for task status change
Route::group(['middleware' => 'auth:api'], function () {
    // ----- develper can change the task status and the role is handled in form request -----------------------------
    Route::patch('projects/{project}/tasks/{task}/changeStatus', [TaskController::class, 'updateByAssignedUser']);
    //------------------------------------------------------------------------------------------------------------------
    Route::apiResource('projects.tasks', TaskController::class)->only(['index','show','update','destroy']);
    Route::post('projects/{project}/tasks/{task}/restore', [TaskController::class, 'restoreTask']);
    //---- tester can add notes , update it or delete it depending on the role that's been tested in form request ----------------
    Route::apiResource('notes', NoteController::class)->except(['restoreNote', 'show']);
    Route::put('notes/{id}/restore', [NoteController::class, 'restoreProject']);
    //---- auth user can see notes on task ------------------------------------
    Route::get('notes/{Task_id}', [NoteController::class, 'show']);
    //--------------------------------------------------------------------------------------
    // Custom Route - Get a user's project tasks using hasManyThrough Relationship---------------------------------------------------------
    Route::get('projects/showMyTasks', [ProjectController::class, 'getMyProjectTasks'])->name('projects.getMyProjectTasks');
    //----------------------------------------------------------------------------------------------------
    // Index - Get all projects --------------------------------------------------------------------------
    Route::get('projects', [ProjectController::class, 'index'])->name('projects.index');
    //----------------------------------------------------------------------------------------------------
    // Show - Get a specific project by ID ---------------------------------------------------------------
    Route::get('projects/{id}', [ProjectController::class, 'show'])->name(name: 'projects.show');
    // --------------- index and show for users --------------------------------------------------------------
    Route::apiResource('users', UserController::class)->except(['store', 'update', 'delete']);
    //---------------------------------------------------------------------------------------------------------------------------
});
