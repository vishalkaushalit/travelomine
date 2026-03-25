<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Payment Link – Charge Booking #{{ $booking->id }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light py-4">
<div class="container">
    <div class="row">
        <div class="col-12 mb-3">
            <a href="{{ route('charge.bookings.show', $booking->id) }}" class="btn btn-sm btn-secondary">
                ← Back to Booking
            </a>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-9">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Create Payment Link for Booking #{{ $booking->id }}</h5>
                </div>
                <div class="card-body">

                    {{-- Errors --}}
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $err)
                                    <li>{{ $err }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {{-- Booking summary --}}
                    <div class="mb-4">
                        <h6 class="mb-2">Booking Summary</h6>
                        <div class="border rounded p-3 bg-light">
                            <p class="mb-1"><strong>Customer:</strong> {{ $booking->customer_name }}</p>
                            <p class="mb-1"><strong>Customer Email:</strong> {{ $booking->customer_email }}</p>
                            <p class="mb-1"><strong>Billing Phone:</strong> {{ $booking->billing_phone }}</p>
                            <p class="mb-1"><strong>Billing Address:</strong> {{ $booking->billing_address }}</p>
                            <p class="mb-0"><strong>Total Amount to pay:</strong> {{ number_format($booking->total_mco, 2) }} USD</p>
                            <p class="mb-0"><strong>Merchant Name:</strong> {{ $booking->agency_merchant_name}}</p>
                        </div>
                    </div>

                    {{-- Create payment link form --}}
                    <form method="POST"
                          action="{{ route('charge.bookings.payment-link.store', $booking->id) }}">
                        @csrf

                        {{-- Amount (can override total_mco) --}}
                        <div class="mb-3">
                            <label class="form-label">Amount to Charge (USD)</label>
                            <input type="number" step="0.01" name="amount"
                                   class="form-control"
                                   value="{{ old('amount', $defaultAmount) }}" required>
                            <small class="text-muted">
                                Default from booking total_mco: {{ number_format($defaultAmount, 2) }} USD
                            </small>
                        </div>

                        {{-- Internal notes (optional) --}}
                        <div class="mb-3">
                            <label class="form-label">Internal Notes (optional)</label>
                            <textarea name="notes" class="form-control" rows="2">{{ old('notes') }}</textarea>
                            <small class="text-muted">Visible only to charge team, not in customer email.</small>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            Generate Payment Link
                        </button>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
