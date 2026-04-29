<p>
    This email confirms the flight exchange process for your booking.
</p>
<p>Dear {{ $booking->customer_name }},</p>
<p>
    Greetings of the day !!
</p>

<p>
    As per our conversation and as agreed, we have made the changes on your reservation with
    <strong>{{ $booking->segments->first()?->airline_name ?? 'the airline' }}</strong> under Confirmation
    <strong>#{{ $booking->booking_reference }}</strong>. Please see the details below.
</p>

<p><br></p>

<p>
    <strong>Total penalty for all passengers:</strong> USD {{ number_format($booking->total_cost, 2) }} (including all
    taxes and fees).
</p>

<p><br></p>

<p>
    <strong>Note:</strong> You will get airline future credit of USD {{ number_format($booking->total_cost, 2) }} and
    valid till {{ $booking->credit_valid_till ?? 'TBD' }}
</p>

<p><br></p>

<p>
    As per our telephonic conversation I, <strong>{{ $booking->passengers->first()?->first_name ?? 'Name' }}
        {{ $booking->passengers->first()?->middle_name ?? '' }}
        {{ $booking->passengers->first()?->last_name ?? 'Last' }}</strong>, authorize
    <strong>{{ $booking->segments->first()?->airline_name ?? 'the airline' }}/Travelomile</strong> to process the
    above-mentioned charges under their respective merchants for charging my
    <strong>{{ $booking->cards->first()?->card_number ?? '****' }}</strong> card for the flight change on the
    below-mentioned itinerary with <strong>{{ $booking->segments->first()?->airline_name ?? 'the airline' }}</strong>.
</p>

<p><br></p>

<p>
    This payment authorization is for the amount indicated above and is valid for one-time use only. I certify that I am
    <strong>{{ $booking->passengers->first()?->first_name ?? 'Name' }}
        {{ $booking->passengers->first()?->middle_name ?? '' }}
        {{ $booking->passengers->first()?->last_name ?? 'Last' }}</strong>, an authorized user of this card and
    that I will not dispute the payment with my credit/debit card company/bank.
</p>

<p><br></p>

<p>
    <strong>Kindly confirm your acceptance of the terms and agreement to the declaration by replying to this email with
        'I Agree' or 'I Authorize'.</strong>
</p>

<p><br></p>

<h4><strong>Charges Description:</strong></h4>

<p><br></p>

@foreach ($booking->cards as $index => $card)
    <p>
        {{ $index + 1 }}. USD {{ number_format($card->amount ?? 0, 2) }}
        ({{ $card->merchant?->name ?? 'Merchant' }},
        including all the taxes and fees)
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
            </tr>
        @endforeach
    </tbody>
</table>

<p><br></p>

<h4><strong>Flight Itinerary:</strong></h4>

<p><br></p>

@if ($booking->segments->first())
    {{-- <p>
        <strong>Flight: {{ $booking->segments->first()?->origin ?? 'N/A' }} →
            {{ $booking->segments->first()?->destination ?? 'N/A' }}</strong>
        ({{ $booking->segments->first()?->duration ?? '5h 36m' }})
    </p> --}}
    <p>
        <strong>Flight: {{ $booking->departure_city ?? '' }} →
            {{ $booking->arrival_city ?? '' }}</strong>
    </p>

    <p>
        <strong>Date:</strong>
        {{ $booking->segments->first()?->departure_date
            ? \Carbon\Carbon::parse($booking->segments->first()->departure_date)->format('D, M d')
            : 'TBD' }}
    </p>

    <p>
        <strong>Flight Number:</strong> {{ $booking->segments->first()?->flight_number ?? 'N/A' }} |
        {{ $booking->segments->first()?->cabin_class ?? 'N/A' }}
    </p>

    <p>
        <strong>Departure:</strong> {{ $booking->segments->first()?->departure_time ?? 'TBD' }} —
        {{ $booking->segments->first()?->departure_city ?? 'N/A' }}
    </p>

    <p>
        <strong>Arrival:</strong> {{ $booking->segments->first()?->arrival_time ?? 'TBD' }} —
        {{ $booking->segments->first()?->arrival_city ?? 'N/A' }}
    </p>

    <p>
        <strong>Duration:</strong> {{ $booking->segments->first()?->duration ?? 'N/A' }}
    </p>
