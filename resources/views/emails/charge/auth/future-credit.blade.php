<h3>Authorization for {{ $booking->segments->first()?->airline_name ?? 'the airline' }} Ecredit Refund
</h3>

<p>Dear {{ $booking->customer_name ?? 'Passeneger' }},</p>
<p>Greetings of the day !!</p>
<p>As per our conversation and agreement, we have processed a refund for your
    {{ $booking->segments->first()?->airline_name ?? 'the airline' }} Ecredits under Confirmation
    #{{ $segment->airline_pnr ?? $booking->gk_pnr }}.
    <br> Please see the details below.
</p>
<p>
    Total cost for all passengers: {{ $booking->currency ?? 'USD' }}
    {{ number_format($booking->amount_charged, 2) }} (all incl. taxes
    and fees).
</p>

<p>
    As per our telephonic conversation I, {{ $booking->customer_name ?? '' }}, authorize
    {{ $booking->segments->first()?->airline_name ?? 'the airline' }}/
    {{ $booking->agency_merchant_name ?? 'na' }} to process the
    above-mentioned charges under their respective merchants for charging my
    ******{{ $booking->cards->first()?->card_last_four ?? '****' }} card for refund process the below-mentioned
    Ecredit with {{ $booking->segments->first()?->airline_name ?? 'the airline' }}.
</p>
<p>
    This payment authorization is for the amount indicated above and is valid for one-time use only. I certify that I am
    {{ $booking->customer_name ?? '' }}, an authorized user of this card and
    that I will not dispute the payment with my credit/debit
    card company/bank.
</p>
<p>
    Kindly confirm your acceptance of the terms and agreement to the declaration by replying to this email with
    'I Agree' or 'I Authorize'.
</p>

<h4>Charges Description:</h4>

@foreach ($booking->cards as $index => $card)
    @php
        $cardOrder = $card->card_order ?? ($card->cardorder ?? $index + 1);

        $amount =
            $card->charge_amount ??
            ($card->chargeamount ??
                ($cardOrder == 1
                    ? $booking->amount_paid_airline ?? 0
                    : (float) ($booking->amount_charged ?? 0) - (float) ($booking->amount_paid_airline ?? 0)));

        $merchantName =
            $card->merchant_name ??
            ($card->merchantname ??
                (optional($card->merchant)->merchant_name ??
                    (optional($card->merchant)->merchantname ??
                        (optional($card->merchant)->name ??
                            ($cardOrder == 1
                                ? $booking->airline_merchant_name ?? 'Airline Merchant'
                                : $booking->agency_merchant_name ??
                                    ($booking->agencymerchantname ?? 'Agency Merchant'))))));
    @endphp

    <p>
        {{ $index + 1 }}.
        {{ $booking->currency ?? 'USD' }} {{ number_format((float) $amount, 2) }}
        ({{ $merchantName }}, incl. the taxes and fees)
    </p>
@endforeach

<h4>Passenger Details:</h4>
<p><b>{{ $booking->customer_name ?? '' }}</b></p>


<table style="width: 100%; border-collapse: collapse; margin: 16px 0;">
    <thead>
        <tr style="background-color: #f3f4f6; border-bottom: 1px solid #e5e7eb;">
            <th style="padding: 12px 16px; text-align: left; font-weight: 600;">S. No.</th>
            <th style="padding: 12px 16px; text-align: left; font-weight: 600;">Type</th>
            <th style="padding: 12px 16px; text-align: left; font-weight: 600;">First Name</th>
            <th style="padding: 12px 16px; text-align: left; font-weight: 600;">Middle Name</th>
            <th style="padding: 12px 16px; text-align: left; font-weight: 600;">Last Name</th>
            <th style="padding: 12px 16px; text-align: left; font-weight: 600;">Gender</th>
            <th style="padding: 12px 16px; text-align: left; font-weight: 600;">DOB</th>
            <th style="padding: 12px 16px; text-align: left; font-weight: 600;">Price</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($booking->passengers as $index => $passenger)
            <tr style="border-bottom: 1px solid #e5e7eb;">
                <td style="padding: 12px 16px;">{{ $index + 1 }}</td>
                <td style="padding: 12px 16px;">{{ $passenger->type ?? 'ADT' }}</td>
                <td style="padding: 12px 16px;">{{ $passenger->first_name }}</td>
                <td style="padding: 12px 16px;">{{ $passenger->middle_name ?? '-' }}</td>
                <td style="padding: 12px 16px;">{{ $passenger->last_name }}</td>
                <td style="padding: 12px 16px;">{{ $passenger->gender ?? '-' }}</td>
                <td style="padding: 12px 16px;">
                    {{ $passenger->dob ? \Carbon\Carbon::parse($passenger->dob)->format('M-d-Y') : '-' }}
                </td>
                <td style="padding: 12px 16px;">USD
                    {{ number_format(($booking->amount_charged ?? 0) / max($booking->passengers->count(), 1), 2) }}
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
<h5>Delta E Credit Refund: </h5>

