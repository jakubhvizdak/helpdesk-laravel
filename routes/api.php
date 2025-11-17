<?php

use App\Http\Controllers\API\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\ProjectController;
use App\Http\Controllers\API\TaskController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\ProfileController;
use App\Http\Controllers\API\TaskCommentController;
use App\Http\Controllers\API\TaskTimeController;
use App\Http\Controllers\API\TaskActivityController;
use App\Http\Controllers\API\TaskStatusController;
use App\Http\Controllers\API\TaskAttachmentController;

Route::post('/login', [AuthController::class, 'login']);
Route::get('/tasks/export-ics', [TaskController::class, 'exportIcs']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/users', [UserController::class, 'index']);
    Route::post('/users', [UserController::class, 'store']);
    Route::put('/users/{user}', [UserController::class, 'update']);
    Route::delete('/users/{user}', [UserController::class, 'destroy']);
    Route::get('/me', [UserController::class, 'me']);
    Route::put('/users/{user}/projects', [UserController::class, 'assignProjectsToUser']);

    Route::get('/projects', [ProjectController::class, 'index']);
    Route::get('/my-projects', [ProjectController::class, 'myProjects']);
    Route::get('/projects/{id}', [ProjectController::class, 'show']);
    Route::get('/projects/{id}/assigned-users', [ProjectController::class, 'users']);
    Route::post('/projects', [ProjectController::class, 'store']);
    Route::put('/projects/{id}', [ProjectController::class, 'update']);
    Route::delete('/projects/{id}', [ProjectController::class, 'destroy']);
    Route::get('/projects/{project}/users', [ProjectController::class, 'projectUsers']);
    Route::put('/projects/{project}/users', [ProjectController::class, 'updateProjectUsers']);

    Route::get('/tasks', [TaskController::class, 'index']);
    Route::get('/tasks/my-requests', [TaskController::class, 'myRequests']);
    Route::get('/tasks/in-progress', [TaskController::class, 'inProgress']);
    Route::get('/tasks/completed', [TaskController::class, 'completed']);
    Route::get('/tasks/{id}', [TaskController::class, 'show']);
    Route::post('/tasks', [TaskController::class, 'store']);
    Route::get('/my-tasks', [TaskController::class, 'myActiveTasks']);
    Route::get('/my-tasks/completed', [TaskController::class, 'myCompletedTasks']);
    Route::patch('/tasks/{task}/status', [TaskStatusController::class, 'updateStatus']);
    Route::get('/task-statuses', [TaskStatusController::class, 'index']);
    Route::get('/tasks/{task}/allowed-transitions', [TaskStatusController::class, 'allowedTransitions']);
    Route::put('/tasks/{task}', [TaskController::class, 'update']);
    Route::post('/tasks/{task}/comments/{comment}/attachments', [TaskAttachmentController::class, 'store']);

    Route::get('/tasks/{taskId}/comments', [TaskCommentController::class, 'index']);
    Route::post('/tasks/{taskId}/comments', [TaskCommentController::class, 'store']);

    Route::get('/tasks/{task}/times', [TaskTimeController::class, 'index']);
    Route::post('/tasks/{task}/times', [TaskTimeController::class, 'store']);
    Route::get('/time-tracking', [TaskTimeController::class, 'getAll']);
    Route::get('/time-tracking/summary', [TaskTimeController::class, 'getSummary']);

    Route::get('/task-activities', [TaskActivityController::class, 'index']);

    Route::get('/profile', [ProfileController::class, 'show']);
    Route::put('/profile', [ProfileController::class, 'update']);

    Route::post('/logout', [AuthController::class, 'logout']);
});
