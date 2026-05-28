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
            transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
        }
        
        .collect-container:focus-within {
            border-color: #86b7fe;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
        }
        
        .collect-container iframe {
            width: 100% !important;
            min-height: 24px !important;
            border: none !important;
        }
        
        .payment-summary p {
            margin-bottom: 0.4rem;
        }
        
        .card-field-error {
            border-color: #dc3545;
        }
        
        .error-message {
            color: #dc3545;
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }
    </style>
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
                        <!-- Debug info - remove in production -->
                        <div class="alert alert-info small mb-3">
                            <strong>Debug Info:</strong><br>
                            link merchant id: {{ $link->merchant->id ?? 'null' }}<br>
                            link tokenization key: {{ $link->merchant->tokenization_key ?? 'null' }}<br>
                            booking merchant id: {{ $booking->agencyMerchant->id ?? 'null' }}<br>
                            booking tokenization key: {{ $booking->agencyMerchant->tokenization_key ?? 'null' }}<br>
                            selected merchant id: {{ $merchant->id ?? 'null' }}<br>
                            selected tokenization key: {{ $merchant->tokenization_key ?? 'null' }}
                        </div>
                        
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

                        @php
                            $merchant = $booking->agencyMerchant ?? ($link->merchant ?? null);
                        @endphp

                        @if (!$merchant)
                            <div class="alert alert-danger mb-0">
                                Merchant configuration not found for this booking.
                            </div>
                        @elseif(empty($merchant->tokenization_key))
                            <div class="alert alert-danger mb-0">
                                Merchant tokenization key is missing. Please contact support.
                            </div>
                        @else
                            <form id="payment-form" method="POST" action="{{ route('public.pay.process', $link->token) }}">
                                @csrf
                                <input type="hidden" name="payment_token" id="payment_token">
                                <input type="hidden" name="cardholder_name" id="cardholder_name">

                                <div class="mb-3">
                                    <label class="form-label">Cardholder Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="cardholder_name_input" 
                                           placeholder="Name on card" required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Card Number <span class="text-danger">*</span></label>
                                    <div id="ccnumber" class="collect-container"></div>
                                    <div id="ccnumber-error" class="error-message"></div>
                                </div>

                                <div class="row">
                                    <div class="mb-3 col-md-6">
                                        <label class="form-label">Expiry Date <span class="text-danger">*</span></label>
                                        <div id="ccexp" class="collect-container"></div>
                                        <div id="ccexp-error" class="error-message"></div>
                                    </div>
                                    <div class="mb-3 col-md-6">
                                        <label class="form-label">CVV/CVC <span class="text-danger">*</span></label>
                                        <div id="cvv" class="collect-container"></div>
                                        <div id="cvv-error" class="error-message"></div>
                                    </div>
                                </div>

                                <button type="submit" id="pay-button" class="btn btn-success w-100 mt-2">
                                    Pay ${{ number_format($link->amount, 2) }} {{ $link->currency }}
                                </button>
                            </form>

                            <p class="text-muted small mt-3 mb-0">
                                <i class="bi bi-lock"></i> Your payment details are collected securely via SSL encryption.
                            </p>
                        @endif

                    </div>
                </div>

            </div>
        </div>
    </div>

    @if ($merchant && !empty($merchant->tokenization_key))
        <!-- Load Collect.js after DOM elements exist -->
        <script src="https://macpayments.transactiongateway.com/token/Collect.js" 
                data-tokenization-key="{{ $merchant->tokenization_key }}"
                async></script>
        
        <script>
            // Global flag to track Collect.js readiness
            window.CollectJSReady = false;
            window.CollectJSFailed = false;
            
            // Set a timeout to detect if Collect.js loads properly
            setTimeout(function() {
                if (!window.CollectJSReady && !window.CollectJSFailed) {
                    document.getElementById('pay-button').disabled = false;
                    alert('Payment form is taking longer than expected. Please refresh the page and try again.');
                }
            }, 10000);
            
            // Main initialization when Collect.js is ready
            function setupCollectJS() {
                if (typeof CollectJS === 'undefined') {
                    console.error('CollectJS not defined');
                    window.CollectJSFailed = true;
                    document.getElementById('pay-button').disabled = false;
                    alert('Payment system failed to load. Please refresh the page and try again.');
                    return;
                }
                
                window.CollectJSReady = true;
                console.log('CollectJS loaded successfully');
                
                // Get references to DOM elements
                const payButton = document.getElementById('pay-button');
                const form = document.getElementById('payment-form');
                const paymentTokenInput = document.getElementById('payment_token');
                const cardholderNameInput = document.getElementById('cardholder_name_input');
                
                // Configure Collect.js
                CollectJS.configure({
                    variant: 'inline',
                    style: {
                        input: {
                            'font-size': '14px',
                            'font-family': 'system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif',
                            'color': '#212529'
                        }
                    },
                    fields: {
                        ccnumber: {
                            selector: '#ccnumber',
                            placeholder: '1234 5678 9012 3456'
                        },
                        ccexp: {
                            selector: '#ccexp',
                            placeholder: 'MM / YY'
                        },
                        cvv: {
                            selector: '#cvv',
                            placeholder: '123'
                        }
                    },
                    callback: function(response) {
                        console.log('Collect.js callback received:', response);
                        
                        if (response.token) {
                            // Token received successfully
                            paymentTokenInput.value = response.token;
                            
                            // Set cardholder name
                            if (cardholderNameInput) {
                                document.getElementById('cardholder_name').value = cardholderNameInput.value;
                            }
                            
                            // Submit the form
                            form.submit();
                        } else if (response.error) {
                            // Handle errors
                            console.error('Tokenization error:', response.error);
                            
                            // Clear any previous error messages
                            document.querySelectorAll('.error-message').forEach(el => el.textContent = '');
                            document.querySelectorAll('.collect-container').forEach(el => el.classList.remove('card-field-error'));
                            
                            if (response.error.field) {
                                const field = response.error.field;
                                const errorDiv = document.getElementById(`${field}-error`);
                                const container = document.getElementById(field);
                                
                                if (errorDiv) {
                                    errorDiv.textContent = response.error.message || 'Invalid card details';
                                }
                                if (container) {
                                    container.classList.add('card-field-error');
                                }
                            } else {
                                alert(response.error.message || 'Unable to process card details. Please check your information and try again.');
                            }
                            
                            // Re-enable the button
                            payButton.disabled = false;
                            payButton.innerHTML = 'Pay ${{ number_format($link->amount, 2) }} {{ $link->currency }}';
                        } else {
                            alert('Unable to tokenize card details. Please try again.');
                            payButton.disabled = false;
                            payButton.innerHTML = 'Pay ${{ number_format($link->amount, 2) }} {{ $link->currency }}';
                        }
                    },
                    error: function(error) {
                        console.error('Collect.js error:', error);
                        alert('Payment system error. Please refresh and try again.');
                        payButton.disabled = false;
                        payButton.innerHTML = 'Pay ${{ number_format($link->amount, 2) }} {{ $link->currency }}';
                    }
                });
                
                // Handle form submission
                if (form) {
                    form.addEventListener('submit', function(e) {
                        e.preventDefault();
                        
                        // Clear previous errors
                        document.querySelectorAll('.error-message').forEach(el => el.textContent = '');
                        document.querySelectorAll('.collect-container').forEach(el => el.classList.remove('card-field-error'));
                        
                        // Validate cardholder name
                        if (!cardholderNameInput || !cardholderNameInput.value.trim()) {
                            alert('Please enter the cardholder name');
                            cardholderNameInput?.focus();
                            return;
                        }
                        
                        // Disable button and show loading state
                        payButton.disabled = true;
                        payButton.innerHTML = 'Processing...';
                        
                        try {
                            // Start the payment tokenization request
                            CollectJS.startPaymentRequest();
                        } catch (error) {
                            console.error('Failed to start payment request:', error);
                            alert('Unable to start payment process. Please refresh and try again.');
                            payButton.disabled = false;
                            payButton.innerHTML = 'Pay ${{ number_format($link->amount, 2) }} {{ $link->currency }}';
                        }
                    });
                }
            }
            
            // Check if Collect.js is already loaded
            if (typeof CollectJS !== 'undefined' && CollectJS.configure) {
                setupCollectJS();
            } else {
                // Wait for Collect.js to load
                window.addEventListener('load', function() {
                    if (typeof CollectJS !== 'undefined' && CollectJS.configure) {
                        setupCollectJS();
                    } else {
                        // Poll for CollectJS
                        let attempts = 0;
                        const checkCollectJS = setInterval(function() {
                            attempts++;
                            if (typeof CollectJS !== 'undefined' && CollectJS.configure) {
                                clearInterval(checkCollectJS);
                                setupCollectJS();
                            } else if (attempts > 50) { // 5 seconds max
                                clearInterval(checkCollectJS);
                                window.CollectJSFailed = true;
                                console.error('CollectJS failed to load after multiple attempts');
                                const payButton = document.getElementById('pay-button');
                                if (payButton) {
                                    payButton.disabled = false;
                                    alert('Payment form failed to load. Please check your internet connection and refresh the page.');
                                }
                            }
                        }, 100);
                    }
                });
            }
        </script>
    @endif
</body>

</html>