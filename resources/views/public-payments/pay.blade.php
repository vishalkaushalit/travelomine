<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Secure Payment – Booking #{{ $booking->id }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light py-5">
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-8">

            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Travel Services Fee</h5>
                </div>
                <div class="card-body">
                    <p class="mb-1"><strong>Booking ID:</strong> #{{ $booking->id }}</p>
                    <p class="mb-1"><strong>Customer Name:</strong> {{ $link->customer_name }}</p>
                    <p class="mb-1"><strong>Billing Email:</strong> {{ $link->billing_email ?? 'N/A' }}</p>
                    <p class="mb-1"><strong>Billing Phone:</strong> {{ $link->billing_phone ?? 'N/A' }}</p>
                    <p class="mb-2"><strong>Billing Address:</strong> {{ $link->billing_address ?? 'N/A' }}</p>
                    <hr>

                    {{-- You can customise this with your actual booking fields --}}
                    <p class="mb-1"><strong>Service:</strong> {{ $booking->serviceprovided ?? 'Flight' }}</p>

                    {{-- Example of segments if you have a relation --}}
                    @if(method_exists($booking, 'segments') && $booking->segments->count())
                        <p class="mb-2"><strong>Route:</strong></p>
                        <ul class="mb-0">
                            @foreach ($booking->segments as $segment)
                                <li> 
                                    {{ $segment->from_city }} → {{ $segment->to_city }}
                                    on {{ \Carbon\Carbon::parse($segment->departure_date)->format('d M Y') }}
                                    ({{ $segment->cabin_class }})
                                </li>
                            @endforeach
                        </ul>
                    @endif

                    {{-- Amount --}}
                    <div class="mt-3">
                        <h5>Amount Due: ${{ number_format($link->amount, 2) }} {{ $link->currency }}</h5>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="mb-0">Secure Payment</h5>
                </div>
                <div class="card-body">

                    @if (session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $err)
                                    <li>{{ $err }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('public.pay.process', $link->token) }}">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label">Card Number <span class="text-danger">*</span></label>
                            <input type="text" name="ccnumber" class="form-control"
                                   placeholder="4111111111111111" required>
                        </div>

                        <div class="row">
                            <div class="mb-3 col-6">
                                <label class="form-label">Expiry (MMYY) <span class="text-danger">*</span></label>
                                <input type="text" name="ccexp" class="form-control"
                                       placeholder="1227" required>
                            </div>
                            <div class="mb-3 col-6">
                                <label class="form-label">CVV <span class="text-danger">*</span></label>
                                <input type="text" name="cvv" class="form-control"
                                       placeholder="123" required>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-success w-100 mt-2">
                            Pay ${{ number_format($link->amount, 2) }} {{ $link->currency }}
                        </button>
                    </form>

                    <p class="text-muted small mt-3 mb-0">
                        Your payment will be processed securely. We do not store your full card number or CVV.
                    </p>
                </div>
            </div>

        </div>
    </div>
</div>
</body>
</html>
