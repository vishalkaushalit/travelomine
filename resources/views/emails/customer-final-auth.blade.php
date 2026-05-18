@php
    $merchantName   = $booking->agencyMerchant->name ?? 'Travelomile';
    $merchantSupport = $booking->agencyMerchant->support_mail ?? null;
    $merchantPhone   = $booking->agencyMerchant->contact_number ?? null;

    $airlineName  = $booking->airline_name ?? null;
    $isAgencyOnly = empty($airlineName); // full charge on agency merchant
@endphp

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Authorization Mail</title>
</head>
<body>

    {{-- Header --}}
    <p>
        Dear {{ $booking->customer_name }},<br>
        Greetings of the day !!
    </p>

    {{-- Dynamic body (edited per booking) --}}
    {!! $emailBody !!}
    <p>
        This payment authorization is for the amount indicated above and is valid for one-time use only.
        I certify that I am {{ $booking->customer_name }}, an authorized user of this card and that I
        will not dispute the payment with my credit/debit card company/bank.
    </p>

    <p>
        Kindly confirm your acceptance of the terms and agreement to the declaration by replying to this
        email with 'I Agree' or 'I Authorize'.
    </p>


</body>
</html>