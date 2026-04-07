<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>@yield('title', 'Admin Dashboard')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- Bootstrap CSS --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    {{-- DataTables CSS --}}
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    {{-- import a classic font family  --}}
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/panel.css') }}">

    @stack('styles')
</head>
<style>
    body {
        font-family: 'Roboto', sans-serif;
    }

    .sidebar {
        min-height: 100vh;
        background: linear-gradient(180deg, #2c3e50 0%, #34495e 100%);
        color: white;
    }

    .sidebar a {
        color: rgba(255, 255, 255, 0.8);
        text-decoration: none;
        padding: 12px 20px;
        display: block;
        transition: all 0.3s;
    }

    .sidebar a:hover {
        background: rgba(255, 255, 255, 0.1);
        color: white;
        padding-left: 25px;
    }

    .sidebar a.active {
        background: rgba(52, 152, 219, 0.3);
        color: white;
        border-left: 4px solid #3498db;
    }

    .sidebar .nav-header {
        padding: 20px;
        font-size: 1.5rem;
        font-weight: bold;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }

    .main-content {
        background: #ecf0f1;
        min-height: 100vh;
    }

    .dropdown-toggle::after {
        float: right;
        margin-top: 8px;
    }

    .submenu {
        background: rgba(0, 0, 0, 0.2);
        padding-left: 20px;
    }

    .submenu a {
        padding: 10px 20px;
        font-size: 0.9rem;
    }
</style>

<body class="bg-light">



    <div class="container-fluid">
        <div class="row">

            {{-- Sidebar --}}
            <nav class="col-md-2 col-lg-2 sidebar p-0">
                <div class="nav-header">
                    <i class="bi bi-speedometer2"></i> Admin Panel
                </div>
                <ul class="nav flex-column mt-3">
                    <li class="nav-item">
                        <a href="/admin/dashboard"
                            class="nav-link {{ request()->is('admin/dashboard') ? 'active' : '' }}">
                            <i class="bi bi-house-door"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="/admin/agents-list"
                            class="nav-link {{ request()->is('admin/agents-list') ? 'active' : '' }}">
                            <i class="bi bi-people"></i> All Agents
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.users.index') }}"
                            class="nav-link {{ request()->is('admin/users*') ? 'active' : '' }}">
                            <i class="bi bi-people-fill"></i> User Management
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route('admin.bookings.all') }}"
                            class="nav-link {{ request()->is('admin/bookings/all') ? 'active' : '' }}">
                            <i class="bi bi-calendar-check-fill"></i> All Bookings
                        </a>
                    </li>

                    <!-- Settings Dropdown -->
                    <li class="nav-item">
                        <a class="nav-link dropdown-toggle {{ request()->is('admin/settings*') ? 'active' : '' }}"
                            data-bs-toggle="collapse" href="#settingsMenu" role="button"
                            aria-expanded="{{ request()->is('admin/settings*') ? 'true' : 'false' }}">
                            <i class="bi bi-gear"></i> Settings
                        </a>
                        <div class="collapse submenu {{ request()->is('admin/settings*') ? 'show' : '' }}"
                            id="settingsMenu">
                            <a href="{{ route('admin.settings.bookings') }}"
                                class="nav-link {{ request()->is('admin/settings/bookings') ? 'active' : '' }}">
                                <i class="bi bi-calendar-check"></i> Bookings
                            </a>
                        </div>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.notifications.index') }}"
                            class="nav-link {{ request()->is('admin/notifications*') ? 'active' : '' }}">
                            <i class="bi bi-bell-fill"></i> Annoucments <i class="bi bi-speaker"></i>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.merchants.index') }}"
                            class="nav-link {{ request()->is('admin/merchants*') ? 'active' : '' }}">
                            <i class="bi bi-shop"></i> Merchants
                        </a>
                    </li>

                    <li class="nav-item mt-auto" style="position: absolute; bottom: 20px; width: 100%;">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="nav-link btn btn-link text-start w-100"
                                style="color: rgba(255,255,255,0.8);">
                                <i class="bi bi-box-arrow-right"></i> Logout
                            </button>
                        </form>
                    </li>
                </ul>
            </nav>
            <style>
                .nav-link.active {
                    background-color: #0d6efd;
                    color: white;
                }

                .nav-link active:hover {
                    background-color: #0b5ed7;
                    color: white;
                }

                .nav-link a {
                    color: #0d6efd;
                    line-height: 1.5;
                    font-size: 1.1rem;
                    letter-spacing: 0.02em;
                    font-weight: 500;
                }
            </style>

            {{-- Main Content --}}
            <main class="col-md-10 ms-sm-auto px-4 py-4">
                @yield('content')
            </main>

        </div>
    </div>

    {{-- JS: Bootstrap + DataTables --}}
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>

    @stack('scripts')
</body>

</html>
