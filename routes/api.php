<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\DiscussionController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProfileApiController;
use App\Http\Controllers\Api\TaskController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    // Profile routes
    Route::get('/profile', [ProfileApiController::class, 'show']);
    Route::put('/profile', [ProfileApiController::class, 'update']);
    Route::delete('/profile', [ProfileApiController::class, 'destroy']);

    // Diskusi Routes (API untuk diskusi)
    Route::get('/diskusi', [DiscussionController::class, 'index']);  // Mendapatkan semua diskusi
    Route::post('/diskusi', [DiscussionController::class, 'store']);  // Membuat diskusi baru
    Route::get('/diskusi/{diskusi}', [DiscussionController::class, 'show']);  // Melihat diskusi tertentu
    Route::post('/diskusi/{diskusi}/balasan', [DiscussionController::class, 'storeReply']);  // Membalas diskusi
// Task Routes API
    Route::get('/tasks', [TaskController::class, 'getAllTasks']);  // Daftar semua tugas
    Route::get('/tasks/recent', [TaskController::class, 'getRecentTasks']);  // Tugas terbaru
    Route::get('/tasks/history', [TaskController::class, 'getTaskHistory']);  // Sejarah tugas
    Route::get('/tasks/statistics', [TaskController::class, 'getTaskStatistics']);  // Statistik tugas
    Route::post('/tasks', [TaskController::class, 'store']);  // Menambah tugas
    Route::put('/tasks/{task}', [TaskController::class, 'update']);  // Memperbarui tugas
    Route::delete('/tasks/{task}', [TaskController::class, 'destroy']);  // Menghapus tugas
    Route::put('/tasks/{task}/toggle-complete', [TaskController::class, 'toggleComplete']);  // Toggle status tugas
});