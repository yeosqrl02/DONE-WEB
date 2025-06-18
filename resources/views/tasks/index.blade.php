@extends('layouts.app')
@section('title', 'Daftar Tugas')

@push('styles')
<style>
    /* ... (styles tidak berubah) ... */
</style>
@endpush

@section('content')
<div class="container py-5">

    {{-- Form Tambah Mata Kuliah --}}
    <div class="card shadow-sm mb-4">
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

    {{-- Daftar Mata Kuliah --}}
    @if($courses->count())
    <div class="card shadow-sm mb-5">
        <div class="card-header bg-dark text-white rounded-top">Mata Kuliah Saya</div>
        <div class="card-body">
            <table class="table table-bordered align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 40px;">No</th>
                        <th>Nama Mata Kuliah</th>
                        <th style="width: 160px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($courses as $index => $course)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $course->name }}</td>
                        <td>
                            {{-- Tombol Edit --}}
                            <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editCourseModal{{ $course->id }}">
                                Edit
                            </button>

                            {{-- Modal Edit --}}
                            <div class="modal fade" id="editCourseModal{{ $course->id }}" tabindex="-1" aria-labelledby="editCourseModalLabel{{ $course->id }}" aria-hidden="true">
                              <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                  <form action="{{ route('courses.update', $course->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-header">
                                      <h5 class="modal-title" id="editCourseModalLabel{{ $course->id }}">Edit Mata Kuliah</h5>
                                      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                      <input type="text" name="name" class="form-control" value="{{ $course->name }}" required>
                                    </div>
                                    <div class="modal-footer">
                                      <button type="submit" class="btn btn-success">Simpan</button>
                                      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                    </div>
                                  </form>
                                </div>
                              </div>
                            </div>

                            {{-- Tombol Hapus --}}
                            <form action="{{ route('courses.destroy', $course->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus mata kuliah ini?')">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-danger">Hapus</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

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
                        @forelse($taskHistory as $index => $task)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $task->nama }}</td>
                                <td>{{ $task->deskripsi ?? '-' }}</td>
                                <td>{{ \Carbon\Carbon::parse($task->tanggal)->format('d M Y, H:i') }}</td>
                                <td>{{ $courses->find($task->course_id)?->name ?? '-' }}</td>
                                <td>{{ \Carbon\Carbon::parse($task->completed_at)->format('d M Y, H:i') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">Belum ada tugas yang diselesaikan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
{{-- Tidak berubah dari skrip sebelumnya (AJAX load tugas & SweetAlert) --}}
@endpush
