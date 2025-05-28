@php
    $no = 1;
@endphp

@foreach ($tasks as $task)
    @if (!$task->completed)
    <tr data-id="{{ $task->id }}">
        <td>{{ $no++ }}</td>
        <td class="td-nama">{{ $task->nama }}</td>
        <td class="td-deskripsi">{{ $task->deskripsi ?? '-' }}</td>
        <td class="td-tanggal">{{ \Carbon\Carbon::parse($task->tanggal)->translatedFormat('d F Y H:i') }}</td>
        <td class="td-course">{{ $task->course->name ?? '-' }}</td>
        <td>
            <button class="btn btn-sm btn-warning btn-edit" data-bs-toggle="modal" data-bs-target="#editModal{{ $task->id }}">
                Edit
            </button>
            <form class="d-inline formHapusTugas" action="{{ route('tasks.destroy', $task->id) }}" method="POST">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
            </form>
        </td>
    </tr>

    {{-- Modal Edit --}}
    <div class="modal fade" id="editModal{{ $task->id }}" tabindex="-1" aria-labelledby="editModalLabel{{ $task->id }}" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form class="formEditTugas" data-id="{{ $task->id }}" action="{{ route('tasks.update', $task->id) }}" method="POST" novalidate>
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title" id="editModalLabel{{ $task->id }}">Edit Tugas</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="nama-{{ $task->id }}" class="form-label">Nama Tugas</label>
                            <input type="text" name="nama" id="nama-{{ $task->id }}" class="form-control" value="{{ $task->nama }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="deskripsi-{{ $task->id }}" class="form-label">Deskripsi</label>
                            <textarea name="deskripsi" id="deskripsi-{{ $task->id }}" class="form-control" rows="3">{{ $task->deskripsi }}</textarea>
                        </div>
                        <div class="mb-3">
                            <label for="tanggal-{{ $task->id }}" class="form-label">Tanggal & Waktu</label>
                            <input type="datetime-local" name="tanggal" id="tanggal-{{ $task->id }}" class="form-control" value="{{ \Carbon\Carbon::parse($task->tanggal)->format('Y-m-d\TH:i') }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="course_id-{{ $task->id }}" class="form-label">Mata Kuliah</label>
                            <select name="course_id" id="course_id-{{ $task->id }}" class="form-select" required>
                                @foreach ($courses as $course)
                                    <option value="{{ $course->id }}" {{ $task->course_id == $course->id ? 'selected' : '' }}>
                                        {{ $course->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    {{-- End Modal Edit --}}
    @endif
@endforeach
