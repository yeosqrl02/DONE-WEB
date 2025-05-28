<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Task;

class CalendarController extends Controller
{
    public function index(Request $request, $year = null, $month = null)
    {
        // Ambil tahun dan bulan dari query param atau default sekarang
        $year = $request->input('year', $year ?? now()->year);
        $month = $request->input('month', $month ?? now()->month);

        $month = max(1, min(12, (int)$month));
        $year = (int)$year;

        // Hitung jumlah hari dalam bulan
        $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        // Hari pertama dalam minggu (0=minggu, 1=senin, dst)
        $startDay = date('w', strtotime("$year-$month-01"));

        // Navigasi bulan sebelumnya
        $prevMonth = $month - 1;
        $prevYear = $year;
        if ($prevMonth < 1) {
            $prevMonth = 12;
            $prevYear--;
        }

        // Navigasi bulan berikutnya
        $nextMonth = $month + 1;
        $nextYear = $year;
        if ($nextMonth > 12) {
            $nextMonth = 1;
            $nextYear++;
        }

        // Ambil tugas yang belum selesai (completed = false) di bulan dan tahun tersebut
        $tasks = Task::whereYear('tanggal', $year)
                     ->whereMonth('tanggal', $month)
                     ->where('completed', false) // FILTER tugas belum selesai
                     ->get();

        // Kelompokkan tugas berdasarkan tanggal (hari)
        $tasksByDate = [];
        foreach ($tasks as $task) {
            $date = Carbon::parse($task->tanggal)->day;
            $tasksByDate[$date][] = $task;
        }

        // Kirim data ke view kalender
        return view('calendar.index', compact(
            'year', 'month', 'daysInMonth', 'startDay',
            'prevMonth', 'prevYear', 'nextMonth', 'nextYear',
            'tasksByDate'
        ));
    }
}
