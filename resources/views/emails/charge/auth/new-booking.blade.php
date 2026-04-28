<h3><strong>{{ $booking->airline_pnr ? $booking->airline_pnr : $booking->gk_pnr }}</strong> New Booking Confirmation</h3>

<p>Dear {{ $booking->customer_name }},</p>

<p>Greetings of the day !!</p>

<p>
As per our conversation and as agreed, we have booked your flight reservation under
Confirmation number <strong>{{ $booking->airline_pnr ? $booking->airline_pnr : $booking->gk_pnr }}</strong>.
</p>
   
<p>
<strong>
Total Cost for all passengers - {{ $booking->currency ?? 'USD' }} {{ number_format($booking->amount_charged, 2) }}
(Including all taxes and fees).
</strong>
</p>

<p>
As per our telephonic conversation I {{ $booking->customer_name }} authorized 
{{ $booking->company_name ?? 'Travelomile' }} to process the above-mentioned charges 
under their respective merchants for charging my 
{{ $booking->masked_card_number }} card for the amount of 
{{ $booking->currency ?? 'USD' }} {{ number_format($booking->amount_charged, 2) }} 
for booking a New Flight Reservation.
</p>

<p>
This payment authorization is for the amount indicated above and is valid for one-time use only.
I certify that I {{ $booking->customer_name }} am an authorized user of this card and that I will not dispute the payment.
</p>

<h4>Charges Description:</h4>

<p>
Charge : {{ $booking->currency ?? 'USD' }} {{ number_format($booking->amount_paid_airline, 2) }} - 
({{ $booking->airline_merchant_name ?? 'Airline' }}, includes all the taxes and service fee)
</p>

<p>
Charge : {{ $booking->currency ?? 'USD' }} {{ number_format($booking->total_mco, 2) }} -
({{ $booking->agency_merchant_name ?? 'Service Charge' }}, includes all the taxes and service fee)
</p>

<h4>Passenger Details:</h4>

<table border="1" cellpadding="8" cellspacing="0" width="100%">
    <thead>
        <tr>
            <th>S. No.</th>
            <th>Type</th>
            <th>First Name</th>
            <th>Middle Name</th>
            <th>Last Name</th>
            <th>Gender</th>
            <th>DOB</th>
        </tr>
    </thead>
    <tbody>
        @foreach($booking->passengers as $index => $pax)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $pax->type ?? 'ADT' }}</td>
                <td>{{ $pax->first_name }}</td>
                <td>{{ $pax->middle_name }}</td>
                <td>{{ $pax->last_name }}</td>
                <td>{{ $pax->gender }}</td>
                <td>{{ \Carbon\Carbon::parse($pax->dob)->format('M-d-Y') }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

<p><strong>Total Amount: USD {{ number_format($booking->amount_charged, 2) }}</strong></p>

<h4>Payment Details:</h4>

<table border="1" cellpadding="8" cellspacing="0" width="100%">
    <tr>
        <td>Card Holder Name:</td>
        <td>{{ $booking->customer_name }} {{ $booking->customer_middle_name }} {{ $booking->customer_last_name }}</td>
    </tr>
    <tr>
        <td>Card Number:</td>
        <td>{{ $booking->card_last_four }} </td>
    </tr>
    <tr>
        <td>Card Type:</td>
        <td>{{ $booking->card_type }}</td>
    </tr>
    <tr>
        <td>Expiration:</td>
        <td>{{ $booking->card_expiry }}</td>
    </tr>
    <tr>
        <td>Billing Address:</td>
        <td>{{ $booking->billing_address }}</td>
    </tr>
    <tr>
        <td>Transaction Date:</td>
        <td>{{ \Carbon\Carbon::parse($booking->transaction_date)->format('Y-m-d') }}</td>
    </tr>
    <tr>
        <td>Phone Number:</td>
        <td>{{ $booking->billing_phone }}</td>
    </tr>
    <tr>
        <td>Email:</td>
        <td>{{ $booking->customer_email }}</td>
    </tr>
</table>

<h4>Customer Support Details</h4>

<table border="1" cellpadding="8" cellspacing="0" width="100%">
    <tr>
        <th>Phone Number</th>
        <th>Email</th>
    </tr>
    <tr>
        <td>{{ $booking->support_phone ?? '+1-888-hasd-5053' }}</td>
        <td>{{ $booking->support_email ?? 'reservation@travelomile.com' }}</td>
    </tr>
</table>

<p>
Make sure that the displayed flight information is correct. Please review the Names, Dates,
Cities, and Departure – Arrival times properly.
</p>

