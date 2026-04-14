<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Charging Panel | Flight Booking CRM</title>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- AdminLTE CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">

    <link rel="stylesheet" href="{{ asset('css/panel.css') }}">

    @stack('styles')
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

    <!-- Navbar -->
    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
        <!-- Left navbar links -->
        <ul class="navbar-nav">
            @auth
                <li class="nav-item">
                    <a class="nav-link" data-widget="pushmenu" href="#" role="button">
                        <i class="fas fa-bars"></i>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('charge.dashboard') }}" class="nav-link {{ request()->routeIs('charging.dashboard') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>Dashboard</p>
                    </a>
                </li>
                <li class="nav-item">
                    {{-- <a href="{{ route('charge.bookings.index') }}" class="nav-link {{ request()->routeIs('charge.bookings.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-clipboard-list"></i>
                        <p>Bookings</p>
                    </a> --}}
                </li>
            @endauth

            @guest
                <li class="nav-item d-none d-sm-inline-block">
                    <a href="{{ route('charge.login') }}" class="nav-link">Charge Login</a>
                </li>
            @endguest
        </ul>

        <!-- Right navbar links -->
        <ul class="navbar-nav ml-auto">
            @auth
                <!-- User Account Menu -->
                <li class="nav-item dropdown">
                    <a class="nav-link" data-toggle="dropdown" href="#" aria-expanded="false">
                        <i class="far fa-user"></i>
                        <span class="d-none d-md-inline ml-1">{{ auth()->user()->alias_name }}</span>
                    </a>

                    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right" style="left: inherit; right: 0px;">
                        <span class="dropdown-item dropdown-header">{{ auth()->user()->email }}</span>

                        <div class="dropdown-divider"></div>

                        <a href="#" class="dropdown-item">
                            <i class="fas fa-user mr-2"></i> My Profile
                        </a>

                        <div class="dropdown-divider"></div>

                        <form method="POST" action="{{ route('charge.logout') }}">
                            @csrf
                            <button type="submit" class="dropdown-item">
                                <i class="fas fa-sign-out-alt mr-2"></i> Logout
                            </button>
                        </form>
                    </div>
                </li>
            @endauth

            @guest
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('charge.login') }}">
                        <i class="fas fa-sign-in-alt"></i>
                        <span class="d-none d-md-inline ml-1">Login</span>
                    </a>
                </li>
            @endguest
        </ul>
    </nav>

    <!-- Main Sidebar Container -->
    <aside class="main-sidebar sidebar-dark-primary elevation-4">
        <!-- Brand Logo -->
        <a href="{{ auth()->check() ? route('charge.dashboard') : route('charge.login') }}" class="brand-link">
            <i class="fas fa-plane-departure brand-image ml-3"></i>
            <span class="brand-text font-weight-light">Charge Panel</span>
        </a>

        <!-- Sidebar -->
        <div class="sidebar">

            @auth
                <!-- Sidebar user panel -->
                <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                    <div class="image">
                        <i class="fas fa-user-circle fa-2x text-white"></i>
                    </div>
                    <div class="info">
                        <a href="#" class="d-block">{{ auth()->user()->name }}</a>
                        <small class="text-muted">Charge</small>
                    </div>
                </div>

                <!-- Sidebar Menu -->
                <nav class="mt-2">
                    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">

                        <!-- Dashboard -->
                        <li class="nav-item">
                            <a href="{{ route('charge.dashboard') }}" class="nav-link {{ request()->routeIs('charge.dashboard') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-tachometer-alt"></i>
                                <p>Dashboard</p>
                            </a>
                        </li>

                      
                        </li>

                        <!-- Reports (Placeholder) -->
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="nav-icon fas fa-chart-bar"></i>
                                <p>Reports</p>
                            </a>
                        </li>

                        <!-- Settings (Placeholder) -->
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="nav-icon fas fa-cog"></i>
                                <p>Settings</p>
                            </a>
                        </li>
                    </ul>
                </nav>
            @endauth

        </div>
    </aside>
    @auth
        <div class="container-fluid mt-2">
            @include('components.user-notifications')
        </div>
    @endauth
    <!-- Content Wrapper -->
    <div class="content-wrapper">
        <section class="content">
            @yield('content')
        </section>
    </div>

</div>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Bootstrap 4 -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>

@stack('scripts')
</body>
</html>
