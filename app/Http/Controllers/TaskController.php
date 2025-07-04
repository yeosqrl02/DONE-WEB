<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\Course;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        $tasks = Task::with('course')
            ->where('user_id', $user->id)
            ->orderBy('tanggal', 'asc')
            ->get();

        $courses = Course::where('user_id', $user->id)->get();

        $taskHistory = Task::with('course')
            ->where('user_id', $user->id)
            ->where('completed', true)
            ->orderBy('completed_at', 'desc')
            ->get();

        if ($request->ajax()) {
            return view('tasks.partials.task_tbody', compact('tasks', 'courses'));
        }

        return view('tasks.index', compact('tasks', 'courses', 'taskHistory'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'tanggal' => 'required|date',
            'course_id' => 'required|exists:courses,id',
        ]);

        $course = Course::where('id', $validated['course_id'])
                        ->where('user_id', Auth::id())
                        ->first();

        if (!$course) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Mata kuliah tidak valid atau bukan milik Anda.'
                ], 403);
            }
            return redirect()->back()->withErrors('Mata kuliah tidak valid atau bukan milik Anda.');
        }

        $validated['user_id'] = Auth::id();

        try {
            $task = Task::create($validated);
            $task->load('course');
        } catch (\Exception $e) {
            Log::error('Gagal simpan tugas: ' . $e->getMessage());

            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Gagal menambah tugas.'], 500);
            }
            return redirect()->back()->withErrors('Gagal menambah tugas.');
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Tugas berhasil ditambahkan.',
                'task' => $task,
                'course_name' => $task->course->name ?? '-',
            ]);
        }

        return redirect()->back()->with('success', 'Tugas berhasil ditambahkan.');
    }

    public function update(Request $request, Task $task)
    {
        if ($task->user_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 403);
        }

        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'tanggal' => 'required|date',
            'course_id' => 'required|exists:courses,id',
        ]);

        $course = Course::where('id', $validated['course_id'])
                        ->where('user_id', Auth::id())
                        ->first();

        if (!$course) {
            return response()->json([
                'success' => false,
                'message' => 'Mata kuliah tidak valid atau bukan milik Anda.'
            ], 403);
        }

        try {
            $task->update($validated);
            $task->load('course');
        } catch (\Exception $e) {
            Log::error('Gagal update tugas: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Gagal memperbarui tugas.'], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Tugas berhasil diperbarui.',
            'task' => $task,
            'course_name' => $task->course->name ?? '-',
        ]);
    }

    public function destroy(Task $task)
    {
        if ($task->user_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 403);
        }

        try {
            $task->delete();
        } catch (\Exception $e) {
            Log::error('Gagal hapus tugas: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Gagal menghapus tugas.'], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Tugas berhasil dihapus.'
        ]);
    }

    public function toggleComplete(Request $request, Task $task)
    {
        if ($task->user_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 403);
        }

        $validated = $request->validate(['completed' => 'required|boolean']);

        try {
            $task->completed = $validated['completed'];
            $task->completed_at = $validated['completed'] ? now() : null;
            $task->save();
        } catch (\Exception $e) {
            Log::error('Gagal update status tugas: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Gagal memperbarui status tugas.'], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Status tugas berhasil diperbarui.',
            'completed' => $task->completed,
        ]);
    }

    public function getRecentTasks()
    {
        $tasks = Task::where('user_id', Auth::id())
            ->orderBy('tanggal', 'asc')
            ->get(['id', 'nama', 'tanggal', 'completed']);

        return response()->json($tasks);
    }

    public function getAllTasks()
    {
        $tasks = Task::with('course')
            ->where('user_id', Auth::id())
            ->orderBy('tanggal', 'asc')
            ->get()
            ->map(function ($task) {
                return [
                    'id' => $task->id,
                    'nama' => $task->nama,
                    'deskripsi' => $task->deskripsi,
                    'tanggal' => $task->tanggal,
                    'course_name' => $task->course->name ?? '-',
                    'completed' => $task->completed,
                ];
            });

        return response()->json($tasks);
    }

    public function getTaskHistory()
    {
        $tasks = Task::with('course')
            ->where('user_id', Auth::id())
            ->where('completed', true)
            ->orderBy('completed_at', 'desc')
            ->get()
            ->map(function ($task) {
                return [
                    'id' => $task->id,
                    'nama' => $task->nama,
                    'deskripsi' => $task->deskripsi,
                    'tanggal' => $task->tanggal,
                    'course_name' => $task->course->name ?? '-',
                    'completed_at' => optional($task->completed_at)->format('d F Y H:i'),
                ];
            });

        return response()->json($tasks);
    }

    public function getTaskStatistics()
    {
        $now = Carbon::now();

        $totalTasks = Task::where('user_id', Auth::id())->count();
        $completedTasks = Task::where('user_id', Auth::id())->where('completed', true)->count();
        $lateTasks = Task::where('user_id', Auth::id())
            ->where('completed', false)
            ->where('tanggal', '<', $now)
            ->count();
        $upcomingTasks = Task::where('user_id', Auth::id())
            ->where('completed', false)
            ->where('tanggal', '>=', $now)
            ->count();

        return response()->json([
            'tasks' => $totalTasks,
            'completed' => $completedTasks,
            'late' => $lateTasks,
            'upcoming' => $upcomingTasks,
        ]);
    }
}
