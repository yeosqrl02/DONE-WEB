<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\Course;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class TaskController extends Controller
{
    // Halaman daftar tugas dan mata kuliah
    public function index(Request $request)
    {
        $tasks = Task::with('course')->orderBy('tanggal', 'asc')->get();
        $courses = Course::all();

        // Jika request AJAX, kembalikan partial tbody untuk reload tabel saja
        if ($request->ajax()) {
            return view('tasks.partials.task_tbody', compact('tasks', 'courses'));
        }

        return view('tasks.index', compact('tasks', 'courses'));
    }

    // Simpan tugas baru
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'tanggal' => 'required|date',
            'course_id' => 'required|exists:courses,id',
        ]);

        try {
            $task = Task::create($validated);
            $task->load('course');
        } catch (\Exception $e) {
            Log::error('Gagal simpan tugas: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Gagal menambah tugas.'], 500);
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
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'tanggal' => 'required|date',
            'course_id' => 'required|exists:courses,id',
        ]);

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

    // Hapus tugas
    public function destroy(Task $task)
    {
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

    // Toggle status completed tugas
    public function toggleComplete(Request $request, Task $task)
    {
        $validated = $request->validate(['completed' => 'required|boolean']);

        try {
            $task->completed = $validated['completed'];
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

    // API daftar tugas untuk sidebar (semua tugas, lengkap dengan completed)
    public function getRecentTasks()
    {
        $tasks = Task::orderBy('tanggal', 'asc')->get(['id', 'nama', 'tanggal', 'completed']);
        return response()->json($tasks);
    }

    // API daftar semua tugas lengkap untuk halaman tasks/index
    public function getAllTasks()
    {
        $tasks = Task::with('course')->orderBy('tanggal', 'asc')->get()->map(function ($task) {
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

    // API: Mendapatkan daftar tugas yang sudah selesai (history) untuk halaman tugas
    public function getTaskHistory()
    {
        $tasks = Task::with('course')
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

    // API statistik tugas untuk dashboard
    public function getTaskStatistics()
    {
        $now = Carbon::now();

        $totalTasks = Task::count();
        $completedTasks = Task::where('completed', true)->count();
        $lateTasks = Task::where('completed', false)->where('tanggal', '<', $now)->count();
        $upcomingTasks = Task::where('completed', false)->where('tanggal', '>=', $now)->count();

        return response()->json([
            'tasks' => $totalTasks,
            'completed' => $completedTasks,
            'late' => $lateTasks,
            'upcoming' => $upcomingTasks,
        ]);
    }
}
