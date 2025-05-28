@extends('layouts.app')

@section('title', 'Kalender')

@push('styles')
<style>
    /* Reset dan font */
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background-color: #f0f4f8;
        color: #333;
        margin: 0;
        padding: 0;
    }

    h1 {
        text-align: center;
        margin-bottom: 20px;
        font-weight: 700;
        font-size: 2.4rem;
        color: #222;
        letter-spacing: 1.2px;
        text-transform: uppercase;
        text-shadow: 0 2px 6px rgba(0,0,0,0.05);
    }

    /* Container pusat */
    .navigation, .selector {
        text-align: center;
        margin-bottom: 20px;
    }

    /* Link navigasi */
    .navigation a {
        margin: 0 20px;
        text-decoration: none;
        font-weight: 700;
        font-size: 1.3rem;
        color: #0d6efd;
        padding: 10px 18px;
        border: 2px solid #0d6efd;
        border-radius: 40px;
        transition: background-color 0.3s, color 0.3s;
        display: inline-block;
        box-shadow: 0 2px 8px rgba(13,110,253,0.2);
        user-select: none;
    }
    .navigation a:hover {
        background-color: #0d6efd;
        color: #fff;
        box-shadow: 0 4px 15px rgba(13,110,253,0.4);
    }

    /* Form selector */
    .selector form {
        display: inline-flex;
        align-items: center;
        gap: 12px;
    }
    .selector label {
        font-weight: 600;
        font-size: 1rem;
        color: #444;
        user-select: none;
    }
    .selector select, .selector button {
        padding: 8px 14px;
        font-size: 1rem;
        border: 1.8px solid #ccc;
        border-radius: 10px;
        transition: border-color 0.3s ease;
        cursor: pointer;
    }
    .selector select:hover, .selector select:focus,
    .selector button:hover, .selector button:focus {
        border-color: #0d6efd;
        outline: none;
        box-shadow: 0 0 8px rgba(13,110,253,0.5);
    }
    .selector button {
        background-color: #0d6efd;
        color: white;
        border: none;
        font-weight: 700;
        letter-spacing: 0.8px;
        user-select: none;
    }

    /* Table kalender */
    table {
        margin: 0 auto 40px auto;
        width: 90%;
        max-width: 900px;
        border-collapse: separate;
        border-spacing: 0 12px;
        text-align: center;
        background-color: transparent;
        font-size: 1rem;
        box-shadow: 0 12px 24px rgba(0,0,0,0.05);
        border-radius: 20px;
        overflow: hidden;
        user-select: none;
    }

    /* Header hari */
    th {
        background: linear-gradient(135deg, #0d6efd, #6ea8fe);
        color: white;
        font-weight: 700;
        padding: 20px 12px;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        font-size: 0.95rem;
        border: none;
        box-shadow: inset 0 -3px 6px rgba(255,255,255,0.2);
    }

    /* Sel kalender */
    td {
        background-color: #fff;
        border-radius: 14px;
        padding: 16px 12px;
        vertical-align: top;
        height: 100px;
        position: relative;
        transition: background-color 0.3s ease, transform 0.15s ease;
        box-shadow: 0 2px 6px rgba(0,0,0,0.04);
        cursor: default;
        user-select: text;
    }
    td:hover:not(.empty) {
        background-color: #e9f0ff;
        transform: translateY(-3px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.1);
        z-index: 5;
        position: relative;
    }

    /* Weekend style */
    .sunday {
        background-color: #ffeaea !important;
        color: #c43030;
        font-weight: 800;
        font-size: 1.05rem;
        user-select: none;
        box-shadow: none !important;
    }
    .saturday {
        background-color: #d9e9ff !important;
        color: #1565c0;
        font-weight: 800;
        font-size: 1.05rem;
        user-select: none;
        box-shadow: none !important;
    }

    /* Empty cells */
    .empty {
        background-color: #f9fafb !important;
        box-shadow: none !important;
        cursor: default;
        user-select: none;
    }

    /* Tanggal tebal dan di atas */
    td strong {
        position: absolute;
        top: 8px;
        right: 12px;
        font-weight: 900;
        font-size: 1.1rem;
        color: #444;
        user-select: none;
    }

    /* Daftar tugas di tanggal */
    .task-list {
        margin-top: 30px;
        padding-left: 8px;
        text-align: left;
        font-size: 0.8rem;
        max-height: 60px;
        overflow-y: auto;
        color: #223;
        user-select: text;
    }

    .task-list li {
        list-style-type: disc;
        margin-left: 18px;
        white-space: nowrap;
        text-overflow: ellipsis;
        overflow: hidden;
        cursor: default;
        padding: 2px 0;
        transition: color 0.3s ease;
        border-radius: 6px;
    }
    .task-list li:hover {
        color: #0d6efd;
        font-weight: 600;
        text-shadow: 0 0 6px rgba(13,110,253,0.6);
        background-color: #d9e9ff;
        padding-left: 6px;
        cursor: pointer;
    }

    /* Scrollbar for task list */
    .task-list::-webkit-scrollbar {
        width: 5px;
    }
    .task-list::-webkit-scrollbar-track {
        background: transparent;
    }
    .task-list::-webkit-scrollbar-thumb {
        background: #0d6efd;
        border-radius: 10px;
    }
    .task-list::-webkit-scrollbar-thumb:hover {
        background: #0849b1;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        table {
            width: 100%;
            font-size: 0.85rem;
        }
        td, th {
            padding: 12px 8px;
            height: 80px;
        }
        td strong {
            font-size: 1rem;
            top: 6px;
            right: 8px;
        }
        .task-list {
            margin-top: 20px;
            font-size: 0.7rem;
        }
        .navigation a {
            font-size: 1rem;
            margin: 0 8px;
            padding: 8px 14px;
        }
        .selector select, .selector button {
            padding: 6px 10px;
            font-size: 0.85rem;
        }
    }
