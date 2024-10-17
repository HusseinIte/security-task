<?php

use App\Enums\TaskStatus;
use App\Http\Controllers\AttachmentController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\UserController;
use App\Models\Permission;
use App\Models\Task;
use App\Models\TaskStatusUpdate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


// ############        Auth Routes    ###################
Route::group(['prefix' => 'auth'], function ($router) {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:api');
    Route::post('/refresh', [AuthController::class, 'refresh'])->middleware('auth:api');
    Route::post('/profile', [AuthController::class, 'profile'])->middleware('auth:api');
});
// ############       User Routes    ###################
Route::middleware(['auth:api', 'role:Admin'])->group(function () {
    Route::apiResource('users', UserController::class);
    Route::post('users/{id}/assign-roles', [UserController::class, 'assignUserRoles']);
    Route::delete('users/{id}/force-delete', [UserController::class, 'forceDelete']);
    Route::post('users/{id}/restore', [UserController::class, 'restore']);
});

// ############          Role Routes    ###################
Route::middleware(['auth:api', 'role:Admin'])->group(function () {
    Route::apiResource('roles', RoleController::class);
    Route::post('roles/{id}/assign-permissions', [RoleController::class, 'assignRolePermissions']);
    Route::delete('roles/{id}/force-delete', [RoleController::class, 'forceDelete']);
    Route::post('roles/{id}/restore', [RoleController::class, 'restore']);
});



// ############        Permission Routes    ###############
Route::middleware(['auth:api', 'role:Admin'])->group(function () {
    Route::apiResource('permissions', PermissionController::class);
    Route::delete('permissions/{id}/force-delete', [PermissionController::class, 'forceDelete']);
    Route::post('permissions/{id}/restore', [PermissionController::class, 'restore']);
});

// ###########   Task Routes #############################
Route::middleware(['auth:api', 'role:Admin'])->group(function () {
    Route::apiResource('tasks', TaskController::class);
    Route::delete('tasks/{id}/force-delete', [TaskController::class, 'forceDelete']);
    Route::post('tasks/{id}/restore', [TaskController::class, 'restore']);
    Route::post('tasks/{id}/attachments', [TaskController::class, 'storeFile']);
    Route::put('tasks/{id}/assign', [TaskController::class, 'assignTask']);
    Route::get('reports/daily-tasks', [TaskController::class, 'generateReportTasks']);
});
Route::post('tasks/{id}/comments', [TaskController::class, 'addComment'])->middleware('auth:api');
Route::put('tasks/{id}/update-status', [TaskController::class, 'updateTaskStatus']);
