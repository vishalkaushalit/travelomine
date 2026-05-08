<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Authorization Email | {{ $booking->bookingreference }}</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- CKEditor 5 CDN -->
    <script src="https://cdn.ckeditor.com/ckeditor5/34.0.0/classic/ckeditor.js"></script>
    <style>
        .ck-editor__editable { min-height: 500px; background-color: white !important; }
        .card-header { font-weight: bold; text-transform: uppercase; letter-spacing: 1px; }
        body { background-color: #f8f9fa; }
    </style>
</head>
<body>
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="text-primary">Authorize & Edit Booking Email</h2>
        <span class="badge bg-secondary">Ref: {{ $booking->bookingreference }}</span>
    </div>
    
    <div class="row">
        <!-- Card & Customer Information Sidebar -->
        <div class="col-md-3">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-dark text-white">Payment Info</div>
                <div class="card-body">
                    <p class="mb-1 text-muted small">Card Holder</p>
                    <h5 class="text-capitalize">
                        {{ $booking->primary_card->card_holder_name ?? 'N/A' }}
                    </h5>
                    <hr>
                    <p class="mb-1 text-muted small">Card Number</p>
                    <h6>**** **** **** {{ $booking->primary_card->card_last_four ?? 'N/A' }}</h6>
                    <hr>
                    <p class="mb-1 text-muted small">Total Amount</p>
                    <h5 class="text-success font-weight-bold">{{ $booking->currency }} {{ number_format($booking->amount_charged, 2) }}</h5>
                </div>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-header bg-info text-white">Customer Info</div>
                <div class="card-body">
                    <p class="mb-1 text-muted small">Name</p>
                    <h6 class="text-capitalize">{{ $booking->customer_name }}</h6>
                    <p class="mb-1 text-muted small">Email</p>
                    <h6 class="text-truncate">{{ $booking->customer_email }}</h6>
                    <p class="mb-1 text-muted small">Phone</p>
                    <h6 class="text-truncate">{{ $booking->customer_phone }}</h6>
                </div>
            </div>
        </div>

        <!-- Email Content Editor Area -->
        <div class="col-md-9">
            <form action="{{ route('charge.authorize.preview', $booking->id) }}" method="POST">
                @csrf
                <div class="card shadow-sm">
                    <div class="card-body">
                        <!-- Ensure $emailContent is exactly as named in the Controller -->
                        <textarea name="email_body" id="editor">{!! $emailContent !!}</textarea>
                        
                        <div class="mt-3 text-end">
                            <button type="submit" class="btn btn-success px-4">Preview & Proceed</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.ckeditor.com/ckeditor5/34.0.0/classic/ckeditor.js"></script>
<script>
    ClassicEditor.create(document.querySelector('#editor')).catch(e => console.error(e));
</script>
</body>
</html>
