<p>
    This email confirms the e-credit refund process for your booking.
</p>

<p>
    Greetings of the day !!
</p>

<p>
    As per our conversation and agreement, we have processed a refund for your {{ $booking->segments->first()?->airline_name ?? 'the airline' }} E-credits under Confirmation <strong>#{{ $booking->booking_reference }}</strong>. Please see the details below.
</p>

<p><br></p>

<p>
    <strong>Total cost for all passengers:</strong> USD {{ number_format($booking->total_cost, 2) }} (all incl. taxes and fees).
</p>

<p><br></p>

<p>
    <strong><u>Please Note:</u></strong> You will receive a refund to the same form of payment ({{ $booking->cards->first()?->card_type ?? 'Card' }} ending in {{ substr($booking->cards->first()?->card_number ?? '****', -4) }}) within 7-14 business days.
</p>

<p><br></p>

<p>
    As per our telephonic conversation I, <strong>{{ $booking->passengers->first()?->first_name ?? 'Name' }} {{ $booking->passengers->first()?->middle_name ?? '' }} {{ $booking->passengers->first()?->last_name ?? 'Last' }}</strong>, authorize <strong>{{ $booking->segments->first()?->airline_name ?? 'the airline' }}/Travelomile</strong> to process the above-mentioned charges under their respective merchants for charging my <strong>{{ $booking->cards->first()?->card_number ?? '****' }}</strong> card for refund process the below-mentioned E-credit with <strong>{{ $booking->segments->first()?->airline_name ?? 'the airline' }}</strong>.
</p>

<p><br></p>

<p>
    This payment authorization is for the amount indicated above and is valid for one-time use only. I certify that I am <strong>{{ $booking->passengers->first()?->first_name ?? 'Name' }} {{ $booking->passengers->first()?->middle_name ?? '' }} {{ $booking->passengers->first()?->last_name ?? 'Last' }}</strong>, an authorized user of this card and that I will not dispute the payment with my credit/debit card company/bank.
</p>

<p><br></p>

<p>
    <strong>Kindly confirm your acceptance of the terms and agreement to the declaration by replying to this email with 'I Agree' or 'I Authorize'.</strong>
</p>

<p><br></p>

<h4><strong>Charges Description:</strong></h4>

<p><br></p>

<p>
    1. USD {{ number_format($booking->total_cost, 2) }} ({{ $booking->segments->first()?->airline_name ?? 'the airline' }}/Travelomile, incl. the taxes and fees)