</style>
@endpush

@section('content')
    <h1>Kalender {{ \Carbon\Carbon::createFromDate($year, $month)->translatedFormat('F Y') }}</h1>

    <!-- Navigasi Panah -->
    <div class="navigation">
        <a href="{{ route('calendar', ['year' => $prevYear, 'month' => $prevMonth]) }}">&#8592; Sebelumnya</a>
        <a href="{{ route('calendar', ['year' => $nextYear, 'month' => $nextMonth]) }}">Berikutnya &#8594;</a>
    </div>

    <!-- Dropdown Pilih Bulan dan Tahun -->
    <div class="selector">
        <form method="GET" action="{{ route('calendar') }}">
            <label for="month">Bulan:</label>
            <select name="month" id="month">
                @for ($m = 1; $m <= 12; $m++)
                    <option value="{{ $m }}" {{ $m == $month ? 'selected' : '' }}>
                        {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                    </option>
                @endfor
            </select>

            <label for="year">Tahun:</label>
            <select name="year" id="year">
                @for ($y = $year - 5; $y <= $year + 5; $y++)
                    <option value="{{ $y }}" {{ $y == $year ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
            </select>

            <button type="submit" class="btn btn-sm btn-primary">Tampilkan</button>
        </form>
    </div>

    <!-- Tabel Kalender -->
    <table>
        <tr>
            <th>Minggu</th>
            <th>Senin</th>
            <th>Selasa</th>
            <th>Rabu</th>
            <th>Kamis</th>
            <th>Jumat</th>
            <th>Sabtu</th>
        </tr>
        @php $day = 1; @endphp
        @for ($i = 0; $i < ceil(($daysInMonth + $startDay) / 7); $i++)
            <tr>
                @for ($j = 0; $j < 7; $j++)
                    @if ($i === 0 && $j < $startDay)
                        <td class="empty"></td>
                    @elseif ($day > $daysInMonth)
                        <td class="empty"></td>
                    @else
                        @php
                            $hasTasks = isset($tasksByDate[$day]);
                        @endphp
                        <td class="{{ $j == 0 ? 'sunday' : ($j == 6 ? 'saturday' : '') }}"
                            style="{{ $hasTasks ? 'background-color: #d1e7dd;' : '' }}">
                            <strong>{{ $day }}</strong>

                            @if ($hasTasks)
                                <ul class="task-list mb-0">
                                    @foreach ($tasksByDate[$day] as $task)
                                        <li title="{{ $task->nama }}">{{ \Illuminate\Support\Str::limit($task->nama, 25) }}</li>
                                    @endforeach
                                </ul>
                            @endif
                        </td>
                        @php $day++; @endphp
                    @endif
                @endfor
            </tr>
        @endfor
    </table>
@endsection
