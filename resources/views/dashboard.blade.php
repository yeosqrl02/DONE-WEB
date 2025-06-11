@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')
<div class="row g-4">
    <!-- Sidebar Kiri: Daftar Tugas -->
    <div class="col-lg-3 col-md-4">
        <div class="bg-white p-4 rounded shadow-sm d-flex flex-column" style="max-height: 90vh;">
            <h5 class="fw-bold border-bottom pb-3 mb-3 text-center">DEADLINE TIME!</h5>
            <div id="taskSidebarList" class="flex-grow-1 overflow-auto">
                <p class="text-center text-muted">Memuat tugas...</p>
            </div>
        </div>
    </div>

    <!-- Area Dashboard -->
    <div class="col-lg-9 col-md-8">
        <h3 class="mb-3">Halo!, <strong>{{ Auth::user()->name }}</strong></h3>

        <!-- Statistik -->
        <div class="row text-center g-3 mb-5">
            @php
                $defaultStats = ['Tugas' => 0, 'Selesai' => 0, 'Terlambat' => 0, 'Mendatang' => 0];
                $statsData = is_array($stats) ? array_merge($defaultStats, $stats) : $defaultStats;

                $statsArr = [
                    ['label' => 'Tugas', 'value' => $statsData['Tugas'], 'class' => 'bg-primary'],
                    ['label' => 'Selesai', 'value' => $statsData['Selesai'], 'class' => 'bg-success'],
                    ['label' => 'Terlambat', 'value' => $statsData['Terlambat'], 'class' => 'bg-danger'],
                    ['label' => 'Mendatang', 'value' => $statsData['Mendatang'], 'class' => 'bg-warning text-dark'],
                ];
            @endphp
            @foreach($statsArr as $stat)
            <div class="col-6 col-md-3">
                <div class="p-4 rounded shadow-sm {{ $stat['class'] }}">
                    <div class="fs-2 fw-bold count-up" data-target="{{ $stat['value'] }}">{{ $stat['value'] }}</div>
                    <div class="fs-6 mt-1">{{ $stat['label'] }}</div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Grafik -->
        <div class="row g-4">
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white fw-bold">Aktivitas Mingguan</div>
                    <div class="card-body">
                        <canvas id="activityChart"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-header bg-success text-white fw-bold">Penyelesaian Tugas</div>
                    <div class="card-body">
                        <canvas id="taskChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    #taskSidebarList::-webkit-scrollbar {
        width: 8px;
    }
    #taskSidebarList::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }
    #taskSidebarList::-webkit-scrollbar-thumb {
        background: #007bff;
        border-radius: 10px;
    }
    #taskSidebarList::-webkit-scrollbar-thumb:hover {
        background: #0056b3;
    }

    .bg-white.d-flex.flex-column {
        max-height: 90vh;
    }

    .task-card {
        background-color: #f9f9f9;
        border-radius: 8px;
        padding: 12px 16px;
        box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        display: flex;
        align-items: center;
        gap: 12px;
        cursor: pointer;
        transition: background-color 0.3s ease, box-shadow 0.3s ease;
        margin-bottom: 10px;

        white-space: normal;
        word-break: break-word;
        flex-wrap: wrap;
        min-height: 50px;
    }
    .task-card:hover, .task-card:focus {
        background-color: #e9f0ff;
        box-shadow: 0 4px 12px rgba(0,123,255,0.3);
        outline: none;
    }
    .task-checkbox {
        width: 22px;
        height: 22px;
        cursor: pointer;
        flex-shrink: 0;
    }
    .task-label {
        flex-grow: 1;
        user-select: none;
        font-weight: 600;
        font-size: 1rem;
        white-space: normal;
    }
    .task-date {
        color: #6c757d;
        font-size: 0.85rem;
        white-space: nowrap;
        flex-shrink: 0;
        margin-left: auto;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    async function fetchTasks() {
        try {
            const response = await fetch('{{ route('api.tasks.recent') }}');
            if (!response.ok) throw new Error('Gagal mengambil data tugas');
            const tasks = await response.json();

            let html = '';
            tasks.forEach(task => {
                if (!task.completed) {
                    const date = new Date(task.tanggal);
                    const formattedDate = date.toLocaleString('id-ID', {
                        day: '2-digit', month: 'short', year: 'numeric',
                        hour: '2-digit', minute: '2-digit'
                    });
                    const slug = task.nama.toLowerCase().replace(/\s+/g, '-').replace(/[^\w\-]+/g, '');
                    html += `
                        <div class="task-card" tabindex="0">
                            <input class="form-check-input task-checkbox" type="checkbox" id="task-${slug}" data-id="${task.id}">
                            <label class="task-label ms-2" for="task-${slug}">${task.nama}</label>
                            <span class="task-date">${formattedDate}</span>
                        </div>
                    `;
                }
            });

            if (html === '') {
                html = '<p class="text-muted text-center mt-4">Tidak ada tugas.</p>';
            }

            document.getElementById('taskSidebarList').innerHTML = html;
        } catch (err) {
            console.error(err);
            document.getElementById('taskSidebarList').innerHTML = '<p class="text-danger text-center mt-4">Gagal memuat tugas.</p>';
        }
    }

    async function loadTaskHistory() {
        try {
            const response = await fetch('{{ route("api.tasks.history") }}');
            if (!response.ok) throw new Error('Gagal memuat riwayat tugas selesai.');

            const tasks = await response.json();
            const historyContainer = document.getElementById('taskHistoryContainer');
            if (!historyContainer) return;

            let html = '';
            if (tasks.length === 0) {
                html = '<p class="text-muted text-center">Belum ada tugas selesai.</p>';
            } else {
                html = '<ul class="list-group">';
                tasks.forEach(task => {
                    html += `
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <strong>${task.nama}</strong><br>
                                <small>${task.course_name}</small><br>
                                <small>${task.deskripsi || '-'}</small>
                            </div>
                            <span class="badge bg-success rounded-pill">Selesai pada ${task.completed_at}</span>
                        </li>
                    `;
                });
                html += '</ul>';
            }
            historyContainer.innerHTML = html;
        } catch (error) {
            console.error(error);
        }
    }

    document.getElementById('taskSidebarList').addEventListener('change', function(e) {
        if (e.target.classList.contains('task-checkbox')) {
            const checkbox = e.target;
            const taskId = checkbox.getAttribute('data-id');
            if (!taskId) return;

            fetch(`/tasks/${taskId}/toggle-complete`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ completed: checkbox.checked })
            })
            .then(res => {
                if (!res.ok) throw new Error('Gagal memperbarui status tugas.');
                return res.json();
            })
            .then(data => {
                fetchTasks();
                reloadTaskListOnPage();
                fetchStatsAndCharts();
                loadTaskHistory();
            })
            .catch(err => {
                alert(err.message || 'Gagal memperbarui status tugas.');
                checkbox.checked = !checkbox.checked;
            });
        }
    });

    function reloadTaskListOnPage() {
        if (typeof loadTasks === 'function') {
            loadTasks();
        }
    }

    async function fetchStatsAndCharts() {
        try {
            const response = await fetch('{{ route("api.tasks.statistics") }}');
            if (!response.ok) throw new Error('Gagal mengambil statistik tugas');
            const stats = await response.json();

            document.querySelectorAll('.count-up').forEach(el => {
                const label = el.parentElement.querySelector('div.fs-6').textContent.toLowerCase();
                if (label === 'tugas' || label === 'tasks') el.textContent = stats.tasks;
                else if (label === 'selesai' || label === 'completed') el.textContent = stats.completed;
                else if (label === 'terlambat' || label === 'late') el.textContent = stats.late;
                else if (label === 'mendatang' || label === 'upcoming') el.textContent = stats.upcoming;
            });

            if (typeof taskChart !== 'undefined') {
                taskChart.data.datasets[0].data = [stats.completed, stats.late, stats.upcoming];
                taskChart.update();
            }
        } catch (err) {
            console.error(err);
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        fetchTasks();
        fetchStatsAndCharts();
        loadTaskHistory();
    });

    function countUp(el) {
        const target = +el.getAttribute('data-target');
        let count = 0;
        const increment = target > 0 ? Math.ceil(target / 100) : 0;
        if (increment === 0) {
            el.textContent = target;
            return;
        }
        const update = () => {
            count += increment;
            if (count >= target) {
                el.textContent = target;
            } else {
                el.textContent = count;
                requestAnimationFrame(update);
            }
        };
        update();
    }
    document.querySelectorAll('.count-up').forEach(countUp);

    const ctxActivity = document.getElementById('activityChart').getContext('2d');
    const activityChart = new Chart(ctxActivity, {
        type: 'line',
        data: {
            labels: @json($weeklyLabels),
            datasets: [{
                label: 'Activity',
                data: @json($weeklyData),
                borderColor: '#007bff',
                backgroundColor: 'rgba(0,123,255,0.2)',
                fill: true,
                tension: 0.3,
                pointRadius: 5,
                pointHoverRadius: 7
            }]
        },
        options: {
            responsive: true,
            plugins: {
                tooltip: { mode: 'index', intersect: false },
                legend: { display: true, labels: { color: '#333' } }
            },
            scales: {
                y: { beginAtZero: true, ticks: { color: '#555' } },
                x: { ticks: { color: '#555' } }
            }
        }
    });

    const ctxTask = document.getElementById('taskChart').getContext('2d');
    const taskChart = new Chart(ctxTask, {
        type: 'bar',
        data: {
            labels: @json($taskCompletionLabels),
            datasets: [{
                label: 'Tasks',
                data: @json($taskCompletionData),
                backgroundColor: '#28a745',
                borderRadius: 5
            }]
        },
        options: {
            responsive: true,
            plugins: {
                tooltip: { enabled: true },
                legend: { display: false }
            },
            scales: {
                y: { beginAtZero: true, ticks: { color: '#555' } },
                x: { ticks: { color: '#555' } }
            }
        }
    });
</script>
@endpush