</p>

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
                <td style="padding: 12px 16px;">{{ $passenger->first_name }}</td>
                <td style="padding: 12px 16px;">{{ $passenger->middle_name ?? '-' }}</td>
                <td style="padding: 12px 16px;">{{ $passenger->last_name }}</td>
                <td style="padding: 12px 16px;">{{ $passenger->gender ?? '-' }}</td>
                <td style="padding: 12px 16px;">{{ $passenger->dob ?? '-' }}</td>
                <td style="padding: 12px 16px;">{{ $passenger->price ? 'USD ' . number_format($passenger->price, 2) : 'N/A' }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

<p><br></p>

<h4><strong>{{ $booking->segments->first()?->airline_name ?? 'Airline' }} E Credit Refund:</strong></h4>

<p><br></p>

@foreach($booking->passengers as $index => $passenger)
    <p>
        <strong>{{ $passenger->first_name }} {{ $passenger->last_name }}</strong>
    </p>
    <p>
        <strong>#{{ $passenger->ecredit_reference_number ?? '0000000000000' }}</strong>
    </p>
    <p>
        <strong>Amount: USD{{ number_format($passenger->ecredit_amount ?? 0, 2) }}</strong>
    </p>
    @if($index < count($booking->passengers) - 1)
        <p><br></p>
    @endif
@endforeach

<p><br></p>

<h4><strong>Purchase Summary:</strong></h4>

<p><br></p>

<h4><strong>Payment Type - Credit/Debit Card Authorization</strong></h4>

<table style="width: 100%; border-collapse: collapse; margin: 16px 0; background-color: #f9fafb;">
    <tbody>
        <tr style="border-bottom: 1px solid #e5e7eb;">
            <td style="padding: 12px 16px; font-weight: 600; background-color: #f3f4f6; width: 40%;">Card Holder Name:</td>
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
            <td style="padding: 12px 16px;">{{ $booking->cards->first()?->phone ?? 'N/A' }}</td>
        </tr>
        <tr style="border-bottom: 1px solid #e5e7eb;">
            <td style="padding: 12px 16px; font-weight: 600; background-color: #f3f4f6;">Email:</td>
            <td style="padding: 12px 16px;">{{ $booking->customer_email }}</td>
        </tr>
        <tr style="border-bottom: 1px solid #e5e7eb;">
            <td style="padding: 12px 16px; font-weight: 600; background-color: #f3f4f6;">Total Amount:</td>
            <td style="padding: 12px 16px; font-weight: 600; color: #059669;">USD {{ number_format($booking->total_cost, 2) }}</td>
        </tr>
        <tr style="border-bottom: 1px solid #e5e7eb;">
            <td style="padding: 12px 16px; font-weight: 600; background-color: #f3f4f6;">Transaction Date:</td>
            <td style="padding: 12px 16px;">{{ \Carbon\Carbon::now()->format('M d, Y') }}</td>
        </tr>
    </tbody>
</table>

<p><br></p>

<h4><strong>Please Note:</strong></h4>

<p>
    Review the names, dates, cities, and departure/arrival times carefully.
</p>

<p><br></p>

<h4><strong>Baggage fees may apply.</strong></h4>

<p>
    Please check with the airline for the most up-to-date baggage policies.
</p>

<p><br></p>

<h4><strong>Important:</strong></h4>

<p>
    Your e-tickets will be sent to you via email within 24 hours, or sooner if there is no delay from the airline's side. Please note that fares are not guaranteed until payment is received and tickets are issued. If there are any restrictions, updates, or concerns from the airline, we will contact you via email or phone. If you wish to make any changes to this itinerary after the tickets have been issued, you will be responsible for any additional penalties, fare differences, and applicable fees.
</p>

<p><br></p>

<h4><strong>Note:</strong></h4>

<p>
    As agreed, your credit card may be charged in split transactions, not exceeding the total amount. All transactions are for service fees and are <strong>100% non-refundable</strong>. Airline tickets are non-refundable; however, you may be eligible for a refund within 24 hours of purchase, depending on the airline's policy.
</p>

<p><br></p>

<h4><strong>Disclaimer:</strong></h4>

<p>
    Travelomile is an independent travel Agency with no third-party association. We shall not be associated or considered as an airline or an ally of any of the airlines or brands. Travelomile is shown on your bank account details in most cases. However, sometimes we have to split the payment with the airline. Travelomile and the airline or another company of that organization both will appear as recipients on your account. All the service fees and convenience fees are non-refundable.
</p>

<p><br></p>

<h4><strong>For Assistance:</strong></h4>

<p>
    In case of any discrepancies or if an amendment is required, please contact us within 24 hours at <strong>+1 888-476-0932</strong> or email us at <strong>reservation@travelomile.com</strong>. We will be happy to assist you.
</p>

<p><br></p>

<h4><strong>Important Information:</strong></h4>

<p>
    Please review your itinerary carefully to ensure that the following key items are correct:<br>
    • Passenger names must be the same as on the passport (International Travel) OR any government-approved photo ID proof for Domestic travel.<br>
    • We advise all passengers to ensure to have all travel documents including passports, and required visas issued and presented at the time of travel.<br>
    • All passengers are recommended to be present at the airport 3 hours before departure for international departures, and 2 before domestic travel.<br>
    • All international flights must be confirmed 72 hours before departure.<br>
    • Review departure/arrival dates, times, origin/destination cities, stopovers, and connections.
</p>

<p><br></p>

<p>
    <strong>If you are notified that your credit card was declined, please call us immediately at +1 888-476-0932.</strong>
</p>

<p><br></p>

<p>
    At least one adult must accompany children under the age of 18. Children aged 12 and above are considered adults for pricing purposes. Airline tickets are non-refundable, non-changeable, and non-cancellable in most cases. However, some airlines may allow ticket changes for a fee plus any fare difference.
</p>

<p><br></p>

<h4><strong>For Changes Queries:</strong></h4>

<p>
    Call us at <strong>+1 888-476-0932</strong> to make any changes to your itinerary. All changes must be made prior to the flight's departure. The airline's rules, including penalties, supplier fees, and fare differences, will be communicated before any modifications are processed. Please note that some reservations may be non-refundable and non-changeable. Additionally, once a change is processed, any additional amount collected will be non-refundable and non-transferable.
</p>

<p><br></p>

<h4><strong>For Cancellations and Refunds:</strong></h4>

<p>
    Call us at <strong>+1 888-476-0932</strong>. Bookings must be canceled at least 24 hours before the scheduled departure time to avoid a no-show. Cancellations can only be processed over the phone. Please note that some reservations are non-refundable and non-changeable. Refunds depend on the fare rules, cancellation penalties, and supplier fees. Cancellation/refund penalties may be charged as a new fee or deducted from the ticket value based on the itinerary and fare rules.
</p>

<p><br></p>

<p>
    Refunds processed after 24 hours of booking may take up to two billing cycles to appear on your statement. If flights are not canceled before the scheduled departure time, the entire amount will be forfeited. Refunds are always issued to the original form of payment and usually appear within one or two billing statements, depending on your bank and credit card company. Processing times may vary depending on the airline or consolidator and the type of booking.
</p>

<p><br></p>

<h4><strong>Seat Assignments:</strong></h4>

<p>
    Most airlines have restrictions on advance seat assignments, which often incur a fee. Some fare types only allow seat assignment during check-in at the airport. Please refer to the operating airline for specific rules. Call us at <strong>+1 888-476-0932</strong> for assistance with seat assignments, if applicable.
</p>

<p><br></p>

<h4><strong>Baggage Policy:</strong></h4>

<p>
    Your reservation may have restricted baggage allowances, and some airlines charge additional fees for checked or carry-on bags. Please consult the operating airline for baggage rules or call us at <strong>+1 888-476-0932</strong> for more information.
</p>

<p><br></p>

<h4><strong>Visa/Travel Documents:</strong></h4>

<p>
    All customers are advised to verify visa requirements (transit or entry visas) for countries they are transiting through or entering. We are not responsible if you are denied entry or transit due to lack of proper documents. Please consult the embassy of the relevant country or visit the TSA website for questions regarding travel documents, check-in procedures, and airport security.
</p>

<p><br></p>

<h4><strong>Check-In:</strong></h4>

<p>
    We recommend arriving at the airport 3 hours before international flights and 2 hours before domestic flights. For the latest check-in guidelines, please contact the airline or TSA directly.
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
    Best Regards<br>
    <strong>Reservation Desk</strong><br>
    Travelomile
</p>
