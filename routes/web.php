<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\DiscussionController;
use App\Http\Controllers\ReplyController;
use App\Http\Controllers\DashboardController;

// Root redirect: jika sudah login ke dashboard, jika belum ke landing page
Route::get('/', function () {
    return Auth::check() ? redirect()->route('dashboard') : redirect()->route('landing');
});

// Landing page (sebelum login)
Route::get('/welcome', function () {
    return view('landing');
})->name('landing');

// Dashboard (harus login dan terverifikasi)
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// Semua route yang harus login
Route::middleware(['auth'])->group(function () {
    // Profile routes
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Calendar
    Route::get('/calendar/{year?}/{month?}', [CalendarController::class, 'index'])->name('calendar');

    // Task CRUD
    Route::get('/tasks', [TaskController::class, 'index'])->name('tasks');
    Route::post('/tasks', [TaskController::class, 'store'])->name('tasks.store');
    Route::put('/tasks/{task}', [TaskController::class, 'update'])->name('tasks.update');
    Route::delete('/tasks/{task}', [TaskController::class, 'destroy'])->name('tasks.destroy');

    // Toggle status selesai
    Route::post('/tasks/{task}/toggle-complete', [TaskController::class, 'toggleComplete'])->name('tasks.toggleComplete');

    // API fetch data tugas
    Route::get('/api/tasks/recent', [TaskController::class, 'getRecentTasks'])->name('api.tasks.recent');
    Route::get('/api/tasks/all', [TaskController::class, 'getAllTasks'])->name('api.tasks.all');
    Route::get('/api/tasks/statistics', [TaskController::class, 'getTaskStatistics'])->name('api.tasks.statistics');

    // API fetch tugas selesai (history)
    Route::get('/api/tasks/history', [TaskController::class, 'getTaskHistory'])->name('api.tasks.history');

    // Partial untuk reload tbody tabel tugas AJAX
    Route::get('/tasks/partial/tbody', function () {
        $userId = Auth::id();
        $tasks = \App\Models\Task::with('course')
            ->where('user_id', $userId)
            ->orderBy('tanggal', 'asc')
            ->get();
        $courses = \App\Models\Course::all();
        return view('tasks.partials.task_tbody', compact('tasks', 'courses'));
    })->middleware('auth')->name('tasks.partial.tbody');

    // Course CRUD
    Route::post('/courses', [CourseController::class, 'store'])->name('courses.store');
    Route::get('/courses/{course}/edit', [CourseController::class, 'edit'])->name('courses.edit');
    Route::put('/courses/{course}', [CourseController::class, 'update'])->name('courses.update');
    Route::delete('/courses/{course}', [CourseController::class, 'destroy'])->name('courses.destroy');

    // Discussion & Reply
    Route::get('/discussion', [DiscussionController::class, 'index'])->name('discussion');
    Route::post('/discussions', [DiscussionController::class, 'store'])->name('discussions.store');
    Route::post('/replies', [ReplyController::class, 'store'])->name('replies.store');
});

require __DIR__.'/auth.php';