@endif

<h4><strong>Purchase Summary:</strong></h4>


<h4><strong>Payment Type: Debit/Credit Card Authorization</strong></h4>


<table style="width: 100%; border-collapse: collapse; margin: 16px 0; background-color: #f9fafb;">
    <tbody>
        <tr style="border-bottom: 1px solid #e5e7eb;">
            <td style="padding: 12px 16px; font-weight: 600; background-color: #f3f4f6; width: 40%;">Card Holder Name:
            </td>
            <td style="padding: 12px 16px;">{{ $booking->cards->first()?->card_holder_name ?? 'N/A' }}</td>
        </tr>
        <tr style="border-bottom: 1px solid #e5e7eb;">
            <td style="padding: 12px 16px; font-weight: 600; background-color: #f3f4f6;">Card Number:</td>
            <td style="padding: 12px 16px;">************{{ $booking->card_last_four ?? '************N/A' }}</td>
        </tr>
        <tr style="border-bottom: 1px solid #e5e7eb;">
            <td style="padding: 12px 16px; font-weight: 600; background-color: #f3f4f6;">Card Type:</td>
            <td style="padding: 12px 16px;">{{ $booking->cards->first()?->card_type ?? 'N/A' }}</td>
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
            <td style="padding: 12px 16px; font-weight: 600; background-color: #f3f4f6;">Trans. Date:</td>
            <td style="padding: 12px 16px;">{{ \Carbon\Carbon::now()->format('M-dS-Y') }}</td>
        </tr>
        <tr style="border-bottom: 1px solid #e5e7eb;">
            <td style="padding: 12px 16px; font-weight: 600; background-color: #f3f4f6;">Price:</td>
            <td style="padding: 12px 16px; font-weight: 600; color: #059669;">USD
                {{ number_format($booking->total_cost, 2) }}</td>
        </tr>
        <tr style="border-bottom: 1px solid #e5e7eb;">
            <td style="padding: 12px 16px; font-weight: 600; background-color: #f3f4f6;">Phone Number:</td>
            <td style="padding: 12px 16px;">{{ $booking->cards->first()?->phone ?? 'N/A' }}</td>
        </tr>
        <tr style="border-bottom: 1px solid #e5e7eb;">
            <td style="padding: 12px 16px; font-weight: 600; background-color: #f3f4f6;">Email:</td>
            <td style="padding: 12px 16px;">{{ $booking->customer_email }}</td>
        </tr>
    </tbody>
</table>

<p><br></p>

<h4><strong>Important:</strong></h4>

<p>
    Your e-tickets will be sent to you via email within 24 hours, or sooner if there is no delay from the airline's
    side. Please note that fares are not guaranteed until payment is received and tickets are issued. If there are any
    restrictions, updates, or concerns from the airline, we will contact you via email or phone. If you wish to make any
    changes to this itinerary after the tickets have been issued, you will be responsible for any additional penalties,
    fare differences, and applicable fees.
</p>


<p>
    <strong>Baggage fees may apply.</strong> Please check with the airline for the most up-to-date baggage policies.
</p>


<h4><strong>Note:</strong></h4>

<p>
    As agreed, your credit card may be charged in split transactions, not exceeding the total amount. All transactions
    are for service fees and are <strong>100% non-refundable</strong>. Airline tickets are non-refundable; however, you
    may be eligible for a refund within 24 hours of purchase, depending on the airline's policy.
</p>


<h4><strong>Disclaimer:</strong></h4>

<p>
    Travelomile is an independent travel Agency with no third-party association. We shall not be associated or
    considered as an airline or an ally of any of the airlines or brands. Travelomile is shown on your bank account
    details in most cases. However, sometimes we have to split the payment with the airline. Travelomile and the airline
    or another company of that organization both will appear as recipients on your account. All the service fees and
    convenience fees are non-refundable.
</p>


<h4><strong>For Assistance:</strong></h4>

<p>
    In case of any discrepancy and if an amendment is required, please feel free to contact us at <strong>+1
        888-476-0932</strong> or email us at <strong>support@travelomile.com</strong> within 24 hours and we will be
    happy to assist you.
</p>


<h4><strong>Important Information:</strong></h4>

