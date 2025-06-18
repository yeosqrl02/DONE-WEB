<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\Course;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;

class TaskApiController extends Controller
{
    // Ambil semua tugas user
    public function index(Request $request)
    {
        $user = Auth::user();

        $tasks = Task::with('course')
            ->where('user_id', $user->id)
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

        return response()->json([
            'success' => true,
            'tasks' => $tasks
        ]);
    }

    // Tambah tugas baru
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'tanggal' => 'required|date',
            'course_name' => 'required|string|max:255',
        ]);

        // Cari course berdasarkan nama & milik user
        $course = Course::where('name', $validated['course_name'])
            ->where('user_id', Auth::id())
            ->first();

        if (!$course) {
            return response()->json([
                'success' => false,
                'message' => 'Mata kuliah tidak ditemukan atau bukan milik Anda.'
            ], 404);
        }

        $validated['user_id'] = Auth::id();
        $validated['course_id'] = $course->id;

        try {
            $task = Task::create($validated);
            $task->load('course');
        } catch (QueryException $e) {
            Log::error('Gagal simpan tugas: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambah tugas.'
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Tugas berhasil ditambahkan.',
            'task' => $task,
            'course_name' => $task->course->name ?? '-',
        ]);
    }

    // Update tugas
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

        // Pastikan course milik user
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
        } catch (QueryException $e) {
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

    // Hapus tugas
    public function destroy(Task $task)
    {
        if ($task->user_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 403);
        }

        try {
            $task->delete();
        } catch (QueryException $e) {
            Log::error('Gagal hapus tugas: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Gagal menghapus tugas.'], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Tugas berhasil dihapus.'
        ]);
    }

    // Toggle status tugas selesai
    public function toggleComplete(Request $request, Task $task)
    {
        if ($task->user_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 403);
        }

        $validated = $request->validate(['completed' => 'required|boolean']);

        try {
            $task->completed = $validated['completed'];
            $task->save();
        } catch (QueryException $e) {
            Log::error('Gagal update status tugas: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Gagal memperbarui status tugas.'], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Status tugas berhasil diperbarui.',
            'completed' => $task->completed,
        ]);
    }

    // Tugas terakhir (sidebar/summary)
    public function getRecentTasks()
    {
        $tasks = Task::where('user_id', Auth::id())
            ->orderBy('tanggal', 'asc')
            ->get(['id', 'nama', 'tanggal', 'completed']);

        return response()->json($tasks);
    }

    // Tugas lengkap (tampilan utama)
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
                    'course_id' => $task->course_id,
                    'course_name' => $task->course->name ?? '-',
                    'completed' => $task->completed,
                ];
            });

        return response()->json($tasks);
    }

    // Tugas yang sudah selesai
    public function getTaskHistory()
    {
        $tasks = Task::with('course')
            ->where('user_id', Auth::id())
            ->where('completed', true)
            ->orderBy('updated_at', 'desc')
            ->get()
            ->map(function ($task) {
                return [
                    'id' => $task->id,
                    'nama' => $task->nama,
                    'deskripsi' => $task->deskripsi,
                    'tanggal' => $task->tanggal,
                    'course_name' => $task->course->name ?? '-',
                    'completed_at' => $task->updated_at->format('d F Y H:i'),
                ];
            });

        return response()->json($tasks);
    }

    // Statistik tugas
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
