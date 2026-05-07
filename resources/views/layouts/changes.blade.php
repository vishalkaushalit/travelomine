<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>@yield('title', 'Changes Panel')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    {{-- Bootstrap Icons CDN --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">

    <!-- AdminLTE CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">

    @stack('styles')

    <style>
        body {
            font-family: 'Source Sans Pro', sans-serif;
        }
    </style>
</head>

<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">
        @auth
            <div class="container-fluid mt-2">
                {{-- @include('components.user-notifications') --}}
            </div>
        @endauth

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

                    <li class="nav-item d-none d-sm-inline-block">
                        <a href="{{ route('changes.dashboard') }}" class="nav-link">Dashboard</a>
                    </li>

                    <li class="nav-item d-none d-sm-inline-block">
                        <a href="{{ route('changes.bookings.index') }}" class="nav-link">All Bookings</a>
                    </li>
                @endauth

                @guest
                    <li class="nav-item d-none d-sm-inline-block">
                        <a href="{{ route('changes.login') }}" class="nav-link">Changes Login</a>
                    </li>
                @endguest
            </ul>

            <!-- Right navbar links -->
            <ul class="navbar-nav ml-auto">
                @auth
                    @include('layouts.notification-bell')
                    <!-- User Account Menu -->
                    <li class="nav-item dropdown">
                        <a class="nav-link" data-toggle="dropdown" href="#" aria-expanded="false">
                            <i class="far fa-user"></i>
                            <span class="d-none d-md-inline ml-1">{{ auth()->user()->name }}</span>
                        </a>

                        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right" style="left: inherit; right: 0px;">
                            <span class="dropdown-item dropdown-header">{{ auth()->user()->email }}</span>

                            <div class="dropdown-divider"></div>

                            <a href="#" class="dropdown-item">
                                <i class="fas fa-user mr-2"></i> My Profile
                            </a>

                            <div class="dropdown-divider"></div>

                            <form method="POST" action="{{ route('changes.logout') }}" class="d-inline">
                                @csrf
                                <button type="submit" class="dropdown-item">
                                    <i class="fas fa-sign-out-alt mr-2"></i> Logout
                                </button>
                            </form>
                        </div>
                    </li>
                @endauth
            </ul>
        </nav>

        @auth
            <!-- Main Sidebar Container -->
            <aside class="main-sidebar sidebar-dark-primary elevation-4">
                <!-- Brand Logo -->
                <a href="{{ route('changes.dashboard') }}" class="brand-link">
                    <span class="brand-text font-weight-light">Changes Panel</span>
                </a>

                <!-- Sidebar -->
                <div class="sidebar">
                    <!-- Sidebar Menu -->
                    <nav class="mt-2">
                        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                            data-accordion="false">
                            <li class="nav-item">
                                <a href="{{ route('changes.dashboard') }}"
                                    class="nav-link {{ request()->routeIs('changes.dashboard') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-tachometer-alt"></i>
                                    <p>Dashboard</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('changes.bookings.index') }}"
                                    class="nav-link {{ request()->routeIs('changes.bookings.*') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-calendar-check"></i>
                                    <p>All Bookings</p>
                                </a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </aside>
        @endauth

        <!-- Content Wrapper -->
        <div class="content-wrapper">
            @yield('content')
        </div>

        @auth
            <!-- Footer -->
            <footer class="main-footer">
                <strong>Copyright &copy; {{ date('Y') }} <a href="#">Travelomile - Changes Panel</a>.</strong>
                All rights reserved.
                <div class="float-right d-none d-sm-inline-block">
                    <b>Version</b> 1.0.0
                </div>
            </footer>
        @endauth
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
