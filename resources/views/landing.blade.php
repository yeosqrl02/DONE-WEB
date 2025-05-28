@extends('layouts.app')

@section('title', 'Selamat Datang')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

<style>
    .hero-section {
        background: linear-gradient(to right, #4e54c8, #8f94fb);
        color: white;
        padding: 100px 20px;
        text-align: center;
        border-radius: 10px;
    }
    .hero-section h1 {
        font-size: 3rem;
        font-weight: bold;
    }
    .hero-section p {
        font-size: 1.2rem;
        margin-top: 10px;
    }
    .features, .testimoni, .cta {
        margin-top: 60px;
    }
    .feature-box {
        background: white;
        border-radius: 10px;
        padding: 30px;
        box-shadow: 0 0 15px rgba(0, 0, 0, 0.05);
        text-align: center;
        height: 100%;
    }
    .feature-box h5 {
        font-weight: bold;
        margin-bottom: 15px;
    }
    .feature-box i {
        font-size: 2.5rem;
        color: #4e54c8;
        margin-bottom: 10px;
    }
    .testimonial {
        background-color: #ffffff;
        padding: 25px;
        border-radius: 10px;
        box-shadow: 0 0 10px rgba(0,0,0,0.05);
        margin-bottom: 20px;
    }
    .cta {
        background-color: #ffc107;
        color: black;
        padding: 40px 20px;
        border-radius: 10px;
        text-align: center;
    }
    .footer {
        padding: 20px 0;
        text-align: center;
        font-size: 14px;
        color: #777;
    }
</style>
@endpush

@section('content')
<div class="container py-5">
    <!-- Hero Section -->
    <div class="hero-section mb-5">
        <h1>Selamat Datang di <span style="color: #ffc107;">DONE!</span></h1>
        <p class="mt-2">Sistem manajemen tugas, diskusi, dan jadwal akademik mahasiswa dalam satu platform.</p>
        <a href="{{ route('login') }}" class="btn btn-warning btn-lg mt-4">Masuk Sekarang</a>
    </div>

    <!-- Features -->
    <div class="row features">
        <div class="col-md-4 mb-4">
            <div class="feature-box h-100">
                <i class="bi bi-list-check"></i>
                <h5>Manajemen Tugas</h5>
                <p>Kelola tugas dengan mudah: tambahkan, edit, dan cek status tugas secara real-time.</p>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="feature-box h-100">
                <i class="bi bi-calendar-event"></i>
                <h5>Kalender Akademik</h5>
                <p>Lihat dan rencanakan kegiatan akademik kamu melalui tampilan kalender interaktif.</p>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="feature-box h-100">
                <i class="bi bi-chat-left-dots"></i>
                <h5>Forum Diskusi</h5>
                <p>Ajukan pertanyaan dan dapatkan jawaban dari teman-temanmu di forum diskusi.</p>
            </div>
        </div>
    </div>

    <!-- Testimonials -->
    <div class="testimoni mt-5">
        <h3 class="text-center mb-4">Apa Kata Mereka?</h3>
        <div class="row">
            <div class="col-md-6">
                <div class="testimonial">
                    <p>"DONE! sangat membantu saya mengatur tugas-tugas kuliah dan tidak pernah telat lagi!"</p>
                    <small>- Yustika, Mahasiswa Sistem Informasi</small>
                </div>
            </div>
            <div class="col-md-6">
                <div class="testimonial">
                    <p>"Saya suka fitur diskusinya, jadi bisa tanya-tanya bareng teman sekelas kapan pun."</p>
                    <small>- Ricko, Mahasiswi Pendidikan Matematika</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Call to Action -->
    <div class="cta mt-5">
        <h4>Gabung Sekarang dan Jadikan Hidup Akademikmu Lebih Teratur!</h4>
        <a href="{{ route('register') }}" class="btn btn-dark btn-lg mt-3">Daftar Gratis</a>
    </div>

    <!-- Footer -->
    <div class="footer mt-5">
        &copy; {{ now()->year }} DONE! by Tim Pengembang | Telkom University
    </div>
</div>
@endsection
