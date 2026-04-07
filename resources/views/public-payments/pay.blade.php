<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Secure Payment – Booking #{{ $booking->id }}</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        .collect-container {
            border: 1px solid #ced4da;
            border-radius: 0.375rem;
            background: #fff;
            min-height: 38px;
            padding: 0.375rem 0.75rem;
            display: flex;
            align-items: center;
        }

        .collect-container iframe {
            width: 100% !important;
            min-height: 24px !important;
        }

        .payment-summary p {
            margin-bottom: 0.4rem;
        }
    </style>

    @php
    $merchant = $booking->agencyMerchant ?? $link->merchant ?? null;
    @endphp

    @if (!$merchant || empty($merchant->tokenization_key))
        <script>
            window.collectJsConfigError = "Missing merchant tokenization key.";
        </script>
    @else
        <script src="https://macpayments.transactiongateway.com/token/Collect.js"
            data-tokenization-key="{{ $merchant->tokenization_key }}"></script>
    @endif
</head>

<body class="bg-light py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">

                <div class="card shadow-sm mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Travel Services Fee</h5>
                    </div>
                    <div class="card-body payment-summary">
                        <p><strong>Booking ID:</strong> #{{ $booking->id }}</p>
                        <p><strong>Customer Name:</strong> {{ $link->customer_name }}</p>
                        <p><strong>Billing Email:</strong> {{ $link->billing_email ?? 'N/A' }}</p>
                        <p><strong>Billing Phone:</strong> {{ $link->billing_phone ?? 'N/A' }}</p>
                        <p><strong>Billing Address:</strong> {{ $link->billing_address ?? 'N/A' }}</p>
                        <hr>

                        <p><strong>Service:</strong> {{ $booking->serviceprovided ?? 'Flight' }}</p>

                        @if (method_exists($booking, 'segments') && $booking->segments->count())
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
                        <pre>
link merchant id: {{ $link->merchant->id ?? 'null' }}
link tokenization key: {{ $link->merchant->tokenization_key ?? 'null' }}
booking merchant id: {{ $booking->agencyMerchant->id ?? 'null' }}
booking tokenization key: {{ $booking->agencyMerchant->tokenization_key ?? 'null' }}
selected merchant id: {{ $merchant->id ?? 'null' }}
selected tokenization key: {{ $merchant->tokenization_key ?? 'null' }}
</pre>
                        @if (session('error'))
                            <div class="alert alert-danger">
                                {{ session('error') }}
                            </div>
                        @endif

                        @if (session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
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

                        @if (!$merchant)
                            <div class="alert alert-danger mb-0">
                                Merchant configuration not found for this booking.
                            </div>
                        @elseif(empty($merchant->tokenization_key))
                            <div class="alert alert-danger mb-0">
                                Merchant tokenization key is missing. Please contact support.
                            </div>
                        @else
                            <form id="payment-form" method="POST"
                                action="{{ route('public.pay.process', $link->token) }}">
                                @csrf

                                <input type="hidden" name="payment_token" id="payment_token">

                                <div class="mb-3">
                                    <label class="form-label">Card Number <span class="text-danger">*</span></label>
                                    <div id="ccnumber" class="collect-container"></div>
                                </div>

                                <div class="row">
                                    <div class="mb-3 col-md-6">
                                        <label class="form-label">Expiry <span class="text-danger">*</span></label>
                                        <div id="ccexp" class="collect-container"></div>
                                    </div>
                                    <div class="mb-3 col-md-6">
                                        <label class="form-label">CVV <span class="text-danger">*</span></label>
                                        <div id="cvv" class="collect-container"></div>
                                    </div>
                                </div>

                                <button type="button" id="pay-button" class="btn btn-success w-100 mt-2">
                                    Pay ${{ number_format($link->amount, 2) }} {{ $link->currency }}
                                </button>
                            </form>

                            <p class="text-muted small mt-3 mb-0">
                                Your payment details are collected securely.
                            </p>
                        @endif

                    </div>
                </div>

            </div>
        </div>
    </div>

    @if ($merchant && !empty($merchant->tokenization_key))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const form = document.getElementById('payment-form');
                const payButton = document.getElementById('pay-button');
                const paymentTokenInput = document.getElementById('payment_token');

                if (window.collectJsConfigError) {
                    alert(window.collectJsConfigError);
                    return;
                }

                if (typeof CollectJS === 'undefined') {
                    alert('Payment form failed to load. Please refresh and try again.');
                    return;
                }

                CollectJS.configure({
                    variant: 'inline',
                    fields: {
                        ccnumber: {
                            selector: '#ccnumber',
                            placeholder: 'Card Number'
                        },
                        ccexp: {
                            selector: '#ccexp',
                            placeholder: 'MM / YY'
                        },
                        cvv: {
                            selector: '#cvv',
                            placeholder: 'CVV'
                        }
                    },
                    callback: function(response) {
                        if (response.token) {
                            paymentTokenInput.value = response.token;
                            form.submit();
                        } else {
                            alert('Unable to tokenize card details. Please try again.');
                            payButton.disabled = false;
                            payButton.innerText =
                                'Pay ${{ number_format($link->amount, 2) }} {{ $link->currency }}';
                        }
                    }
                });

                payButton.addEventListener('click', function(e) {
                    e.preventDefault();

                    payButton.disabled = true;
                    payButton.innerText = 'Processing...';

                    try {
                        CollectJS.startPaymentRequest();
                    } catch (error) {
                        alert('Payment request failed to start. Please refresh and try again.');
                        payButton.disabled = false;
                        payButton.innerText =
                            'Pay ${{ number_format($link->amount, 2) }} {{ $link->currency }}';
                    }
                });
            });
        </script>
    @endif
</body>

</html>
