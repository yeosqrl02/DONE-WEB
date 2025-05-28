<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::now();

        // Hitung total tugas
        $totalTasks = Task::count();

        // Hitung tugas yang sudah selesai
        $completedTasks = Task::where('completed', true)->count();

        // Hitung tugas terlambat (belum selesai & deadline sudah lewat)
        $lateTasks = Task::where('completed', false)
                         ->where('tanggal', '<', $today)
                         ->count();

        // Hitung tugas yang upcoming (belum selesai & deadline di masa depan)
        $upcomingTasks = Task::where('completed', false)
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
