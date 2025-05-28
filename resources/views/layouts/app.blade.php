<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>@yield('title', config('app.name', 'Laravel'))</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net" />
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Bootstrap CSS -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
        rel="stylesheet"
    />

    <!-- Bootstrap Icons -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css"
        rel="stylesheet"
    />

    @stack('styles')

    <style>
        body {
            background-color: #f8f9fa;
        }

        /* Navbar Custom sesuai style sebelumnya */
        .navbar-custom {
            background-color: #2d2d2d;
        }

        .navbar-custom .navbar-brand,
        .navbar-custom .nav-link {
            color: white !important;
        }

        .navbar-custom .nav-link:hover,
        .navbar-custom .nav-link.active {
            color: #ffc107 !important; /* kuning */
            font-weight: bold;
        }

        .navbar-nav-center {
            flex-grow: 1;
            justify-content: center;
            display: flex;
        }

        /* Footer Styling */
        footer {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding-top: 3rem;
            padding-bottom: 1rem;
            border-top: 1px solid #dee2e6;
            color: #6c757d;
        }

        footer h5 {
            color: #0d6efd;
            font-weight: 700;
            margin-bottom: 1rem;
        }

        footer a {
            color: #6c757d;
            text-decoration: none;
            transition: color 0.2s ease;
        }

        footer a:hover {
            color: #0d6efd;
            text-decoration: underline;
        }

        footer .social-icons a {
            font-size: 1.5rem;
            margin-right: 1rem;
            color: #6c757d;
        }

        footer .social-icons a:hover {
            color: #0d6efd;
        }
    </style>
</head>
<body class="font-sans antialiased bg-light">
    <div class="min-vh-100 d-flex flex-column">

        {{-- Navbar (jika bukan landing) --}}
        @unless(request()->routeIs('landing'))
        <nav class="navbar navbar-expand-lg navbar-dark navbar-custom px-4 sticky-top shadow-sm">
            <a class="navbar-brand fw-bold" href="{{ route('dashboard') }}">DONE!</a>
            <button
                class="navbar-toggler"
                type="button"
                data-bs-toggle="collapse"
                data-bs-target="#navbarMain"
                aria-controls="navbarMain"
                aria-expanded="false"
                aria-label="Toggle navigation"
            >
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarMain">
                <ul class="navbar-nav navbar-nav-center mb-2 mb-lg-0">
                    <li class="nav-item mx-2">
                        <a
                            class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"
                            href="{{ route('dashboard') }}"
                            >Beranda</a
                        >
                    </li>
                    <li class="nav-item mx-2">
                        <a
                            class="nav-link {{ request()->routeIs('tasks') ? 'active' : '' }}"
                            href="{{ route('tasks') }}"
                            >Tugas</a
                        >
                    </li>
                    <li class="nav-item mx-2">
                        <a
                            class="nav-link {{ request()->routeIs('discussion') ? 'active' : '' }}"
                            href="{{ route('discussion') }}"
                            >Diskusi</a
                        >
                    </li>
                    <li class="nav-item mx-2">
                        <a
                            class="nav-link {{ request()->routeIs('calendar') ? 'active' : '' }}"
                            href="{{ route('calendar') }}"
                            >Kalender</a
                        >
                    </li>
                </ul>

                <div class="dropdown ms-auto">
                    <a
                        class="nav-link dropdown-toggle text-white"
                        href="#"
                        role="button"
                        data-bs-toggle="dropdown"
                    >
                        {{ Auth::user()->name ?? 'Guest' }}
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        @auth
                        <li><a class="dropdown-item" href="{{ route('profile') }}">Profil</a></li>
                        <li><hr class="dropdown-divider" /></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button class="dropdown-item" type="submit">Logout</button>
                            </form>
                        </li>
                        @else
                        <li><a class="dropdown-item" href="{{ route('login') }}">Login</a></li>
                        @endauth
                    </ul>
                </div>
            </div>
        </nav>
        @endunless

        {{-- Optional Header --}}
        @isset($header)
        <header class="bg-white shadow-sm">
            <div class="container py-4">
                {{ $header }}
            </div>
        </header>
        @endisset

        {{-- Main Content --}}
        <main class="flex-grow-1 pt-4">
            <div class="container">
                @yield('content')
            </div>
        </main>

        {{-- Footer --}}
        <footer>
            <div class="container">
                <div class="row">
                    <!-- Tentang Done -->
                    <div class="col-md-4 mb-4">
                        <h5>Tentang Done</h5>
                        <p>
                            Done! adalah platform manajemen tugas dan aktivitas yang membantu kamu tetap produktif dan terorganisir.
                            Kami berkomitmen menyediakan fitur terbaik untuk meningkatkan kinerja harianmu.
                        </p>
                        <p class="small mb-0">&copy; {{ date('Y') }} Done!. All rights reserved.</p>
                    </div>

                    <!-- Navigasi -->
                    <div class="col-md-3 mb-4">
                        <h5>Navigasi</h5>
                        <ul class="list-unstyled">
                            <li><a href="{{ route('dashboard') }}">Beranda</a></li>
                            <li><a href="{{ route('tasks') }}">Tugas</a></li>
                            <li><a href="{{ route('discussion') }}">Diskusi</a></li>
                            <li><a href="{{ route('calendar') }}">Kalender</a></li>
                        </ul>
                    </div>

                    <!-- Kontak -->
                    <div class="col-md-3 mb-4">
                        <h5>Kontak Kami</h5>
                        <address>
                            Jalan Telekomuniasi No.31<br />
                            Bandung, Indonesia<br />
                            Email: <a href="mailto:support@mydashboard.com">support@mydashboard.com</a><br />
                            Telepon: <a href="tel:+628123456789">+62 812 3456 789</a>
                        </address>
                    </div>

                    <!-- Sosial Media -->
                    <div class="col-md-2 mb-4">
                        <h5>Sosial Media</h5>
                        <div class="social-icons">
                            <a href="https://facebook.com/mydashboard" target="_blank" rel="noopener"><i class="bi bi-facebook"></i></a>
                            <a href="https://twitter.com/mydashboard" target="_blank" rel="noopener"><i class="bi bi-twitter"></i></a>
                            <a href="https://instagram.com/mydashboard" target="_blank" rel="noopener"><i class="bi bi-instagram"></i></a>
                            <a href="https://linkedin.com/company/mydashboard" target="_blank" rel="noopener"><i class="bi bi-linkedin"></i></a>
                        </div>
                    </div>
                </div>

                <div class="border-top pt-3 pb-4 text-center small">
                    <a href="#">Privacy Policy</a> |
                    <a href="#">Terms of Service</a>
                </div>
            </div>
        </footer>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    @stack('scripts')
</body>
</html>
