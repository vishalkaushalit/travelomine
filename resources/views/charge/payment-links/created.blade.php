<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payment Link Created – Booking #{{ $booking->id }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light py-4">
<div class="container">
    <div class="row mb-3">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <a href="{{ route('charge.bookings.show', $booking->id) }}" class="btn btn-sm btn-secondary">
                ← Back to Booking
            </a>
            <span class="text-muted">Booking #{{ $booking->id }}</span>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">✅ Payment Link Created</h5>
                </div>
                <div class="card-body">

                    {{-- Flash messages --}}
                    @if (session('mail_sent'))
                        <div class="alert alert-success">
                            {{ session('mail_sent') }}
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    {{-- Summary --}}
                    <p><strong>Customer:</strong> {{ $link->customer_name }}</p>
                    <p><strong>Booking ID:</strong> #{{ $booking->id }}</p>
                    <p><strong>Amount:</strong> ${{ number_format($link->amount, 2) }} {{ $link->currency }}</p>
                    <p><strong>Merchant:</strong> {{ $merchant->name ?? 'N/A' }}</p>
                    <p><strong>Expires:</strong>
                        {{ $link->expires_at?->format('d M Y, h:i A') ?? 'Never' }}
                    </p>

                    @if ($link->billing_email)
                        <p><strong>Billing Email:</strong> {{ $link->billing_email }}</p>
                    @endif

                    {{-- Payment URL --}}
                    <div class="mb-4">
                        <label class="form-label fw-bold">Payment URL (send this to the customer):</label>
                        <div class="input-group">
                            <input type="text" id="paymentUrl" class="form-control"
                                   value="{{ $paymentUrl }}" readonly>
                            <button type="button" class="btn btn-outline-secondary" onclick="copyUrl()">
                                Copy
                            </button>
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="d-flex flex-wrap gap-2">
                        {{-- Optional: send email via dedicated route (you'll add controller+route) --}}
                        @if ($link->billing_email)
                            <form method="POST"
                                  action="{{ route('charge.bookings.payment-link.send-mail', [$booking->id, $link->id]) }}">
                                @csrf
                                <button type="submit" class="btn btn-success">
                                    ✉️ Send Link via Email
                                </button>
                            </form>
                        @else
                            <button type="button" class="btn btn-success" disabled
                                    title="No billing email on this link">
                                ✉️ Send Link via Email
                            </button>
                        @endif

                        <a href="{{ route('charge.bookings.payment-link.create', $booking->id) }}"
                           class="btn btn-primary">
                            ➕ Create Another Payment Link
                        </a>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function copyUrl() {
        const input = document.getElementById('paymentUrl');
        input.select();
        input.setSelectionRange(0, 99999);
        document.execCommand('copy');
        alert('Payment URL copied to clipboard!');
    }
</script>
</body>
</html>
