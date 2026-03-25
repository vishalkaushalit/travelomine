<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payment Already Completed</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light py-5">
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm text-center">
                <div class="card-body py-5">
                    <h1 class="display-4 mb-3">ℹ️</h1>
                    <h3>Payment Already Completed</h3>
                    <p class="mt-3">
                        Our records show that this payment link for booking
                        <strong>#{{ $link->booking_id }}</strong> was already completed
                        on {{ $link->paid_at?->format('d M Y, h:i A') ?? 'a previous date' }}.
                    </p>
                    <p class="text-muted small mb-0">
                        If you believe this is an error, please contact support with your booking ID.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
