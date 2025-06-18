@extends('layouts.app')

@push('styles')
<style>
    .avatar-img {
        width: 150px;
        height: 150px;
        border-radius: 50%;
        object-fit: cover;
        border: 3px solid #007bff;
        box-shadow: 0 2px 8px rgba(0,0,0,0.15);
        margin-bottom: 1rem;
    }
</style>
@endpush

@section('content')
@php
    $avatarPath = 'storage/avatars/' . $user->avatar;
    $avatarExists = $user->avatar && file_exists(public_path($avatarPath));
    $avatarUrl = $avatarExists ? asset($avatarPath) : asset('images/default-avatar.png');
@endphp

<div class="container">
    <div class="row">
        <!-- Sidebar Kiri -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-center">
                    <img 
                      src="{{ $avatarUrl }}" 
                      alt="Avatar" 
                      class="avatar-img" 
                      id="currentAvatar"
                    >
                    <h4 class="mb-0">{{ $user->name }}</h4>
                    <p class="text-muted">
                        {{ $user->job_title ?? '-' }}<br>
                        {{ $user->location ?? '-' }}
                    </p>
                </div>
                <ul class="list-group list-group-flush mt-3">
                    @foreach ([
                        'Website' => $user->website,
                        'Github' => $user->github,
                        'Twitter' => $user->twitter,
                        'Instagram' => $user->instagram,
                        'Facebook' => $user->facebook,
                    ] as $label => $value)
                        <li class="list-group-item">
                            <strong>{{ $label }}:</strong> 
                            <span class="float-end">
                                {!! $value ? '<a href="'.$value.'" target="_blank">'.e($value).'</a>' : '-' !!}
                            </span>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>

        <!-- Konten Kanan -->
        <div class="col-md-8">
            <h1 class="mb-4">Profil Saya</h1>

            @if (session('status') === 'profile-updated')
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    Profil berhasil diperbarui.
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="card mb-3">
                <div class="card-body">
                    @foreach ([
                        'Full Name' => $user->name,
                        'Email' => $user->email,
                        'Phone' => $user->phone ?? '-',
                        'Mobile' => $user->mobile ?? '-',
                        'Address' => $user->address ?? '-',
                        'Job Title' => $user->job_title ?? '-',
                        'Location' => $user->location ?? '-',
                    ] as $label => $value)
                        <div class="row mb-2">
                            <div class="col-sm-3"><h6 class="mb-0">{{ $label }}</h6></div>
                            <div class="col-sm-9 text-secondary">{{ $value }}</div>
                        </div>
                        <hr class="my-1">
                    @endforeach

                    <div class="text-center mt-4">
                        <button class="btn btn-info" data-bs-toggle="modal" data-bs-target="#editProfileModal">Edit</button>
                    </div>
                </div>
            </div>

            {{-- Status Proyek --}}
            <div class="row justify-content-center">
                @if ($tasks->isEmpty())
                    <div class="col-md-12 mb-3">
                        <div class="card h-100 text-center p-4" style="max-width:600px; margin:auto;">
                            <h6 class="text-primary"> Status Penugasan </h6>
                            <p class="text-muted mb-0">YEAY! TIDAK ADA TUGAS</p>
                        </div>
                    </div>
                @else
                    <div class="col-12 mb-3" style="max-width:600px; margin:auto;">
                        <div class="card h-100 text-center d-flex flex-column justify-content-center" style="min-height:200px;">
                            <div class="card-body">
                                <h6 class="text-primary mb-4">Status Penugasan</h6>
                                <ul class="list-unstyled mb-0" style="text-align:center;">
                                    @foreach ($tasks as $task)
                                        <li>{{ $task->nama }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modal Edit Profil -->
<div class="modal fade" id="editProfileModal" tabindex="-1" aria-labelledby="editProfileModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PATCH')
        <div class="modal-header">
          <h5 class="modal-title" id="editProfileModalLabel">Edit Profil</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
        </div>
        <div class="modal-body">
          <!-- Upload Avatar -->
          <div class="mb-3 text-center">
            <label class="form-label d-block">Foto Profil</label>
            <img id="avatarPreview" 
                 src="{{ $avatarUrl }}" 
                 class="rounded-circle mb-2" width="120" 
                 style="object-fit: cover; border: 3px solid #007bff; box-shadow: 0 2px 8px rgba(0,0,0,0.15);">
            <input type="file" name="avatar" accept="image/*" 
                   class="form-control mt-2 @error('avatar') is-invalid @enderror" 
                   onchange="previewAvatar(this)">
            @error('avatar')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <script>
            function previewAvatar(input) {
              const file = input.files[0];
              if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                  document.getElementById('avatarPreview').src = e.target.result;
                }
                reader.readAsDataURL(file);
              }
            }
          </script>

          <!-- Data Diri -->
          <div class="row">
            @foreach ([
              'name' => 'Nama Lengkap',
              'email' => 'Email',
              'phone' => 'Telepon',
              'mobile' => 'HP',
              'job_title' => 'Jabatan',
              'location' => 'Lokasi',
              'website' => 'Website',
              'github' => 'Github',
              'twitter' => 'Twitter',
              'instagram' => 'Instagram',
              'facebook' => 'Facebook',
            ] as $field => $label)
              <div class="col-md-6 mb-3">
                <label class="form-label">{{ $label }}</label>
                <input type="text" name="{{ $field }}" 
                       class="form-control @error($field) is-invalid @enderror" 
                       value="{{ old($field, $user->$field) }}">
                @error($field)
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
            @endforeach

            <!-- Textarea untuk Alamat -->
            <div class="col-12 mb-3">
              <label class="form-label">Alamat</label>
              <textarea name="address" rows="3"
                        class="form-control @error('address') is-invalid @enderror">{{ old('address', $user->address) }}</textarea>
              @error('address')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-success">Simpan</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