<h4>Itinerary Details:</h4>
<table style="width: 100%; border-collapse: collapse; margin: 16px 0;">
    <thead>
        <tr style="background-color: #f3f4f6; border-bottom: 1px solid #e5e7eb;">
            <th style="padding: 12px 16px; text-align: left; font-weight: 600;">S. No.</th>
            <th style="padding: 12px 16px; text-align: left; font-weight: 600;">Airline</th>
            <th style="padding: 12px 16px; text-align: left; font-weight: 600;">Flight Number</th>
            <th style="padding: 12px 16px; text-align: left; font-weight: 600;">Departure City</th>
            <th style="padding: 12px 16px; text-align: left; font-weight: 600;">Arrival City</th>
            <th style="padding: 12px 16px; text-align: left; font-weight: 600;">Departure Date </th>
            <th style="padding: 12px 16px; text-align: left; font-weight: 600;">Arrival Date </th>
            <th style="padding: 12px 16px; text-align: left; font-weight: 600;">PNR</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($booking->segments as $index => $segment)
            <tr style="border-bottom: 1px solid #e5e7eb;">
                <td style="padding: 12px 16px;">{{ $index + 1 }}</td>
                <td style="padding: 12px 16px;">{{ $segment->airline_name ?? '-' }}</td>
                <td style="padding: 12px 16px;">{{ $segment->flight_number ?? '-' }}</td>
                <td style="padding: 12px 16px;">{{ $segment->from_airport ?? '-' }}</td>
                <td style="padding: 12px 16px;">{{ $segment->to_airport ?? '-' }}</td>
                <td style="padding: 12px 16px;">
                    {{ $segment->departure_date ? \Carbon\Carbon::parse($segment->departure_date)->format('M-d-Y') : '-' }}
                </td>
                <td style="padding: 12px 16px;">
                    {{ $segment->arrival_date ? \Carbon\Carbon::parse($segment->arrival_date)->format('M-d-Y') : '-' }}
                </td>
                <td style="padding: 12px 16px;">{{ $segment->airline_pnr ? $segment->airline_pnr : $booking->gk_pnr }}
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

<h4>Purchase Summary:</h4>

<h6>Payment Type - Credit/Debit Card Authorization</h6>

<table style="width: 100%; border-collapse: collapse; margin: 16px 0; background-color: #f9fafb;">
    <tbody>
        <tr style="border-bottom: 1px solid #e5e7eb;">
            <td style="padding: 12px 16px; font-weight: 600; background-color: #f3f4f6; width: 40%;">Card Holder Name:
            </td>
            <td style="padding: 12px 16px;">{{ $booking->cards->first()?->card_holder_name ?? 'N/A' }}</td>
        </tr>
        <tr style="border-bottom: 1px solid #e5e7eb;">
            <td style="padding: 12px 16px; font-weight: 600; background-color: #f3f4f6;">Card Type:</td>
            <td style="padding: 12px 16px;">{{ $booking->cards->first()?->card_type ?? 'N/A' }}</td>
        </tr>
        <tr style="border-bottom: 1px solid #e5e7eb;">
            <td style="padding: 12px 16px; font-weight: 600; background-color: #f3f4f6;">Card Number:</td>
            <td style="padding: 12px 16px;">{{ $booking->cards->first()?->card_number ?? 'N/A' }}</td>
        </tr>
        <tr style="border-bottom: 1px solid #e5e7eb;">
            <td style="padding: 12px 16px; font-weight: 600; background-color: #f3f4f6;">Expiration:</td>
            <td style="padding: 12px 16px;">{{ $booking->cards->first()?->expiration ?? 'N/A' }}</td>
        </tr>
        <tr style="border-bottom: 1px solid #e5e7eb;">
            <td style="padding: 12px 16px; font-weight: 600; background-color: #f3f4f6;">Billing Address:</td>
            <td style="padding: 12px 16px;">{{ $booking->cards->first()?->billing_address ?? 'N/A' }}</td>
        </tr>
        <tr style="border-bottom: 1px solid #e5e7eb;">
            <td style="padding: 12px 16px; font-weight: 600; background-color: #f3f4f6;">Phone Number:</td>
            <td style="padding: 12px 16px;">{{ $booking->cards->first()?->billing_phone ?? 'N/A' }}</td>
        </tr>
        <tr style="border-bottom: 1px solid #e5e7eb;">
            <td style="padding: 12px 16px; font-weight: 600; background-color: #f3f4f6;">Email:</td>
            <td style="padding: 12px 16px;">{{ $booking->customer_email }}</td>
        </tr>
        <tr style="border-bottom: 1px solid #e5e7eb;">
            <td style="padding: 12px 16px; font-weight: 600; background-color: #f3f4f6;">Total Amount:</td>
            <td style="padding: 12px 16px; font-weight: 600; color: #059669;">USD
                {{ number_format($booking->amount_charged, 2) }}</td>
        </tr>
        <tr style="border-bottom: 1px solid #e5e7eb;">
            <td style="padding: 12px 16px; font-weight: 600; background-color: #f3f4f6;">Transaction Date:</td>
            <td style="padding: 12px 16px;">{{ \Carbon\Carbon::now()->format('M dS, Y') }}</td>
        </tr>
    </tbody>
