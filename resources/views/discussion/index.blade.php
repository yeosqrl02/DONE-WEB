@extends('layouts.app')
@section('title', 'Diskusi')

@section('content')
<div class="container py-5">
    <h2 class="text-center fw-bold mb-5">💬 Forum Diskusi Mahasiswa</h2>

    {{-- Form Ajukan Pertanyaan --}}
    <div class="card border-0 shadow mb-5">
        <div class="card-header bg-gradient bg-primary text-white">Ajukan Pertanyaan Baru</div>
        <div class="card-body">
            <form action="{{ route('discussions.store') }}" method="POST" class="d-flex">
                @csrf
                <input type="text" name="pertanyaan" class="form-control me-2 rounded-pill px-4" placeholder="Tulis pertanyaanmu di sini..." required>
                <button type="submit" class="btn btn-outline-dark rounded-pill px-4">Kirim</button>

            </form>
        </div>
    </div>

    {{-- Daftar Diskusi --}}
    @forelse ($discussions as $discussion)
        <div class="card mb-4 shadow-sm border-0">
            <div class="card-body">
                <h5 class="fw-bold text-primary">🗨️ {{ $discussion->pertanyaan }}</h5>
                <p class="text-muted small">Oleh <strong>{{ $discussion->user->name }}</strong> &bull; {{ $discussion->created_at->translatedFormat('d F Y H:i') }}</p>
                <div class="d-flex gap-2">
                    <form method="POST" action="#">
                        @csrf
                        <button class="btn btn-outline-danger btn-sm rounded-pill">❤️ Suka</button>
                    </form>
                    <button class="btn btn-outline-secondary btn-sm rounded-pill" data-bs-toggle="collapse" data-bs-target="#balasan{{ $discussion->id }}">💬 Balas</button>
                </div>

                {{-- Form Balasan --}}
                <div class="collapse mt-3" id="balasan{{ $discussion->id }}">
                    <form action="{{ route('replies.store') }}" method="POST" class="d-flex">
                        @csrf
                        <input type="hidden" name="discussion_id" value="{{ $discussion->id }}">
                        <input type="text" name="isi" class="form-control me-2 rounded-pill px-3" placeholder="Tulis balasan..." required>
                        <button type="submit" class="btn btn-primary rounded-pill px-4">Kirim</button>
                    </form>
                </div>

                {{-- Balasan --}}
                @if ($discussion->replies->count())
                    <div class="mt-4 border-top pt-3">
                        <h6 class="text-muted mb-3">💡 Balasan:</h6>
                        @foreach ($discussion->replies as $reply)
                            <div class="d-flex align-items-start mb-3">
                                <div class="rounded-circle bg-secondary me-3" style="width: 40px; height: 40px;"></div>
                                <div class="bg-light rounded shadow-sm px-4 py-2 flex-fill">
                                    <p class="mb-1">{{ $reply->isi }}</p>
                                    <small class="text-muted">Dibalas oleh {{ $reply->user->name ?? 'Anonim' }} pada {{ $reply->created_at->translatedFormat('d F Y H:i') }}</small>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    @empty
        <div class="alert alert-info text-center">Belum ada diskusi. Jadilah yang pertama bertanya! 🤔</div>
    @endforelse
</div>
@endsection
