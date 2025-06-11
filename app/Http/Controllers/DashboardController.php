<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::now();

        // Ambil user yang sedang login
        $userId = Auth::id();

        // Hitung total tugas milik user
        $totalTasks = Task::where('user_id', $userId)->count();

        // Hitung tugas yang sudah selesai milik user
        $completedTasks = Task::where('user_id', $userId)
                              ->where('completed', true)
                              ->count();

        // Hitung tugas terlambat (belum selesai & deadline sudah lewat) milik user
        $lateTasks = Task::where('user_id', $userId)
                         ->where('completed', false)
                         ->where('tanggal', '<', $today)
                         ->count();

        // Hitung tugas yang upcoming (belum selesai & deadline di masa depan) milik user
        $upcomingTasks = Task::where('user_id', $userId)
                             ->where('completed', false)
                             ->where('tanggal', '>=', $today)
                             ->count();

        // Contoh data grafik mingguan (ganti sesuai kebutuhan/data riil)
        $weeklyLabels = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
        $weeklyData = [5, 7, 4, 8, 6, 10, 9];

        // Data untuk grafik penyelesaian tugas
        $taskCompletionLabels = ['Completed', 'Late', 'Upcoming'];
        $taskCompletionData = [$completedTasks, $lateTasks, $upcomingTasks];

        // Data statistik untuk blade
        $stats = [
            'tasks' => $totalTasks,
            'completed' => $completedTasks,
            'late' => $lateTasks,
            'upcoming' => $upcomingTasks,
        ];

        return view('dashboard', compact(
            'stats',
            'weeklyLabels',
            'weeklyData',
            'taskCompletionLabels',
            'taskCompletionData'
        ));
    }
}