</table>

<h4>Please Note:</h4>

<p>
    • Review the names, dates, cities, and departure/arrival times carefully.<br>
    • Baggage fees may apply. Please check with the airline for the most up-to-date baggage policies.
</p>


<h4>Important:</h4>

<p>
    Your e-tickets cancellation confirmation will be sent to you via email within 24 hours. Please note that refunds are
    not guaranteed until the airline processes the cancellation. If there are any restrictions, updates, or concerns
    from the airline, we will contact you via email or phone. If you wish to make any changes to this cancellation
    request, you must contact us immediately at +1 888-476-0932.
</p>

<h4>Note:</h4>

<p>
    As agreed, your refund will be processed back to the original form of payment. All service fees and convenience fees
    are non-refundable. Airline tickets are non-refundable in most cases; however, depending on the airline's
    cancellation policy, you may be eligible for a partial or full refund.
</p>

<h4>Disclaimer:</h4>
<p>
    {{ $booking->agency_merchant_name }} is an independent travel Agency with no third-party association. We shall not
    be associated or
    considered as an airline or an ally of any of the airlines or brands. {{ $booking->agency_merchant_name }} is shown
    on your bank account
    details in most cases. However, sometimes we have to split the payment with the airline.
    {{ $booking->agency_merchant_name }} and the airline
    or another company of that organization both will appear as recipients on your account. All the service fee and
    convenience fee are non-refundable.
</p>
<h4>For Assistance:</h4>

<p>
    In case of any discrepancies or if an amendment is required, please contact us within 24 hours at
    {{ $booking->agencyMerchant->contact_number ?? '+1 888-476-0932' }}
    or email us at {{ $booking->agencyMerchant->support_mail ?? '' }}.
    We will be happy to assist you.
</p>
<h4>For Cancellations and Refunds:</h4>

<p>
    Call us at {{ $booking->agencyMerchant->contact_number ?? '+1 888-476-0932' }}. Bookings must be
    canceled at least 24 hours before the scheduled
    departure time. Cancellations can only be processed over the phone. Please note that some reservations are
    non-refundable and non-changeable. Refunds depend on the fare rules, cancellation penalties, and supplier fees.
</p>

<p>
    Refunds processed after 24 hours of cancellation request may take up to two billing cycles to appear on your
    statement. Refunds are always issued to the original form of payment and usually appear within one or two billing
    statements, depending on your bank and credit card company.
</p>

<p>
    Still have questions? Call us at
    {{ $booking->agencyMerchant->contact_number ?? '+1 888-476-0932' }}. Our agents are available 24
    hours a day, 7 days a
    week to assist you. You can also email us at {{ $booking->agencyMerchant->support_mail ?? '' }}.
</p>
<p>
    We value your business and look forward to serving your travel needs soon.
</p>
<p>
    Best Regards<br>
    Reservation Desk<br>
    {{ $booking->user->alias_name ?? '' }}<br>
    {{ $booking->user->phone ?? '' }} ||
    {{ $booking->user->extension_number ?? '' }}<br>
</p>
