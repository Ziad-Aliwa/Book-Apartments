<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Apartment Booking Platform') - Book Apartments</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- Flatpickr CSS for interactive datepicker -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <!-- Leaflet Maps CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

    <style>
        body {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            background-color: #f8f9fa;
        }

        .main-content {
            flex: 1 0 auto;
            padding-bottom: 50px;
        }

        footer {
            flex-shrink: 0;
            background: #ffffff;
            padding: 16px 0;
            text-align: center;
            border-top: 1px solid #e9ecef;
        }

        .apartment-card {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            border-radius: 12px;
            overflow: hidden;
        }

        .apartment-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.08);
        }

        .apartment-thumb {
            height: 220px;
            object-fit: cover;
            width: 100%;
        }

        .star-rating {
            color: #ffc107;
        }

        .wishlist-btn {
            background: rgba(255, 255, 255, 0.9);
            border-radius: 50%;
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: none;
            cursor: pointer;
            transition: transform 0.15s ease, background 0.15s ease;
        }

        .wishlist-btn:hover {
            transform: scale(1.1);
            background: #ffffff;
        }

        .wishlist-btn.active i {
            color: #dc3545;
        }

        .map-container {
            height: 350px;
            border-radius: 12px;
            overflow: hidden;
            border: 1px solid #dee2e6;
        }
    </style>
    @stack('styles')
</head>

<body>
    {{-- Navbar --}}
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="{{ route('apartments.index') }}">
                <i class="bi bi-buildings-fill text-warning me-2"></i>BookApartments
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navMenu">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('apartments.index') }}">
                            <i class="bi bi-search me-1"></i>Explore
                        </a>
                    </li>
                    @auth
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('wishlists.index') }}">
                                <i class="bi bi-heart me-1"></i>Wishlist
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('messages.index') }}">
                                <i class="bi bi-chat-dots me-1"></i>Messages
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('bookings.index') }}">
                                <i class="bi bi-calendar2-check me-1"></i>My Bookings
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('dashboard.owner') }}">
                                <i class="bi bi-speedometer2 me-1"></i>Host Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('apartments.my_bookings') }}">
                                <i class="bi bi-door-open me-1"></i>My Apartments
                            </a>
                        </li>

                        @if (Auth::user()->role === 'admin')
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle text-warning" href="#" role="button" data-bs-toggle="dropdown">
                                    <i class="bi bi-shield-lock-fill me-1"></i>Admin Panel
                                </a>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="{{ route('admin.dashboard') }}"><i class="bi bi-bar-chart-fill me-2"></i>Dashboard</a></li>
                                    <li><a class="dropdown-item" href="{{ route('admin.apartments.pending') }}"><i class="bi bi-hourglass-split me-2"></i>Pending Approvals</a></li>
                                </ul>
                            </li>
                        @endif
                    @endauth
                </ul>

                <ul class="navbar-nav align-items-center">
                    @guest
                        <li class="nav-item"><a class="nav-link" href="{{ route('login') }}">Login</a></li>
                        <li class="nav-item"><a class="btn btn-outline-light btn-sm ms-2" href="{{ route('register') }}">Register</a></li>
                    @else
                        <!-- Notifications Bell -->
                        <li class="nav-item me-3">
                            <a class="nav-link position-relative" href="{{ route('notifications.index') }}" title="Notifications">
                                <i class="bi bi-bell-fill fs-5"></i>
                                @php
                                    $unreadCount = Auth::user()->unreadNotifications->count();
                                @endphp
                                @if($unreadCount > 0)
                                    <span class="position-absolute top-1 start-100 translate-middle badge rounded-pill bg-danger">
                                        {{ $unreadCount > 9 ? '9+' : $unreadCount }}
                                    </span>
                                @endif
                            </a>
                        </li>

                        <li class="nav-item me-2">
                            <a href="{{ route('apartments.create') }}" class="btn btn-warning btn-sm fw-bold">
                                <i class="bi bi-plus-lg me-1"></i>List Apartment
                            </a>
                        </li>

                        <!-- User Profile Dropdown -->
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                <i class="bi bi-person-circle me-1"></i>{{ Auth::user()->name }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end shadow">
                                <li><a class="dropdown-item" href="{{ route('dashboard.owner') }}"><i class="bi bi-grid me-2"></i>Host Dashboard</a></li>
                                <li><a class="dropdown-item" href="{{ route('wishlists.index') }}"><i class="bi bi-heart me-2"></i>My Wishlist</a></li>
                                <li><a class="dropdown-item" href="{{ route('messages.index') }}"><i class="bi bi-chat-dots me-2"></i>Inbox Messages</a></li>
                                <li><a class="dropdown-item" href="{{ route('bookings.index') }}"><i class="bi bi-ticket-perforated me-2"></i>My Bookings</a></li>
                                <li><a class="dropdown-item" href="{{ route('payments.unpaid') }}"><i class="bi bi-wallet2 me-2"></i>Unpaid Bills</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button class="dropdown-item text-danger" type="submit">
                                            <i class="bi bi-box-arrow-right me-2"></i>Logout
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @endguest
                </ul>
            </div>
        </div>
    </nav>

    {{-- Page Content --}}
    <main class="main-content">
        <div class="container pt-4">
            {{-- Flash Messages --}}
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger shadow-sm">
                    <strong><i class="bi bi-exclamation-octagon-fill me-1"></i> Please fix the following:</strong>
                    <ul class="mb-0 mt-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </div>
    </main>

    {{-- Footer --}}
    <footer>
        <div class="container">
            <small class="text-muted">
                &copy; {{ date('Y') }} <strong>BookApartments</strong>. Modern Apartment Reservation Platform.
            </small>
        </div>
    </footer>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Flatpickr JS -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <!-- Global AJAX Wishlist Script -->
    <script>
        function toggleWishlist(apartmentId, buttonElement) {
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            fetch(`/wishlists/${apartmentId}/toggle`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                }
            })
            .then(res => {
                if (res.status === 401) {
                    window.location.href = "{{ route('login') }}";
                    return;
                }
                return res.json();
            })
            .then(data => {
                if (!data) return;
                const icon = buttonElement.querySelector('i');
                if (data.favorited) {
                    buttonElement.classList.add('active');
                    icon.classList.remove('bi-heart');
                    icon.classList.add('bi-heart-fill');
                    icon.classList.add('text-danger');
                } else {
                    buttonElement.classList.remove('active');
                    icon.classList.remove('bi-heart-fill');
                    icon.classList.remove('text-danger');
                    icon.classList.add('bi-heart');
                }
            })
            .catch(err => console.error('Wishlist error:', err));
        }
    </script>
    @stack('scripts')
</body>

</html>