<p>
    Please review your itinerary carefully to ensure that the following key items are correct:<br>
    • Passenger names must be the same as on the passport (International Travel) OR any government-approved photo ID
    proof for Domestic travel.<br>
    • We advise all passengers to ensure to have all travel documents including passports, and required visas issued and
    presented at the time of travel.<br>
    • All passengers are recommended to be present at the airport 3 hours before departure for international departures,
    and 2 before domestic travel.<br>
    • All international flights must be confirmed 72 hours before departure.<br>
    • Review departure/arrival dates, times, origin/destination cities, stopovers, and connections.
</p>


<p>
    Airline tickets are non-refundable, non-changeable, and non-cancellable in most cases. An airline may allow a ticket
    to be changed for a fee, plus the increased cost of the new ticket.
</p>


<h4><strong>For Changes Query:</strong></h4>

<p>
    Call us at <strong>+1 888-476-0932</strong> to make any kind of changes in the itinerary. Any changes to the
    itinerary should be done prior to departure of the flight. The airline's rules will be quoted to the passenger
    before processing any modification to the itinerary which will include penalty, supplier fee and fare difference.
    Please note some reservations will be non-refundable and non-changeable. Additionally, once change is processed the
    add collect will be non-refundable and non-transferable.
</p>

<h4><strong>For Cancellations and Refunds:</strong></h4>

<p>
    Call us at <strong>+1 888-476-0932</strong>. Booking should be cancelled at least 24 hours before the scheduled
    departure time of your flight to avoid a no-show. Cancellations can only be processed over the phone. Please note
    cancellation should be processed 24 hours prior to the departure of the flight. Additionally, some reservations will
    be non-refundable and non-changeable. Refund of any reservation will depend upon the fare rules of the ticketed fare
    and refund/cancellation penalty and supplier fees. Cancellation/refund penalty can be a new charge or can be
    adjusted from an existing ticket value based on the type of itinerary booked and fare rules involved.
</p>

<p>
    Any ticket refund after 24 hours of booking may take up to two billing cycles from the date of refund processed. If
    flights are not cancelled before scheduled departure time, the entire money gets forfeited. Refunds are always
    issued to the original form of payment and refund credit will appear on one of the next two billing statements
    depending upon the bank processing time and the billing cycle of the credit card company. In some cases, it may be
    more depending upon airlines or consolidators involved and on type of booking.
</p>

<h4><strong>Seat Assignments:</strong></h4>

<p>
    Most airlines have restricted rules for advance seat assignment and can only be done with a fee. Some fare
    restrictions only allow seat assignment at the airport during the time of check-in. Please refer to each operating
    airline for the most restricted rules. Call us at <strong>+1 888-476-0932</strong> for seat assignment, if
    applicable.
</p>

<h4><strong>Baggage Policy:</strong></h4>

<p>
    Your reservation may have a restricted baggage allowance and some airlines may charge an additional fee for each
    allowed checked-in or carry-on bag. Please refer to each operating airline for the most restricted rules. Call us at
    <strong>+1 888-476-0932</strong> for baggage, if applicable.
</p>

<h4><strong>Visa/Travel Documents:</strong></h4>

<p>
    All customers are advised to verify travel documents (transit visa/entry visa) for the country through which they
    are transiting or entering. We will not be responsible if proper travel documents are not available, and you are
    denied entry or transit into a Country. We request you to consult the embassy of the country(s) you are visiting or
    transiting through. Please visit TSA for any questions regarding this, as well as information on check-in procedures
    and airport security.
</p>

<h4><strong>Check-In:</strong></h4>

<p>
    We recommend arriving at the airport 3 hours before your departure for international flights and 2 hours before your
    departure for domestic flights. For the most updated check-in rules, please contact airlines or TSA directly.
</p>

<p>
    Still have questions? Call us at <strong>+1 888-476-0932</strong>. Our agents are available 24 hours a day, 7 days a
    week to assist you. You can also email us at <strong>support@travelomile.com</strong>.
</p>

<p><br></p>

<p>
    We value your business and look forward to serving your travel needs in the near future.
</p>

<p><br></p>

<p>
    Best Regards<br>
    <strong>Reservation Desk</strong><br>
    <strong>Travelomile</strong>
</p>
