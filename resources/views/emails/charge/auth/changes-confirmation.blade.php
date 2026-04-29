<h3>
    <strong>Authorization for {{ $booking->segments->first()?->airline_name ?? 'the airline' }} Flight
        Changes</strong>
</h3>

<p>
    Dear {{ $booking->passengers->first()?->first_name ?? 'Valued Customer' }},
</p>

<p>
    Greetings of the day !!
</p>

<p><br></p>

<p>
    As per our conversation and as agreed, we have changed your reservation with <strong>{{
        $booking->segments->first()?->airline_name ?? 'the airline' }}</strong> under Confirmation <strong>#{{
        $booking->booking_reference }}</strong>. Please see the details below.
</p>

<p><br></p>

<p>
    <strong>Total cost for all passengers:</strong> USD {{ number_format($booking->total_cost, 2) }} (all incl. taxes and fees).
</p>

<p><br></p>

<p>
    As per our telephonic conversation I, <strong>{{ strtoupper($booking->passengers->first()?->first_name ?? 'Name') }} {{
        strtoupper($booking->passengers->first()?->middle_name ?? '') }} {{ strtoupper($booking->passengers->first()?->last_name ?? 'Last')
        }}</strong>, authorize <strong>{{ $booking->segments->first()?->airline_name ?? 'the airline'
        }}/Travelomile</strong> to process the above-mentioned charges under their respective merchants for charging my
    <strong>{{ $booking->cards->first()?->card_number ?? '****' }}</strong> card for changing to the
    below-mentioned itinerary with <strong>{{ $booking->segments->first()?->airline_name ?? 'the airline' }}</strong>.
</p>

<p><br></p>

<p>
    This payment authorization is for the amount indicated above and is valid for one-time use only. I certify that I am
    <strong>{{ strtoupper($booking->passengers->first()?->first_name ?? 'Name') }} {{ strtoupper($booking->passengers->first()?->middle_name ??
        '') }} {{ strtoupper($booking->passengers->first()?->last_name ?? 'Last') }}</strong>, an authorized user of this card and
    that I will not dispute the payment with my credit/debit card company/bank.
</p>

<p><br></p>

<p><br></p>

<p>
    <strong>Kindly confirm your acceptance of the terms and agreement to the declaration by replying to this email with
        'I Agree' or 'I Authorize'.</strong>
</p>

<p><br></p>

<h4><strong>Charges Description:</strong></h4>

<p><br></p>

@foreach($booking->cards as $index => $card)
<p>
    {{ $index + 1 }}. USD {{ number_format($card->amount ?? 0, 2) }} ({{ $card->merchant?->name ?? 'Merchant' }}, {{ $card->description ?? 'including all the taxes and fees' }})
</p>
@endforeach

<p><br></p>

<p><br></p>

<h4><strong>Passenger Details:</strong></h4>

