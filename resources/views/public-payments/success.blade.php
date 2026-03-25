<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payment Successful</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light py-5">
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm text-center">
                <div class="card-body py-5">
                    <h1 class="display-4 mb-3">✅</h1>
                    <h3>Payment Successful</h3>
                    <p class="mt-3">
                        Thank you, {{ $link->customer_name }}.<br>
                        Your payment of <strong>${{ number_format($link->amount, 2) }} {{ $link->currency }}</strong>
                        for booking #{{ $link->booking_id }} has been processed.
                    </p>
                    <p class="text-muted small mb-0">
                        If you have any questions, please contact our support team with your booking ID.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
