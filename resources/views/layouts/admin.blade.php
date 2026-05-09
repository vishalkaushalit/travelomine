<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>@yield('title', 'Admin Dashboard')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Font Awesome 5 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <!-- Bootstrap 4 CSS (stable) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">

    <!-- DataTables Bootstrap 4 compatible -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap4.min.css">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">

    <!-- Custom Panel CSS -->
    <link rel="stylesheet" href="{{ asset('css/panel.css') }}">

    @stack('styles')

    <style>
        /* === GLOBAL RESET & BODY === */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Roboto', sans-serif;
            background-color: #ecf0f1;
            overflow-x: hidden;
        }

        /* === SIDEBAR STYLES - Bootstrap 4 friendly === */
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(180deg, #2c3e50 0%, #1a2632 100%);
            color: #fff;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        /* Sidebar nav links container */
        .sidebar .nav {
            flex-direction: column;
            width: 100%;
        }

        .sidebar .nav-item {
            width: 100%;
        }

        .sidebar a.nav-link {
            color: rgba(255, 255, 255, 0.85);
            text-decoration: none;
            padding: 12px 20px;
            display: block;
            transition: all 0.25s ease;
            font-size: 0.95rem;
            border-left: 3px solid transparent;
        }

        .sidebar a.nav-link:hover {
            background: rgba(255, 255, 255, 0.1);
            color: white;
            padding-left: 25px;
            border-left-color: #3498db;
        }

        .sidebar a.nav-link.active {
            color: white;
            font-weight: 500;
        }

        /* Dropdown toggle link (parent) */
        .sidebar .dropdown-toggle-link {
            cursor: pointer;
            position: relative;
        }

        .sidebar .dropdown-toggle-link::after {
            content: "\f078";
            font-family: "Font Awesome 5 Free";
            font-weight: 900;
            float: right;
            margin-top: 3px;
            transition: transform 0.3s ease;
            font-size: 0.75rem;
            opacity: 0.8;
        }

        .sidebar .dropdown-toggle-link[aria-expanded="true"]::after {
            transform: rotate(180deg);
        }

        /* Submenu styling */
        .sidebar .submenu {
            background: rgba(0, 0, 0, 0.25);
            padding-left: 0;
            list-style: none;
        }

        .sidebar .submenu .nav-item {
            width: 100%;
        }

        .sidebar .submenu a.nav-link {
            padding: 10px 20px 10px 45px;
            font-size: 0.85rem;
            background: transparent;
            border-left: 3px solid transparent;
        }

        .sidebar .submenu a.nav-link:hover {
            background: rgba(255, 255, 255, 0.08);
            padding-left: 50px;
        }

        .sidebar .submenu a.nav-link.active {
            background: rgba(52, 152, 219, 0.3);
            border-left-color: #3498db;
        }

        /* Sidebar header */
        .nav-header {
            padding: 20px 16px;
            font-size: 1.4rem;
            font-weight: 600;
            border-bottom: 1px solid rgba(255, 255, 255, 0.15);
            margin-bottom: 10px;
            letter-spacing: 0.5px;
        }

        .nav-header i {
            margin-right: 8px;
        }

        /* Logout button */
        .logout-btn-wrapper {
            position: absolute;
            bottom: 20px;
            width: calc(100% - 30px);
            left: 0;
            right: 0;
            margin: 0 15px;
        }

        .logout-btn-wrapper .btn-logout-sidebar {
            display: block;
            width: 100%;
            text-align: left;
            background: rgba(231, 76, 60, 0.2);
            border: none;
            color: rgba(255, 255, 255, 0.9);
            padding: 12px 20px;
            border-radius: 8px;
            transition: all 0.2s;
            font-weight: 500;
        }

        .logout-btn-wrapper .btn-logout-sidebar:hover {
            background: rgba(231, 76, 60, 0.5);
            color: white;
        }

        /* MAIN CONTENT */
        .main-content {
            background: #ecf0f1;
            min-height: 100vh;
            padding: 20px 30px;
        }

        /* Custom card styles */
        .card {
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            border: none;
        }

        .card-header {
            background: white;
            border-bottom: 1px solid #e2e8f0;
            font-weight: 600;
        }

        /* Adjust dataTable wrapper for Bootstrap 4 */
        .dataTables_wrapper .dataTables_paginate .paginate_button {
            padding: 0.25rem 0.75rem;
        }

        /* responsive fix */
        @media (max-width: 768px) {
            .sidebar {
                min-height: auto;
                position: relative;
            }

            .logout-btn-wrapper {
                position: relative;
                margin-top: 30px;
                bottom: 0;
            }

            .main-content {
                padding: 15px;
            }
        }

        /* Small icons spacing */
        .sidebar .nav-link i,
        .sidebar .dropdown-toggle-link i {
            margin-right: 10px;
            width: 22px;
            text-align: center;
        }

        /* Fix dropdown toggle active state */
        .sidebar .nav-link.dropdown-toggle-link.active {
            background: rgba(52, 152, 219, 0.25);
            border-left-color: #3498db;
        }

        /* Fix for collapse show on bootstrap 4 */
        .sidebar .submenu.collapse:not(.show) {
            display: none;
        }

        .sidebar .submenu.collapse.show {
            display: block;
        }
    </style>
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            {{-- SIDEBAR --}}
            <div class="col-md-2 col-lg-2 sidebar p-0 position-relative">
                <div class="nav-header">
                    <i class="bi bi-speedometer2"></i> Admin Panel
                </div>

                <ul class="nav flex-column">
                    {{-- Dashboard --}}
                    <li class="nav-item">
                        <a href="/admin/dashboard"
                            class="nav-link {{ request()->is('admin/dashboard') ? 'active' : '' }}">
                            <i class="bi bi-house-door"></i> Dashboard
                        </a>
                    </li>

                    {{-- All Agents --}}
                    <li class="nav-item">
                        <a href="/admin/agents-list"
                            class="nav-link {{ request()->is('admin/agents-list') ? 'active' : '' }}">
                            <i class="bi bi-people"></i> All Agents
                        </a>
                    </li>

                    {{-- User Management --}}
                    <li class="nav-item">
                        <a href="{{ route('admin.users.index') }}"
                            class="nav-link {{ request()->is('admin/users*') ? 'active' : '' }}">
                            <i class="bi bi-people-fill"></i> User Management
                        </a>
                    </li>

                    {{-- All Bookings --}}
                    <li class="nav-item">
                        <a href="{{ route('admin.bookings.all') }}"
                            class="nav-link {{ request()->is('admin/bookings/all') ? 'active' : '' }}">
                            <i class="bi bi-calendar-check-fill"></i> All Bookings
                        </a>
                    </li>

                    {{-- Settings Dropdown (Bootstrap 4 collapse) --}}
                    <li class="nav-item dropdown-custom">
                        <a class="nav-link dropdown-toggle-link {{ request()->is('admin/settings*') ? 'active' : '' }}"
                            href="#settingsMenu" role="button" data-toggle="collapse"
                            aria-expanded="{{ request()->is('admin/settings*') ? 'true' : 'false' }}"
                            aria-controls="settingsMenu">
                            <i class="bi bi-gear"></i> Settings
                        </a>
                        <div class="collapse submenu {{ request()->is('admin/settings*') ? 'show' : '' }}"
                            id="settingsMenu">
                            <div class="nav flex-column">
                                <a href="{{ route('admin.settings.bookings') }}"
                                    class="nav-link {{ request()->is('admin/settings/bookings') ? 'active' : '' }}">
                                    <i class="bi bi-calendar-check"></i> Bookings
                                </a>
                            </div>
                        </div>
                    </li>

                    {{-- Announcements --}}
                    <li class="nav-item">
                        <a href="{{ route('admin.notifications.index') }}"
                            class="nav-link {{ request()->is('admin/notifications*') ? 'active' : '' }}">
                            <i class="bi bi-bell-fill"></i> Announcements
                        </a>
                    </li>

                    {{-- Merchants --}}
                    <li class="nav-item">
                        <a href="{{ route('admin.merchants.index') }}"
                            class="nav-link {{ request()->is('admin/merchants*') ? 'active' : '' }}">
                            <i class="bi bi-shop"></i> Merchants
                        </a>
                    </li>
                </ul>

                {{-- Logout Button at bottom --}}
                <div class="logout-btn-wrapper">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="btn-logout-sidebar">
                            <i class="bi bi-box-arrow-right"></i> Logout
                        </button>
                    </form>
                </div>
            </div>

            {{-- MAIN CONTENT --}}
            <main class="col-md-10 col-lg-10 main-content">
                @yield('content')
            </main>
        </div>
    </div>

    {{-- JAVASCRIPTS: Bootstrap 4 bundle, jQuery, DataTables --}}
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap4.min.js"></script>

    {{-- Additional initialization for Bootstrap 4 dropdown/sidebar collapse compatibility --}}
    <script>
        // Ensure all dropdown toggles work perfectly and maintain active states
        $(document).ready(function() {
            // Sync active class with parent dropdown expanded state based on current URL
            var currentUrl = window.location.pathname;

            // For settings menu (or any collapse) - keep it expanded if subpage active
            if (currentUrl.includes('/admin/settings')) {
                $('#settingsMenu').addClass('show');
                $('.dropdown-toggle-link[href="#settingsMenu"]').attr('aria-expanded', 'true');
            }

            // Manually handle the collapse toggle icon rotation and active class
            $('.dropdown-toggle-link').on('click', function(e) {
                e.preventDefault();
                var target = $(this).attr('href');
                var $collapseEl = $(target);
                var isExpanded = $(this).attr('aria-expanded') === 'true';

                if ($collapseEl.length) {
                    $collapseEl.collapse('toggle');
                    $(this).attr('aria-expanded', !isExpanded);
                }

                // Update active class for parent dropdown when any child is active 
                // but also not removing when clicking toggle itself
                if (!isExpanded) {
                    $(this).addClass('active');
                } else {
                    // don't remove active from dropdown if settings route already active
                    if (!currentUrl.includes('/admin/settings')) {
                        $(this).removeClass('active');
                    }
                }
            });

            // When a submenu link is clicked, keep parent expanded and set active states
            $('.submenu .nav-link').on('click', function() {
                var parentCollapse = $(this).closest('.submenu');
                var toggleLink = parentCollapse.prev('.dropdown-toggle-link');
                if (toggleLink.length) {
                    toggleLink.addClass('active');
                    if (!parentCollapse.hasClass('show')) {
                        parentCollapse.collapse('show');
                        toggleLink.attr('aria-expanded', 'true');
                    }
                }
            });

            // For any regular nav link active highlight and smooth interaction
            $('.sidebar .nav-link:not(.dropdown-toggle-link)').on('click', function() {
                $('.sidebar .nav-link.active').not(this).removeClass('active');
                $(this).addClass('active');
            });

            // If any submenu link is active on load, mark parent active and expand
            if ($('.submenu .nav-link.active').length) {
                var activeSubLink = $('.submenu .nav-link.active');
                var parentCollapseDiv = activeSubLink.closest('.submenu');
                if (parentCollapseDiv.length) {
                    parentCollapseDiv.addClass('show');
                    var toggleBtn = parentCollapseDiv.prev('.dropdown-toggle-link');
                    toggleBtn.attr('aria-expanded', 'true');
                    toggleBtn.addClass('active');
                }
            }

            // fix for data tables responsive BS4 theme
            if ($.fn.dataTable) {
                $.fn.dataTable.defaults.lengthMenu = [
                    [10, 25, 50, -1],
                    [10, 25, 50, "All"]
                ];
            }
        });
    </script>

    @stack('scripts')

    {{-- Additional inline style to fix any dataTable Bootstrap 4 overlapping --}}
    <style>
        /* DataTables Bootstrap 4 integration fix */
        table.dataTable {
            margin-top: 12px !important;
            margin-bottom: 12px !important;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button.page-item.active .page-link {
            background-color: #3498db;
            border-color: #3498db;
        }

        .dataTables_wrapper .dataTables_filter input {
            border-radius: 20px;
            border: 1px solid #ced4da;
            padding: 0.375rem 0.75rem;
        }

        /* Card body spacing */
        .card-body {
            overflow-x: auto;
        }

        /* Adjust navbar toggler icons for consistent look */
        .sidebar .nav-link i.bi,
        .sidebar .dropdown-toggle-link i.bi {
            font-size: 1.1rem;
            vertical-align: middle;
        }

        /* minor fix for logout button */
        .btn-logout-sidebar i {
            margin-right: 8px;
        }

        /* Mobile responsiveness: ensure sidebar wraps */
        @media (max-width: 768px) {
            .sidebar {
                position: relative;
                width: 100%;
                min-height: auto;
            }

            .main-content {
                width: 100%;
                max-width: 100%;
                flex: 0 0 100%;
            }

            .logout-btn-wrapper {
                position: relative;
                margin-top: 25px;
                margin-bottom: 15px;
            }
        }

        /* Fix any broken spacing */
        .row.no-gutters {
            margin-right: 0;
            margin-left: 0;
        }

        .row.no-gutters>[class*="col-"] {
            padding-right: 0;
            padding-left: 0;
        }
    </style>
</body>

</html>