<p><br></p>

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
        @foreach($booking->passengers as $index => $passenger)
        <tr style="border-bottom: 1px solid #e5e7eb;">
            <td style="padding: 12px 16px;">{{ $index + 1 }}</td>
            <td style="padding: 12px 16px;">{{ $passenger->type ?? 'ADT' }}</td>
            <td style="padding: 12px 16px;">{{ strtoupper($passenger->first_name) }}</td>
            <td style="padding: 12px 16px;">{{ strtoupper($passenger->middle_name ?? '-') }}</td>
            <td style="padding: 12px 16px;">{{ strtoupper($passenger->last_name) }}</td>
            <td style="padding: 12px 16px;">{{ ucfirst($passenger->gender ?? '-') }}</td>
            <td style="padding: 12px 16px;">{{ $passenger->dob ? \Carbon\Carbon::parse($passenger->dob)->format('M d, Y')
                : '-' }}</td>
            <td style="padding: 12px 16px;">USD {{ number_format($passenger->price ?? 0, 2) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

<p><br></p>

<h4><strong>Flight Itinerary:</strong></h4>

<p><br></p>

@php
    $outboundSegments = $booking->segments->where('segment_type', 'outbound')->sortBy('departure_date');
    $returnSegments = $booking->segments->where('segment_type', 'return')->sortBy('departure_date');
@endphp

@if($outboundSegments->count())
<h5><strong>Outbound: {{ $outboundSegments->first()?->origin ?? 'N/A' }} → {{ $outboundSegments->last()?->destination ?? 'N/A' }}</strong></h5>

@foreach($outboundSegments as $segment)
<p>
    <strong>{{ \Carbon\Carbon::parse($segment->departure_date)->format('D, M d') }}</strong><br>
    <strong>{{ $segment->flight_number }}</strong> - {{ $segment->cabin_class }}
</p>

<p>
    <strong>{{ strtoupper($segment->origin) }}</strong><br>
    {{ $segment->departure_time }} — {{ $segment->departure_airport }}
</p>

<p>
    ↓ Duration: {{ $segment->duration ?? 'N/A' }}
</p>

<p>
    <strong>{{ strtoupper($segment->destination) }}</strong><br>
    {{ $segment->arrival_time }} — {{ $segment->arrival_airport }}
</p>

@if($segment->transit_duration)
<p>
    {{ $segment->transit_duration }} transit at {{ $segment->destination }}
</p>
@endif

<p><br></p>
@endforeach
@endif

@if($returnSegments->count())
<h5><strong>Return: {{ $returnSegments->first()?->origin ?? 'N/A' }} → {{ $returnSegments->last()?->destination ?? 'N/A' }}</strong></h5>

@foreach($returnSegments as $segment)
<p>
    <strong>{{ \Carbon\Carbon::parse($segment->departure_date)->format('D, M d') }}</strong><br>
    <strong>{{ $segment->flight_number }}</strong> - {{ $segment->cabin_class }}
</p>

<p>
    <strong>{{ strtoupper($segment->origin) }}</strong><br>
    {{ $segment->departure_time }} — {{ $segment->departure_airport }}
</p>

<p>
    ↓ Duration: {{ $segment->duration ?? 'N/A' }}
</p>

<p>
    <strong>{{ strtoupper($segment->destination) }}</strong><br>
    {{ $segment->arrival_time }} — {{ $segment->arrival_airport }}
</p>

@if($segment->transit_duration)
<p>
    {{ $segment->transit_duration }} transit at {{ $segment->destination }}
</p>
@endif

<p><br></p>
@endforeach
@endif

<p><br></p>

<h4><strong>Purchase Summary:</strong></h4>

<p><br></p>

<h4><strong>Payment Type - Credit/Debit Card Authorization</strong></h4>

<table style="width: 100%; border-collapse: collapse; margin: 16px 0;">
    <tr style="border-bottom: 1px solid #e5e7eb;">
        <td style="padding: 8px 16px; font-weight: 600; width: 40%;">Card Holder Name:</td>
        <td style="padding: 8px 16px;">{{ strtoupper($booking->passengers->first()?->first_name ?? 'N/A') }} {{ strtoupper($booking->passengers->first()?->middle_name ?? '') }} {{ strtoupper($booking->passengers->first()?->last_name ?? 'N/A') }}</td>
    </tr>
    <tr style="border-bottom: 1px solid #e5e7eb;">
        <td style="padding: 8px 16px; font-weight: 600;">Card Type:</td>
        <td style="padding: 8px 16px;">{{ $booking->cards->first()?->card_type ?? 'VISA' }}</td>
    </tr>
    <tr style="border-bottom: 1px solid #e5e7eb;">
        <td style="padding: 8px 16px; font-weight: 600;">Card Number:</td>
        <td style="padding: 8px 16px;">{{ $booking->cards->first()?->card_number ?? '****' }}</td>
    </tr>
    <tr style="border-bottom: 1px solid #e5e7eb;">
        <td style="padding: 8px 16px; font-weight: 600;">Expiration:</td>
        <td style="padding: 8px 16px;">{{ $booking->cards->first()?->expiration ?? 'N/A' }}</td>
    </tr>
    <tr style="border-bottom: 1px solid #e5e7eb;">
        <td style="padding: 8px 16px; font-weight: 600;">Billing Address:</td>
        <td style="padding: 8px 16px;">{{ $booking->billing_address ?? 'N/A' }}</td>
    </tr>
    <tr style="border-bottom: 1px solid #e5e7eb;">
        <td style="padding: 8px 16px; font-weight: 600;">Phone Number:</td>
        <td style="padding: 8px 16px;">{{ $booking->phone ?? 'N/A' }}</td>
    </tr>
    <tr style="border-bottom: 1px solid #e5e7eb;">
        <td style="padding: 8px 16px; font-weight: 600;">Email:</td>
        <td style="padding: 8px 16px;">{{ $booking->email ?? 'N/A' }}</td>
    </tr>
    <tr style="border-bottom: 1px solid #e5e7eb;">
        <td style="padding: 8px 16px; font-weight: 600;">Total Amount:</td>
        <td style="padding: 8px 16px;">USD {{ number_format($booking->total_cost, 2) }}</td>
    </tr>
    <tr style="border-bottom: 1px solid #e5e7eb;">
        <td style="padding: 8px 16px; font-weight: 600;">Transaction Date:</td>
        <td style="padding: 8px 16px;">{{ now()->format('M d, Y') }}</td>
    </tr>
</table>

<p><br></p>

<p>
    <strong>Please Note:</strong><br>
    Review the names, dates, cities, and departure/arrival times carefully.
</p>

<p><br></p>

<p>
    <strong>Baggage fees may apply.</strong><br>
    Please check with the airline for the most up-to-date baggage policies.
</p>

<p><br></p>

<p>
    <strong>Important:</strong><br>
    Your e-tickets will be sent to you via email within 24 hours, or sooner if there is no delay from the airline's side. Please note that fares are not guaranteed until payment is received and tickets are issued. If there are any restrictions, updates, or concerns from the airline, we will contact you via email or phone. If you wish to make any changes to this itinerary after the tickets have been issued, you will be responsible for any additional penalties, fare differences, and applicable fees.
</p>

<p><br></p>

<p>
    <strong>Note:</strong><br>
    As agreed, your credit card may be charged in split transactions, not exceeding the total amount. All transactions are for service fees and are <strong>100% non-refundable</strong>. Airline tickets are non-refundable; however, you may be eligible for a refund within 24 hours of purchase, depending on the airline's policy.
</p>

<p><br></p>

<p>
    <strong>Disclaimer:</strong><br>
    Travelomile is an independent travel agency with no third-party association. We shall not be associated or considered as an airline or an ally of any of the airlines or brands. Travelomile is shown on your bank account details in most cases. However, sometimes we have to split the payment with the airline. Travelomile and the airline or another company of that organization both will appear as recipients on your account. All the service fees and convenience fees are non-refundable.
</p>

<p><br></p>

<p>
    <strong>For Assistance:</strong><br>
    In case of any discrepancies or if an amendment is required, please feel free to contact us at <strong>+1 888-476-0932</strong> within 24 hours and we will be happy to assist you.
</p>

<p><br></p>

<p>
    <strong>Important Information:</strong><br>
    Please review your itinerary carefully to ensure that the following key items are correct:
</p>

<ul style="margin-left: 20px;">
    <li>Passenger names must be the same as on the passport (International Travel) OR any government-approved photo ID proof for Domestic travel.</li>
    <li>We advise all passengers to ensure to have all travel documents including passports, and required visas issued and presented at the time of travel.</li>
    <li>All passengers are recommended to be present at the airport 3 hours before departure for international departures, and 2 hours before domestic travel.</li>
    <li>All international flights must be confirmed 72 hours before departure.</li>
    <li>Review departure/arrival dates, times, origin/destination cities, stopovers, and connections.</li>
</ul>

<p><br></p>

<p>
    <strong>For Changes Queries:</strong><br>
    Call us at <strong>+1 888-476-0932</strong> to make any changes to your itinerary. All changes must be made prior to the flight's departure. The airline's rules, including penalties, supplier fees, and fare differences, will be communicated before any modifications are processed. Please note that some reservations may be non-refundable and non-changeable. Additionally, once a change is processed, any additional amount collected will be non-refundable and non-transferable.
</p>

<p><br></p>

<p>
    <strong>For Cancellations and Refunds:</strong><br>
    Call us at <strong>+1 888-476-0932</strong>. Bookings must be canceled at least 24 hours before the scheduled departure time to avoid a no-show. Cancellations can only be processed over the phone. Please note that some reservations are non-refundable and non-changeable. Refunds depend on the fare rules, cancellation penalties, and supplier fees. Cancellation/refund penalties may be charged as a new fee or deducted from the ticket value based on the itinerary and fare rules.
</p>

<p>
    Refunds processed after 24 hours of booking may take up to two billing cycles to appear on your statement. If flights are not canceled before the scheduled departure time, the entire amount will be forfeited. Refunds are always issued to the original form of payment and usually appear within one or two billing statements, depending on your bank and credit card company. Processing times may vary depending on the airline or consolidator and the type of booking.
</p>

<p><br></p>

<p>
    Still have questions? Call us at <strong>+1 888-476-0932</strong>. Our agents are available 24 hours a day, 7 days a week to assist you. You can also email us at <strong>reservation@travelomile.com</strong>.
</p>

<p><br></p>

<p>
    We value your business and look forward to serving your travel needs soon.
</p>

<p><br></p>

<p>
    <strong>Best Regards,</strong><br>
    <strong>Reservation Desk</strong><br>
    TraveloMile
</p>
