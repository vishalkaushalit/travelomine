<!doctype html>
<html lang="en">

<head>
    <title>Title</title>
    <!-- Required meta tags -->
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Bootstrap CSS v5.2.1 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous" />
</head>
<style>
    :root {
        --airline-primary: #0a2351;
        /* Midnight Blue */
        --airline-gold: #c5a059;
        /* Elegant Gold */
        --glass-bg: rgba(255, 255, 255, 0.9);
    }

    body {
        font-family: 'Montserrat', sans-serif;
        background: linear-gradient(rgba(10, 35, 81, 0.8), rgba(10, 35, 81, 0.8)),
            url('https://images.unsplash.com/photo-1436491865332-7a61a109c0f2?auto=format&fit=crop&w=1920&q=80');
        background-size: cover;
        background-position: center;
        min-height: 100vh;
        display: flex;
        align-items: center;
    }

    .login-card {
        background: var(--glass-bg);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 20px;
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
        overflow: hidden;
        transition: transform 0.3s ease;
    }

    .login-header {
        background: var(--airline-primary);
        padding: 40px;
        text-align: center;
        color: white;
    }

    .login-header h2 {
        font-family: 'Playfair Display', serif;
        letter-spacing: 1px;
        margin-bottom: 5px;
    }

    .form-floating>.form-control:focus {
        border-color: var(--airline-gold);
        box-shadow: 0 0 0 0.25rem rgba(197, 160, 89, 0.25);
    }

    .btn-aviation {
        background: var(--airline-primary);
        color: white;
        border: none;
        padding: 12px;
        font-weight: 600;
        letter-spacing: 1px;
        text-transform: uppercase;
        transition: all 0.3s ease;
    }

    .btn-aviation:hover {
        background: var(--airline-gold);
        color: white;
        transform: translateY(-2px);
    }

    .input-group-text {
        background: transparent;
        border-right: none;
        color: var(--airline-primary);
    }

    .form-control {
        border-left: none;
    }
</style>

<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-6">
                <div class="login-card">
                    <div class="login-header">
                        <i class="bi bi-airplane-engines fs-1 mb-3"></i>
                        <h2>Agent Portal</h2>
                        <p class="small opacity-75">Exclusive Agent Ticketing Access</p>
                    </div>

                    <form method="POST" action="{{ route('agent.login') }}">
                        @csrf

                        <div class="mb-4">
                            <label class="form-label fw-semibold small text-muted">AGENT EMAIL</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                <input type="email" name="email" class="form-control form-control-lg"
                                    placeholder="name@agency.com" required autofocus>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold small text-muted">SECURE PASSWORD</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-shield-lock"></i></span>
                                <input type="password" name="password" class="form-control form-control-lg"
                                    placeholder="••••••••" required>
                            </div>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-aviation btn-lg shadow-sm">
                                Authenticate Session
                            </button>
                        </div>

                        <div class="text-center mt-4">
                            <a href="#" class="text-decoration-none small text-muted hover-gold">
                                Forgot Credentials? Contact Support
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JavaScript Libraries -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
        integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous">
        </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js"
        integrity="sha384-BBtl+eGJRgqQAUMxJ7pMwbEyER4l1g+O15P+16Ep7Q9Q+zqX6gSbd85u4mG4QzX+"
        crossorigin="anonymous"></script>
</body>
</html>