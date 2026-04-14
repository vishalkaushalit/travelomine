<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payment Link Expired</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light py-5">
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm text-center">
                <div class="card-body py-5">
                    <h1 class="display-4 mb-3">⏰</h1>
                    <h3>Payment Link Expired</h3>
                    <p class="mt-3">
                        This payment link for booking
                        <strong>#{{ $link->booking_id }}</strong> has expired.
                    </p>
                    <p class="text-muted small mb-0">
                        Please contact our team to receive a new payment link or alternative payment instructions.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
