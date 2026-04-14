<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: 'Arial', sans-serif; line-height: 1.5; color: #333; margin: 0; padding: 20px; background: #f4f4f4; }
        .wrapper { max-width: 800px; margin: auto; background: #fff; padding: 40px; border-radius: 5px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .header-ref { font-size: 20px; font-weight: bold; color: #1a237e; border-bottom: 2px solid #1a237e; padding-bottom: 10px; margin-bottom: 20px; }
        .auth-section { background: #fff9e6; border: 1px solid #ffe082; padding: 20px; border-radius: 5px; margin: 20px 0; }
        .table-custom { width: 100%; border-collapse: collapse; margin-top: 15px; }
        .table-custom th { background: #1a237e; color: white; padding: 10px; text-align: left; font-size: 13px; }
        .table-custom td { padding: 10px; border-bottom: 1px solid #eee; font-size: 13px; }
        .legal-terms { font-size: 11px; color: #777; margin-top: 40px; border-top: 1px solid #ddd; padding-top: 20px; text-align: justify; }
        .premium-itinerary { border: 1px solid #1a237e; border-radius: 8px; margin: 20px 0; overflow: hidden; }
        .itinerary-title { background: #1a237e; color: white; padding: 8px 15px; font-weight: bold; }
    </style>
</head>
<body>
<div class="wrapper">
    <div class="header-ref">
        {{ $booking->booking_reference }} - New Booking Confirmation
    </div>

    <p>Dear <strong>{{ $booking->customer_name }}</strong>,</p>
    <p>Greetings of the day !!<br>
    As per our conversation and as agreed, we have booked your flight reservation under Confirmation number <strong>{{ $booking->booking_reference }}</strong>. 
    Total Cost for all passengers - <strong>{{ $booking->currency }} {{ number_format($booking->amount_charged, 2) }}</strong> (Including all taxes and fees).</p>

    <div class="auth-section">
        <p>As per our telephonic conversation I, <strong>{{ $booking->customer_name }}</strong>, authorized Travelomile to process the above-mentioned charges under their respective merchants for charging my <strong>{{ $booking->card_last_four }}</strong> card for the amount of <strong>{{ $booking->currency }} {{ number_format($booking->amount_charged, 2) }}</strong> for booking a New Flight Reservation.</p>
        <p style="font-size: 12px;">This payment authorization is for the amount indicated above and is valid for one-time use only. I certify that I, {{ $booking->customer_name }}, am an authorized user of this card and that I will not dispute the payment with my bank.</p>
    </div>

    <h4>Charges Description:</h4>
    <p>Charge : {{ $booking->currency }} {{ number_format($booking->amount_paid_airline, 2) }} </p>
    <p>Charge : {{ $booking->currency }} {{ number_format($booking->total_mco, 2) }} - (Agency Service Fee, includes all taxes)</p>

    <h4>Passenger Details:</h4>
    <table class="table-custom">
        <thead>
            <tr><th>S.No.</th><th>Type</th><th>First Name</th><th>Last Name</th><th>Gender</th><th>DOB</th><th>Price</th></tr>
        </thead>
        <tbody>
            @foreach($booking->passengers as $index => $p)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $p->passenger_type }}</td>
                <td>{{ $p->first_name }}</td>
                <td>{{ $p->last_name }}</td>
                <td>{{ ucfirst($p->gender) }}</td>
                <td>{{ \Carbon\Carbon::parse($p->dob)->format('M-d-Y') }}</td>
                <td>{{ number_format($booking->amount_charged / count($booking->passengers), 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="premium-itinerary">
        <div class="itinerary-title">FLIGHT ITINERARY</div>
        @foreach($booking->segments as $segment)
        <div style="padding: 15px; border-bottom: 1px solid #eee;">
            <strong>{{ $segment->airline_name }} ({{ $segment->flight_number }})</strong><br>
            {{ $segment->from_city }} ({{ $segment->from_airport }}) &rarr; {{ $segment->to_city }} ({{ $segment->to_airport }})<br>
            Date: {{ \Carbon\Carbon::parse($segment->departure_date)->format('D, d M Y') }}
        </div>
        @endforeach
    </div>

    <h4>Payment Summary:</h4>
    <p>Card Holder: {{ $booking->cards->first()->card_holder_name ?? 'N/A' }}<br>
    Card Number: {{ $booking->card_last_four }}<br>
    Billing Address: {{ $booking->billing_address }}<br>
    Email: {{ $booking->customer_email }}</p>

    <div class="legal-terms">
        <strong>Terms and Conditions:</strong><br>
        Travelomile offers you access to and use of our site subject to your acceptance without modification...
        [All the legal text you provided goes here]
    </div>
    
    <p style="margin-top: 30px;">Best Regards,<br>
    <strong>Reservation Desk</strong><br>
    {{ Auth::user()->name ?? 'Support Team' }}</p>
</div>
</body>
</html>
