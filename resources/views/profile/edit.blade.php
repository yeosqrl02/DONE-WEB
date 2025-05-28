@extends('layouts.app')

@section('content')
<style>
    .overlay-bg {
        background-color: rgba(0,0,0,0.4);
        position: fixed;
        top: 0; left: 0; right: 0; bottom: 0;
        z-index: 1040;
    }
    .modal-center {
        position: fixed;
        top: 50%;
        left: 50%;
        width: 700px;
        transform: translate(-50%, -50%);
        z-index: 1050;
    }
</style>

<div class="overlay-bg"></div>

<div class="modal-content modal-center shadow rounded">
    <div class="modal-header border-bottom">
        <h5 class="modal-title">Edit Profil</h5>
        <a href="{{ route('profile') }}" class="btn-close"></a>
    </div>

    <form action="{{ route('profile.update') }}" method="POST">
        @csrf
        <div class="modal-body p-4">
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="name" class="form-label">Nama Lengkap</label>
                    <input type="text" id="name" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                </div>
                <div class="col-md-6">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" id="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="phone" class="form-label">Telepon</label>
                    <input type="text" id="phone" name="phone" class="form-control" value="{{ old('phone', $user->phone) }}">
                </div>
                <div class="col-md-6">
                    <label for="mobile" class="form-label">HP</label>
                    <input type="text" id="mobile" name="mobile" class="form-control" value="{{ old('mobile', $user->mobile) }}">
                </div>
            </div>

            <div class="mb-3">
                <label for="address" class="form-label">Alamat</label>
                <textarea id="address" name="address" class="form-control">{{ old('address', $user->address) }}</textarea>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="job_title" class="form-label">Pekerjaan / Jabatan</label>
                    <input type="text" id="job_title" name="job_title" class="form-control" value="{{ old('job_title', $user->job_title) }}">
                </div>
                <div class="col-md-6">
                    <label for="location" class="form-label">Lokasi</label>
                    <input type="text" id="location" name="location" class="form-control" value="{{ old('location', $user->location) }}">
                </div>
            </div>
        </div>

        <div class="modal-footer px-4 pb-4">
            <button type="submit" class="btn btn-success">Simpan Perubahan</button>
            <a href="{{ route('profile') }}" class="btn btn-secondary">Batal</a>
        </div>
    </form>
</div>
@endsection
