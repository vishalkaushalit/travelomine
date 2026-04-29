<h3><strong>Authorization for {{ $booking->segments->first()?->airline_name ?? 'the airline' }} Pet Addition</strong>
</h3>

<p>Dear {{ $booking->customer_name ?? 'Passeneger' }},</p>
<p>Greetings of the day !!</p>
<p>As per our conversation and as agreed, we have added your pet toyour reservation with United Airlines under
    Confirmation #CHE7BS. Please see the details below.
</p>
<p> As per our conversation, we have added a pet to your existing flight reservation under Confirmation number
    <strong>{{ $booking->airline_pnr ? $booking->airline_pnr : $booking->gk_pnr }}</strong>.
</p>
<p> <strong> Total Pet Addition Cost - {{ $booking->currency ?? 'USD' }} {{ number_format($booking->pet_amount, 2) }}
        (Including all taxes and fees). </strong> </p>
<p> As discussed, I {{ $booking->customer_name }} authorize {{ $booking->company_name ?? 'Travelomile' }} to process the
    above-mentioned charges for adding a pet to my booking using my {{ $booking->masked_card_number }} card for the
    amount of {{ $booking->currency ?? 'USD' }} {{ number_format($booking->pet_amount, 2) }}. </p>
<p> This authorization is valid for one-time use only. I confirm that I am an authorized user of this card and will not
    dispute this transaction. </p>

<h4>Charges Description:</h4>
<p> Charge : {{ $booking->currency ?? 'USD' }} {{ number_format($booking->pet_amount, 2) }} -
    ({{ $booking->pet_merchant ?? 'Airline Pet Fee' }}, includes all taxes and service fee) </p> <br>
<h4>Payment Details:</h4>
<table border="1" cellpadding="8" cellspacing="0" width="100%">
    <tr>
        <td>Card Holder Name:</td>
        <td>{{ $booking->customer_name }}</td>
    </tr>
    <tr>
        <td>Card Number:</td>
        <td>{{ $booking->card_last_four }}</td>
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
        <td>{{ $booking->phone }}</td>
    </tr>
    <tr>
        <td>Email:</td>
        <td>{{ $booking->email }}</td>
    </tr>
</table> <br>
<h4>Customer Support Details</h4>
<table border="1" cellpadding="8" cellspacing="0" width="100%">
    <tr>
        <th>Phone Number</th>
        <th>Email</th>
    </tr>
    <tr>
        <td>{{ $booking->support_phone ?? '+1-888-575-5053' }}</td>
        <td>{{ $booking->support_email ?? 'reservation@travelomile.com' }}</td>
    </tr>
</table> <br>
<p> Please review all pet details carefully. Ensure compliance with airline pet policies, including carrier size, weight
    limits, and documentation requirements. </p>
