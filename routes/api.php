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
use App\Http\Controllers\API\TaskEditLogController;
use App\Http\Controllers\API\TaskStatusTransitionController;
use App\Http\Controllers\API\NotificationController;
use App\Http\Controllers\API\ProjectDocumentationController;
use App\Http\Controllers\API\DashboardController;

Route::post('/login', [AuthController::class, 'login']);
Route::get('/version', function () {
    return response()->json(['version' => env('APP_VERSION', 'dev')]);
});
Route::get('/tasks/export-ics', [TaskController::class, 'exportIcs']);

Route::middleware('jwt')->group(function () {
    Route::get('/users', [UserController::class, 'index']);
    Route::post('/users', [UserController::class, 'store']);
    Route::put('/users/{user}', [UserController::class, 'update']);
    Route::delete('/users/{user}', [UserController::class, 'destroy']);
    Route::get('/me', [UserController::class, 'me']);
    Route::put('/users/{user}/projects', [UserController::class, 'assignProjectsToUser']);
    Route::get('/dashboard', [DashboardController::class, 'dashboard']);

    Route::get('/projects', [ProjectController::class, 'index']);
    Route::get('/my-projects', [ProjectController::class, 'myProjects']);
    Route::get('/projects/{id}', [ProjectController::class, 'show']);
    Route::get('/projects/{id}/assigned-users', [ProjectController::class, 'users']);
    Route::post('/projects', [ProjectController::class, 'store']);
    Route::put('/projects/{id}', [ProjectController::class, 'update']);
    Route::delete('/projects/{id}', [ProjectController::class, 'destroy']);
    Route::get('/projects/{project}/users', [ProjectController::class, 'projectUsers']);
    Route::put('/projects/{project}/users', [ProjectController::class, 'updateProjectUsers']);
    Route::get('/project/{project}/documentation', [ProjectDocumentationController::class, 'index']);
    Route::post('/project/{project}/documentation', [ProjectDocumentationController::class, 'store']);
    Route::put('/project/{project}/documentation/{section}', [ProjectDocumentationController::class, 'update']);
    Route::delete('/project/{project}/documentation/{section}', [ProjectDocumentationController::class, 'destroy']);

    Route::get('/tasks', [TaskController::class, 'index']);
    Route::get('/tasks/{id}', [TaskController::class, 'show']);
    Route::post('/tasks', [TaskController::class, 'store']);
    Route::get('/tasks/{task}/edit-log', [TaskEditLogController::class, 'index']);
    Route::patch('/tasks/{task}/status', [TaskStatusController::class, 'updateStatus']);
    Route::get('/task-statuses', [TaskStatusController::class, 'index']);
    Route::get('/tasks/{task}/allowed-transitions', [TaskStatusController::class, 'allowedTransitions']);
    Route::put('/tasks/{task}', [TaskController::class, 'update']);
    Route::post('/tasks/{task}/comments/{comment}/attachments', [TaskAttachmentController::class, 'store']);

    Route::get('/status-transitions', [TaskStatusTransitionController::class, 'index']);
    Route::post('/status-transitions', [TaskStatusTransitionController::class, 'store']);
    Route::delete('/status-transitions/{id}', [TaskStatusTransitionController::class, 'destroy']);

    Route::get('/tasks/{taskId}/comments', [TaskCommentController::class, 'index']);
    Route::post('/tasks/{taskId}/comments', [TaskCommentController::class, 'store']);

    Route::get('/notifications/show-in-app-notification', [NotificationController::class, 'getShowInAppNotification']);
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::get('/notifications/unread', [NotificationController::class, 'unread']);
    Route::patch('/notifications/{id}', [NotificationController::class, 'markRead']);
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllRead']);

    Route::get('/tasks/{task}/times', [TaskTimeController::class, 'index']);
    Route::post('/tasks/{task}/times', [TaskTimeController::class, 'store']);
    Route::get('/time-tracking', [TaskTimeController::class, 'getAll']);

    Route::get('/task-activities', [TaskActivityController::class, 'index']);

    Route::get('/profile', [ProfileController::class, 'show']);
    Route::put('/profile', [ProfileController::class, 'update']);
    Route::get('/profile/{id}', [ProfileController::class, 'getUserInfo']);

    Route::post('/logout', [AuthController::class, 'logout']);
});
