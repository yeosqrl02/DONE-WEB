<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProfileApiController;
use App\Http\Controllers\Api\TaskApiController;
use App\Http\Controllers\Api\DiscussionController;
use App\Http\Controllers\Api\CourseApiController; // 🆕 Tambahkan ini

// 🛡️ Autentikasi
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// 🛡️ Route yang dilindungi (perlu token)
Route::middleware('auth:sanctum')->group(function () {

    // 👤 Profile
    Route::get('/profile', [ProfileApiController::class, 'show']);
    Route::put('/profile', [ProfileApiController::class, 'update']);
    Route::delete('/profile', [ProfileApiController::class, 'destroy']);
    Route::post('/profile/upload-avatar', [ProfileApiController::class, 'uploadAvatar']);

    // 📚 Tugas (Task)
    Route::get('/tasks', [TaskApiController::class, 'getAllTasks']);
    Route::get('/tasks/recent', [TaskApiController::class, 'getRecentTasks']);
    Route::get('/tasks/history', [TaskApiController::class, 'getTaskHistory']);
    Route::get('/tasks/statistics', [TaskApiController::class, 'getTaskStatistics']);
    Route::post('/tasks', [TaskApiController::class, 'store']);
    Route::put('/tasks/{task}', [TaskApiController::class, 'update']);
    Route::delete('/tasks/{task}', [TaskApiController::class, 'destroy']);
    Route::put('/tasks/{task}/toggle-complete', [TaskApiController::class, 'toggleComplete']);

    // 💬 Diskusi
    Route::get('/diskusi', [DiscussionController::class, 'index']);
    Route::post('/diskusi', [DiscussionController::class, 'store']);
    Route::get('/diskusi/{diskusi}', [DiscussionController::class, 'show']);
    Route::post('/diskusi/{diskusi}/balasan', [DiscussionController::class, 'storeReply']);

    // 📘 Mata Kuliah (Course) — 🆕 API
    Route::get('/courses', [CourseApiController::class, 'index']);
    Route::post('/courses', [CourseApiController::class, 'store']);
    Route::get('/courses/{id}', [CourseApiController::class, 'show']);
    Route::put('/courses/{id}', [CourseApiController::class, 'update']);
    Route::delete('/courses/{id}', [CourseApiController::class, 'destroy']);

    // 🔓 Logout
    Route::post('/logout', [AuthController::class, 'logout']);
});
