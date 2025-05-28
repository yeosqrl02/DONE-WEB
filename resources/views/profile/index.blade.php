@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <!-- Sidebar Kiri -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-center">
                    <img src="{{ Auth::user()->avatar ? asset('storage/avatars/' . Auth::user()->avatar) : 'https://bootdey.com/img/Content/avatar/avatar7.png' }}" class="rounded-circle mb-3" width="150" id="currentAvatar">
                    <h4 class="mb-0">{{ Auth::user()->name }}</h4>
                    <p class="text-muted">
                        {{ Auth::user()->job_title ?? 'Full Stack Developer' }}<br>
                        {{ Auth::user()->location ?? 'Bay Area, San Francisco, CA' }}
                    </p>
                </div>
                <ul class="list-group list-group-flush mt-3">
                    <li class="list-group-item"><strong>Website:</strong> <span class="float-end">{{ Auth::user()->website ?? '-' }}</span></li>
                    <li class="list-group-item"><strong>Github:</strong> <span class="float-end">{{ Auth::user()->github ?? '-' }}</span></li>
                    <li class="list-group-item"><strong>Twitter:</strong> <span class="float-end">{{ Auth::user()->twitter ?? '-' }}</span></li>
                    <li class="list-group-item"><strong>Instagram:</strong> <span class="float-end">{{ Auth::user()->instagram ?? '-' }}</span></li>
                    <li class="list-group-item"><strong>Facebook:</strong> <span class="float-end">{{ Auth::user()->facebook ?? '-' }}</span></li>
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
                    @php $user = Auth::user(); @endphp

                    @foreach ([
                        'Full Name' => $user->name,
                        'Email' => $user->email,
                        'Phone' => $user->phone ?? '-',
                        'Mobile' => $user->mobile ?? '-',
                        'Address' => $user->address ?? '-',
                        'Job Title' => $user->job_title ?? '-',
                        'Location' => $user->location ?? '-',
                    ] as $label => $value)
                        <div class="row">
                            <div class="col-sm-3"><h6 class="mb-0">{{ $label }}</h6></div>
                            <div class="col-sm-9 text-secondary">{{ $value }}</div>
                        </div>
                        <hr>
                    @endforeach

                    <div class="text-center">
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
          <!-- Upload Avatar dengan Preview -->
          <div class="mb-3 text-center">
            <label class="form-label d-block">Foto Profil</label>
            <img id="avatarPreview" src="{{ $user->avatar ? asset('storage/avatars/' . $user->avatar) : 'https://bootdey.com/img/Content/avatar/avatar7.png' }}" class="rounded-circle mb-2" width="120">
            <input type="file" name="avatar" accept="image/*" class="form-control mt-2 @error('avatar') is-invalid @enderror" onchange="previewAvatar(this)">
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
          <div class="row mb-3">
            <div class="col-md-6">
              <label class="form-label">Nama Lengkap</label>
              <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required>
              @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
              <label class="form-label">Email</label>
              <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required>
              @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
          </div>

          <div class="row mb-3">
            <div class="col-md-6">
              <label class="form-label">Telepon</label>
              <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone', $user->phone) }}">
              @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
              <label class="form-label">HP</label>
              <input type="text" name="mobile" class="form-control @error('mobile') is-invalid @enderror" value="{{ old('mobile', $user->mobile) }}">
              @error('mobile')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label">Alamat</label>
            <textarea name="address" class="form-control @error('address') is-invalid @enderror">{{ old('address', $user->address) }}</textarea>
            @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>

          <div class="row mb-3">
            <div class="col-md-6">
              <label class="form-label">Pekerjaan / Jabatan</label>
              <input type="text" name="job_title" class="form-control @error('job_title') is-invalid @enderror" value="{{ old('job_title', $user->job_title) }}">
              @error('job_title')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
              <label class="form-label">Lokasi</label>
              <input type="text" name="location" class="form-control @error('location') is-invalid @enderror" value="{{ old('location', $user->location) }}">
              @error('location')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
          </div>

          <div class="row mb-3">
            <div class="col-md-6">
              <label class="form-label">Website</label>
              <input type="url" name="website" class="form-control @error('website') is-invalid @enderror" value="{{ old('website', $user->website) }}">
              @error('website')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
              <label class="form-label">Github</label>
              <input type="text" name="github" class="form-control @error('github') is-invalid @enderror" value="{{ old('github', $user->github) }}">
              @error('github')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
          </div>

          <div class="row mb-3">
            <div class="col-md-6">
              <label class="form-label">Twitter</label>
              <input type="text" name="twitter" class="form-control @error('twitter') is-invalid @enderror" value="{{ old('twitter', $user->twitter) }}">
              @error('twitter')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
              <label class="form-label">Instagram</label>
              <input type="text" name="instagram" class="form-control @error('instagram') is-invalid @enderror" value="{{ old('instagram', $user->instagram) }}">
              @error('instagram')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label">Facebook</label>
            <input type="text" name="facebook" class="form-control @error('facebook') is-invalid @enderror" value="{{ old('facebook', $user->facebook) }}">
            @error('facebook')<div class="invalid-feedback">{{ $message }}</div>@enderror
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
