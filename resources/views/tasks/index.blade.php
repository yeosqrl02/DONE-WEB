@extends('layouts.app')
@section('title', 'Daftar Tugas')

@push('styles')
<style>
    /* Tambahan styling untuk form dan tabel supaya lebih modern */
    .card-header {
        font-weight: 700;
        font-size: 1.25rem;
        letter-spacing: 0.05em;
    }

    /* Custom button style */
    button.btn-primary, button.btn-secondary {
        border-radius: 30px;
        padding: 8px 24px;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    button.btn-primary:hover, button.btn-secondary:hover {
        filter: brightness(0.9);
    }

    /* Input dan textarea rounded */
    input.form-control, select.form-select, textarea.form-control {
        border-radius: 12px;
        box-shadow: none;
        transition: box-shadow 0.3s ease;
    }
    input.form-control:focus, select.form-select:focus, textarea.form-control:focus {
        box-shadow: 0 0 8px rgba(0, 123, 255, 0.5);
        border-color: #007bff;
    }

    /* Table styling */
    table.table {
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    table.table thead {
        background: linear-gradient(90deg, #007bff, #0056b3);
        color: white;
        font-weight: 600;
        letter-spacing: 0.05em;
    }
    table.table tbody tr:hover {
        background-color: #f1f9ff;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }
    table.table td, table.table th {
        vertical-align: middle !important;
        padding: 14px 18px;
    }

    /* Card shadow & spacing */
    .card {
        border-radius: 16px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.07);
        transition: box-shadow 0.3s ease;
    }
    .card:hover {
        box-shadow: 0 14px 40px rgba(0,0,0,0.12);
    }

    /* Scroll for description if long text */
    td .text-truncate {
        max-width: 240px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        display: inline-block;
        vertical-align: middle;
    }

    /* Responsive improvements */
    @media (max-width: 767px) {
        table.table {
            font-size: 0.85rem;
        }
        input.form-control, select.form-select, textarea.form-control {
            font-size: 0.9rem;
        }
        button.btn-primary, button.btn-secondary {
            font-size: 0.9rem;
            padding: 6px 20px;
        }
    }
</style>
@endpush

@section('content')
<div class="container py-5">

    {{-- Form Tambah Mata Kuliah --}}
    <div class="card shadow-sm mb-5">
        <div class="card-header bg-secondary text-white rounded-top">Tambah Mata Kuliah</div>
        <div class="card-body">
            <form action="{{ route('courses.store') }}" method="POST" class="row g-3 align-items-center">
                @csrf
                <div class="col-md-8">
                    <input type="text" name="name" class="form-control" placeholder="Nama Mata Kuliah" required>
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-secondary">Tambah</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Form Tambah Tugas --}}
    <div class="card shadow-sm mb-5">
        <div class="card-header bg-primary text-white rounded-top">Tambah Tugas</div>
        <div class="card-body">
            <form id="formTambahTugas" action="{{ route('tasks.store') }}" method="POST" novalidate>
                @csrf
                <div class="mb-3">
                    <label for="nama" class="form-label fw-semibold">Nama Tugas</label>
                    <input type="text" name="nama" id="nama" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="deskripsi" class="form-label fw-semibold">Deskripsi</label>
                    <textarea name="deskripsi" id="deskripsi" class="form-control" rows="3" placeholder="Deskripsi tugas (opsional)"></textarea>
                </div>
                <div class="mb-3">
                    <label for="tanggal" class="form-label fw-semibold">Tanggal & Waktu</label>
                    <input type="datetime-local" name="tanggal" id="tanggal" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="course_id" class="form-label fw-semibold">Mata Kuliah</label>
                    <select name="course_id" id="course_id" class="form-select" required>
                        <option value="">Pilih Mata Kuliah</option>
                        @forelse ($courses as $course)
                            <option value="{{ $course->id }}">{{ $course->name }}</option>
                        @empty
                            <option disabled>Belum ada mata kuliah</option>
                        @endforelse
                    </select>
                </div>
                <button class="btn btn-primary rounded-pill px-4" type="submit">Tambah</button>
            </form>
        </div>
    </div>

    {{-- Daftar Tugas --}}
    <div class="card shadow-sm mb-5">
        <div class="card-header bg-info text-white rounded-top">Daftar Tugas</div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered align-middle mb-0" id="tabelTugas">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 40px;">No</th>
                            <th>Nama Tugas</th>
                            <th>Deskripsi</th>
                            <th style="width: 160px;">Tanggal</th>
                            <th>Mata Kuliah</th>
                            <th style="width: 130px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="taskTableBody" class="fw-normal">
                        @include('tasks.partials.task_tbody', ['tasks' => $tasks, 'courses' => $courses])
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- History Tugas Selesai --}}
    <div class="card shadow-sm">
        <div class="card-header bg-success text-white rounded-top">History Tugas Selesai</div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered align-middle mb-0" id="tabelHistoryTugas">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 40px;">No</th>
                            <th>Nama Tugas</th>
                            <th>Deskripsi</th>
                            <th style="width: 160px;">Tanggal</th>
                            <th>Mata Kuliah</th>
                            <th style="width: 160px;">Selesai Pada</th>
                        </tr>
                    </thead>
                    <tbody id="taskHistoryBody" class="fw-normal">
                        <tr><td colspan="6" class="text-center text-muted py-4">Memuat data history...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Fungsi load ulang daftar tugas via partial blade
    async function loadTasks() {
        try {
            const response = await fetch('{{ route('tasks.partial.tbody') }}');
            if (!response.ok) throw new Error('Gagal memuat data tugas.');
            const html = await response.text();
            document.querySelector('#taskTableBody').innerHTML = html;
            attachDeleteAndEditListeners();
            loadTaskHistory();  // Load ulang history saat reload daftar tugas
        } catch (error) {
            console.error(error);
            Swal.fire('Error', 'Gagal memuat data tugas.', 'error');
        }
    }

    // Pasang event listener hapus dan edit
    function attachDeleteAndEditListeners() {
        // Hapus tugas
        document.querySelectorAll('.formHapusTugas').forEach(form => {
            form.removeEventListener('submit', handleDeleteTask);
            form.addEventListener('submit', handleDeleteTask);
        });

        // Edit tugas via modal
        document.querySelectorAll('.formEditTugas').forEach(form => {
            form.removeEventListener('submit', handleEditTask);
            form.addEventListener('submit', handleEditTask);
        });
    }

    // Handler hapus tugas
    function handleDeleteTask(e) {
        e.preventDefault();
        const formEl = this;

        Swal.fire({
            title: 'Yakin ingin menghapus tugas ini?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(formEl.action, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                        'Accept': 'application/json'
                    }
                })
                .then(res => {
                    if (!res.ok) throw new Error('Gagal menghapus tugas.');
                    return res.json();
                })
                .then(data => {
                    Swal.fire({
                        icon: 'success',
                        title: 'Terhapus!',
                        text: data.message || 'Tugas berhasil dihapus.',
                        timer: 1500,
                        showConfirmButton: false
                    });
                    loadTasks();
                })
                .catch(err => {
                    Swal.fire('Error', err.message || 'Gagal menghapus tugas.', 'error');
                });
            }
        });
    }

    // Handler edit tugas
    function handleEditTask(e) {
        e.preventDefault();
        const formEl = this;
        const data = new FormData(formEl);

        fetch(formEl.action, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                'Accept': 'application/json',
                'X-HTTP-Method-Override': 'PUT'
            },
            body: data
        })
        .then(response => {
            if (!response.ok) throw new Error('Terjadi kesalahan saat memperbarui tugas.');
            return response.json();
        })
        .then(data => {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: data.message || 'Tugas berhasil diperbarui.',
                timer: 2000,
                showConfirmButton: false,
                timerProgressBar: true,
            });

            // Tutup modal edit
            const modal = bootstrap.Modal.getInstance(formEl.closest('.modal'));
            modal.hide();

            loadTasks();
        })
        .catch(error => {
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: error.message || 'Gagal memperbarui tugas.',
            });
        });
    }

    // AJAX submit tambah tugas
    document.getElementById('formTambahTugas').addEventListener('submit', function(e) {
        e.preventDefault();

        const form = this;
        const data = new FormData(form);

        fetch(form.action, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                'Accept': 'application/json',
            },
            body: data
        })
        .then(response => {
            if (!response.ok) throw new Error('Terjadi kesalahan saat menambah tugas.');
            return response.json();
        })
        .then(data => {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: data.message || 'Tugas berhasil ditambahkan.',
                timer: 2000,
                showConfirmButton: false,
                timerProgressBar: true,
            });
            form.reset();
            loadTasks();
        })
        .catch(error => {
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: error.message || 'Gagal menambah tugas.',
            });
        });
    });

    // Load history tugas selesai
    async function loadTaskHistory() {
        try {
            const response = await fetch('{{ route("api.tasks.history") }}');
            if (!response.ok) throw new Error('Gagal memuat data history tugas.');
            const tasks = await response.json();

            let html = '';
            if (tasks.length === 0) {
                html = '<tr><td colspan="6" class="text-center text-muted">Belum ada tugas selesai.</td></tr>';
            } else {
                let no = 1;
                tasks.forEach(task => {
                    html += `
                        <tr>
                            <td>${no++}</td>
                            <td>${task.nama}</td>
                            <td class="text-truncate" title="${task.deskripsi || '-'}">${task.deskripsi || '-'}</td>
                            <td>${new Date(task.tanggal).toLocaleString('id-ID')}</td>
                            <td>${task.course_name || '-'}</td>
                            <td>${task.completed_at}</td>
                        </tr>
                    `;
                });
            }

            document.querySelector('#taskHistoryBody').innerHTML = html;
        } catch (error) {
            console.error(error);
            document.querySelector('#taskHistoryBody').innerHTML = '<tr><td colspan="6" class="text-danger text-center">Gagal memuat data history tugas.</td></tr>';
        }
    }

    // Pasang event listener saat halaman load
    attachDeleteAndEditListeners();
    document.addEventListener('DOMContentLoaded', () => {
        loadTasks();
        loadTaskHistory();
    });
</script>
@endpush
