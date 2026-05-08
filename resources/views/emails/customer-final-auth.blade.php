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

    {{-- Common authorization text --}}
    {{-- <p>
        @if($isAgencyOnly)
            As per our telephonic conversation I, {{ $booking->customer_name }},
            authorize {{ $merchantName }} to process the above-mentioned charges
            for the {{ $booking->service_type }} itinerary using my
            ************{{ $booking->card_last_four }} card.
        @else
            As per our telephonic conversation I, {{ $booking->customer_name }},
            authorize {{ $airlineName }}/{{ $merchantName }} to process the above-mentioned
            charges under their respective merchants for charging my
            ************{{ $booking->card_last_four }} card for the
            {{ $booking->service_type }} itinerary with {{ $airlineName }}.
        @endif
    </p> --}}

    <p>
        This payment authorization is for the amount indicated above and is valid for one-time use only.
        I certify that I am {{ $booking->customer_name }}, an authorized user of this card and that I
        will not dispute the payment with my credit/debit card company/bank.
    </p>

    <p>
        Kindly confirm your acceptance of the terms and agreement to the declaration by replying to this
        email with 'I Agree' or 'I Authorize'.
    </p>

    {{-- Here you can add any shared “Please Note / Important / For Assistance” blocks as you had earlier --}}

    {{-- Footer --}}
    {{-- <p>
        Best Regards<br>
        Reservation Desk<br>
        {{ $booking->agent_name ?? 'Agent Name' }}<br>
        {{ $booking->agent_phone ?? $merchantPhone }}
        @if(!empty($booking->agent_extension))
            || Ext: {{ $booking->agent_extension }}
        @endif
    </p> --}}
</body>
</html>