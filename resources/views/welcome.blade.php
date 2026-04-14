<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Flight CRM – Unified Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- Bootstrap 5 --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    {{-- Icons --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body {
            min-height: 100vh;
            background: radial-gradient(circle at top, #0d6efd 0, #031633 45%, #000814 100%);
            color: #f8f9fa;
            display: flex;
            flex-direction: column;
        }
        .hero-section {
            padding: 4rem 1.5rem 2rem;
        }
        .hero-badge {
            background: rgba(13, 110, 253, 0.15);
            border: 1px solid rgba(13, 110, 253, 0.4);
            border-radius: 999px;
            padding: .35rem .9rem;
            font-size: .8rem;
            text-transform: uppercase;
            letter-spacing: .08em;
        }
        .glass-card {
            background: rgba(15, 23, 42, 0.85);
            border-radius: 1.2rem;
            border: 1px solid rgba(148, 163, 184, .25);
            box-shadow: 0 24px 80px rgba(15, 23, 42, 0.85);
            backdrop-filter: blur(16px);
        }
        .role-card {
            border-radius: 1rem;
            border: 1px solid rgba(148, 163, 184, 0.25);
            background: rgba(15, 23, 42, 0.9);
            transition: all .2s ease;
            cursor: pointer;
        }
        .role-card:hover {
            transform: translateY(-4px);
            border-color: #0d6efd;
            box-shadow: 0 16px 40px rgba(13, 110, 253, 0.35);
        }
        .role-icon {
            width: 44px;
            height: 44px;
            border-radius: 999px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
        }
        .orbit {
            position: relative;
            width: 260px;
            height: 260px;
            border-radius: 50%;
            border: 1px dashed rgba(148, 163, 184, 0.4);
        }
        .orbit-dot {
            position: absolute;
            width: 10px;
            height: 10px;
            border-radius: 999px;
            background: #0d6efd;
        }
        .orbit-dot:nth-child(1) { top: 7%; left: 50%; transform: translateX(-50%); }
        .orbit-dot:nth-child(2) { right: 7%; top: 50%; transform: translateY(-50%); }
        .orbit-dot:nth-child(3) { bottom: 7%; left: 50%; transform: translateX(-50%); }
        .orbit-dot:nth-child(4) { left: 7%; top: 50%; transform: translateY(-50%); }
        footer {
            margin-top: auto;
            padding: 1.25rem 0;
            font-size: .85rem;
            color: #9ca3af;
        }
        @media (max-width: 767.98px) {
            .hero-section { padding-top: 3rem; }
            .glass-card { margin-top: 1.5rem; }
        }
    </style>
</head>
<body>
    {{-- Top navigation --}}
    <nav class="navbar navbar-dark navbar-expand-lg border-bottom border-secondary border-opacity-25">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2" href="{{ url('/') }}">
                <i class="bi bi-airplane-fill text-primary"></i>
                <span class="fw-semibold">Flight CRM</span>
            </a>
            <div class="d-flex align-items-center gap-2">
                @if(Auth::check())
                    <span class="small text-light me-2 d-none d-sm-inline">
                        Signed in as <strong>{{ Auth::user()->name }}</strong>
                    </span>
                    <a href="{{ route('agent.dashboard') }}" class="btn btn-sm btn-outline-light">
                        Go to Dashboard
                    </a>
                @else
                    <span class="text-secondary small me-2 d-none d-sm-inline">Already have access?</span>
                    <a href="{{ route('agent.login') }}" class="btn btn-sm btn-outline-light">
                        Agent Login
                    </a>
                @endif
            </div>
        </div>
    </nav>

    {{-- Hero + Role selection --}}
    <main class="hero-section">
        <div class="container">
            <div class="row g-4 align-items-center">
                {{-- Left: marketing copy --}}
                <div class="col-lg-6">
                    <div class="mb-3 hero-badge d-inline-flex align-items-center gap-2">
                        <i class="bi bi-speedometer2 text-primary"></i>
                        <span>Margin Cut-Off & Flight Sales CRM</span>
                    </div>

                    <h1 class="display-5 fw-semibold mt-3">
                        One cockpit for your<br>
                        <span class="text-primary">agents, managers & MIS</span>
                    </h1>

                    <p class="mt-3 text-secondary">
                        Centralize margin cut-off (MCO) records, track agent performance,
                        resolve tickets faster, and keep every team aligned on every booking.
                    </p>

                    <ul class="list-unstyled text-secondary mt-3 mb-4">
                        <li class="mb-2">
                            <i class="bi bi-check-circle-fill text-success me-2"></i>
                            Real-time MCO tracking for all agents
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-check-circle-fill text-success me-2"></i>
                            Role-based dashboards for Admin, Manager, Agent, MIS
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-check-circle-fill text-success me-2"></i>
                            Built for high-volume flight booking operations
                        </li>
                    </ul>

                    <div class="d-flex flex-wrap gap-2">
                        <a href="#role-login" class="btn btn-primary btn-lg">
                            <i class="bi bi-box-arrow-in-right me-1"></i>
                            Choose your panel
                        </a>
                        <button type="button" class="btn btn-outline-light btn-lg">
                            <i class="bi bi-bar-chart-line me-1"></i>
                            View KPIs (Admin)
                        </button>
                    </div>
                </div>

                {{-- Right: glass login selector --}}
                <div class="col-lg-6">
                    <div class="glass-card p-4 p-md-5" id="role-login">
                        <div class="row g-4">
                            <div class="col-md-6 d-flex flex-column gap-3">
                                <h5 class="text-uppercase text-secondary fw-semibold small mb-2">
                                    Sign in to your workspace
                                </h5>

                                {{-- Admin --}}
                                <a href="{{ route('admin.login') }}" class="text-decoration-none text-light">
                                    <div class="role-card p-3 h-100">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="role-icon bg-primary bg-opacity-10 text-primary border border-primary border-opacity-50">
                                                <i class="bi bi-shield-lock"></i>
                                            </div>
                                            <div>
                                                <div class="fw-semibold">Admin Panel</div>
                                                <small class="text-secondary">
                                                    User management, MCO oversight, system settings.
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                </a>

                                {{-- Manager (future URL placeholder) --}}
                                <a href="{{ route('charge.login') }}" class="text-decoration-none text-light">
                                    <div class="role-card p-3 h-100">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="role-icon bg-warning bg-opacity-10 text-warning border border-warning border-opacity-50">
                                                <i class="bi bi-people-fill"></i>
                                            </div>
                                            <div>
                                                <div class="fw-semibold">Charging Panel</div>
                                                <small class="text-secondary">
                                                    Team performance, sales tracking, escalations.
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                </a>

                                {{-- MIS placeholder --}}
                                <a href="{{ route('mis.login') }}" class="text-decoration-none text-light">
                                    <div class="role-card p-3 h-100">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="role-icon bg-danger bg-opacity-10 text-danger border border-danger border-opacity-50">
                                                <i class="bi bi-cash-stack"></i>
                                            </div>
                                            <div>
                                                <div class="fw-semibold">MIS</div>
                                                <small class="text-secondary">
                                                    Manage bookingn
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                                
                            </div>

                            <div class="col-md-6 d-flex flex-column gap-3">
                                {{-- Agent --}}
                                <a href="{{ route('agent.login') }}" class="text-decoration-none text-light">
                                    <div class="role-card p-3 h-100">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="role-icon bg-success bg-opacity-10 text-success border border-success border-opacity-50">
                                                <i class="bi bi-person-fill-check"></i>
                                            </div>
                                            <div>
                                                <div class="fw-semibold">Agent Panel</div>
                                                <small class="text-secondary">
                                                    Create MCO records, track commissions & activity.
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                </a>

                                {{-- Chargeback / Finance placeholder --}}
                                <a href="{{ route('support.login') }}" class="text-decoration-none text-light">
                                    <div class="role-card p-3 h-100">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="role-icon bg-danger bg-opacity-10 text-danger border border-danger border-opacity-50">
                                                <i class="bi bi-cash-stack"></i>
                                            </div>
                                            <div>
                                                <div class="fw-semibold">Chargeback / Finance</div>
                                                <small class="text-secondary">
                                                    Future module for disputes, refunds & audits.
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                </a>

                                {{-- Visual orbit element --}}
                                <div class="d-flex justify-content-center align-items-center mt-2">
                                    <div class="orbit">
                                        <div class="orbit-dot"></div>
                                        <div class="orbit-dot"></div>
                                        <div class="orbit-dot"></div>
                                        <div class="orbit-dot"></div>
                                        <div class="position-absolute top-50 start-50 translate-middle text-center">
                                            <i class="bi bi-airplane-engines text-primary fs-3 mb-1"></i>
                                            <div class="small text-secondary">
                                                All teams orbit around<br>the same booking data.
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div> {{-- row --}}
                    </div>
                </div>
            </div>
        </div>
    </main>

    {{-- Footer --}}
    <footer>
        <div class="container d-flex flex-column flex-md-row justify-content-between align-items-center gap-2">
            <span>© {{ date('Y') }} Flight CRM. All rights reserved.</span>
            <span class="text-secondary">
                Built for high-volume US & EU flight operations.
            </span>
        </div>
    </footer>

    {{-- Scripts --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>