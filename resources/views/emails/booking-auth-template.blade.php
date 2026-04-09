<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Authorization</title>
</head>

<body style="margin:0; padding:0; background-color:#0b1220; font-family:Arial, Helvetica, sans-serif; color:#e5e7eb;">

    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0"
        style="background-color:#0b1220; margin:0; padding:0;">
        <tr>
            <td align="center" style="padding:30px 15px;">

                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0"
                    style="max-width:760px; background-color:#111827; border-radius:18px; overflow:hidden; border:1px solid #1f2937;">

                    {{-- Header --}}
                    <tr>
                        <td
                            style="background:linear-gradient(135deg,#111827 0%,#1f2937 100%); padding:32px 30px; text-align:center; border-bottom:1px solid #243042;">
                            <div style="font-size:28px; line-height:36px; font-weight:700; color:#ffffff;">
                                Booking Confirmation

                            </div>
                            <p style="font-size:14px; color:#9ca3af; line-height:24px;">
                                Your reservation and payment authorization details are listed below.
                            </p>
                        </td>
                    </tr>

                    {{-- Booking Ref --}}
                    <tr>
                        <td style="padding:24px 30px 10px 30px;">
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0"
                                style="background-color:#0f172a; border:1px solid #1e293b; border-radius:14px;">
                                <tr>
                                    <td style="padding:20px 24px; text-align:center;">
                                        <div
                                            style="font-size:12px; text-transform:uppercase; letter-spacing:1.5px; color:#94a3b8; margin-bottom:8px;">
                                            Confirmation Number
                                        </div>
                                        <div style="font-size:24px; font-weight:700; color:#f8fafc; margin-bottom:6px;">
                                            {{ $booking->booking_gk_pnr }}
                                        </div>
                                        <div style="font-size:14px; color:#60a5fa;">
                                            Total Cost for all passengers: {{ $booking->currency }}
                                            {{ number_format($booking->amount_charged, 2) }}
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    {{-- Greeting --}}
                    <tr>
                        <td style="padding:20px 30px 10px 30px;">
                            <div style="font-size:16px; color:#e5e7eb; line-height:28px;">
                                Dear <span style="color:#ffffff; font-weight:700;">{{ $booking->customer_name }}</span>,
                            </div>
                            <div style="font-size:15px; color:#cbd5e1; line-height:28px; margin-top:12px;">
                                Greetings of the day. As per our conversation and agreement, we have booked your flight
                                reservation under confirmation number
                                <span style="color:#ffffff; font-weight:700;">{{ $booking->booking_gk_pnr }}</span>.
                                The total cost for all passengers is
                                <span style="color:#93c5fd; font-weight:700;">{{ $booking->currency }}
                                    {{ number_format($booking->amount_charged, 2) }}</span>
                                including applicable taxes and fees.
                            </div>
                        </td>
                    </tr>

                    {{-- Authorization --}}
                    <tr>
                        <td style="padding:10px 30px;">
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0"
                                style="background-color:#111827; border:1px solid #253041; border-radius:14px;">
                                <tr>
                                    <td style="padding:22px 24px;">
                                        <div
                                            style="font-size:17px; font-weight:700; color:#ffffff; margin-bottom:12px;">
                                            Payment Authorization
                                        </div>
                                        <div style="font-size:14px; line-height:26px; color:#cbd5e1;">
                                            As per our telephonic conversation, I,
                                            <span
                                                style="font-weight:700; color:#ffffff;">{{ $booking->customer_name }}</span>,
                                            authorize Travelomile to process the above-mentioned charges under their
                                            respective merchants for charging my {{ $booking->card_holder_name }}, card
                                            ending in
                                            <span
                                                style="font-weight:700; color:#93c5fd;">{{ $booking->card_last_four }}</span>
                                            for the amount of
                                            <span style="font-weight:700; color:#93c5fd;">{{ $booking->currency }}
                                                {{ number_format($booking->amount_charged, 2) }}</span>
                                            for booking a new flight reservation.
                                        </div>
                                        <div style="font-size:14px; line-height:26px; color:#cbd5e1; margin-top:14px;">
                                            This payment authorization is valid for one-time use only. I certify that I
                                            am an authorized user of this card and that I will not dispute the payment
                                            with my bank.
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    {{-- Charges --}}
                    <tr>
                        <td style="padding:10px 30px;">
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
                                <tr>
                                    <td colspan="2"
                                        style="padding:0 0 12px 0; font-size:17px; font-weight:700; color:#ffffff;">
                                        Charge Breakdown
                                    </td>
                                </tr>
                                <tr>
                                    <td style="width:50%; padding:0 8px 0 0;">
                                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0"
                                            border="0"
                                            style="background-color:#0f172a; border:1px solid #1e293b; border-radius:14px;">
                                            <tr>
                                                <td style="padding:18px;">
                                                    <div style="font-size:13px; color:#94a3b8; margin-bottom:8px;">
                                                        {{ $booking->airline_merchant_name }}
                                                    </div>
                                                    <div style="font-size:22px; font-weight:700; color:#ffffff;">
                                                        {{ $booking->currency }}
                                                        {{ number_format($booking->amount_paid_airline, 2) }}
                                                    </div>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                    <td style="width:50%; padding:0 0 0 8px;">
                                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0"
                                            border="0"
                                            style="background-color:#0f172a; border:1px solid #1e293b; border-radius:14px;">
                                            <tr>
                                                <td style="padding:18px;">
                                                    <div style="font-size:13px; color:#94a3b8; margin-bottom:8px;">
                                                        {{ $booking->agency_merchant_name }} </div>
                                                    <div>
                                                        <p style="font-size:22px; font-weight:700; color:#ffffff;">
                                                            {{ $booking->currency }}
                                                            {{ number_format($booking->total_mco, 2) }}
                                                        </p> <span
                                                            style="font-size:12px; color:#64748b; margin-top:6px; font-wight:400; display:inline-block;">
                                                            Includes all taxes
                                                        </span>
                                                    </div>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    {{-- Passenger Table --}}
                    <tr>
                        <td style="padding:10px 30px;">
                            <div style="font-size:17px; font-weight:700; color:#ffffff; margin-bottom:12px;">
                                Passenger Details
                            </div>
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0"
                                style="border-collapse:collapse; overflow:hidden; border-radius:12px; border:1px solid #253041;">
                                <thead>
                                    <tr style="background-color:#1e293b;">
                                        <th align="left"
                                            style="padding:12px 10px; font-size:12px; color:#cbd5e1; border-bottom:1px solid #253041;">
                                            S.No.</th>
                                        <th align="left"
                                            style="padding:12px 10px; font-size:12px; color:#cbd5e1; border-bottom:1px solid #253041;">
                                            Type</th>
                                        <th align="left"
                                            style="padding:12px 10px; font-size:12px; color:#cbd5e1; border-bottom:1px solid #253041;">
                                            First Name</th>
                                        <th align="left"
                                            style="padding:12px 10px; font-size:12px; color:#cbd5e1; border-bottom:1px solid #253041;">
                                            Last Name</th>
                                        <th align="left"
                                            style="padding:12px 10px; font-size:12px; color:#cbd5e1; border-bottom:1px solid #253041;">
                                            Gender</th>
                                        <th align="left"
                                            style="padding:12px 10px; font-size:12px; color:#cbd5e1; border-bottom:1px solid #253041;">
                                            DOB</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($booking->passengers as $index => $p)
                                        <tr style="background-color:{{ $index % 2 == 0 ? '#111827' : '#0f172a' }};">
                                            <td
                                                style="padding:12px 10px; font-size:13px; color:#e5e7eb; border-bottom:1px solid #1f2937;">
                                                {{ $index + 1 }}</td>
                                            <td
                                                style="padding:12px 10px; font-size:13px; color:#e5e7eb; border-bottom:1px solid #1f2937;">
                                                {{ $p->passenger_type }}</td>
                                            <td
                                                style="padding:12px 10px; font-size:13px; color:#e5e7eb; border-bottom:1px solid #1f2937;">
                                                {{ $p->first_name }}</td>
                                            <td
                                                style="padding:12px 10px; font-size:13px; color:#e5e7eb; border-bottom:1px solid #1f2937;">
                                                {{ $p->last_name }}</td>
                                            <td
                                                style="padding:12px 10px; font-size:13px; color:#e5e7eb; border-bottom:1px solid #1f2937;">
                                                {{ ucfirst($p->gender) }}</td>
                                            <td
                                                style="padding:12px 10px; font-size:13px; color:#e5e7eb; border-bottom:1px solid #1f2937;">
                                                {{ \Carbon\Carbon::parse($p->dob)->format('M-d-Y') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </td>
                    </tr>

                    {{-- Card Details --}}
                    <tr>
                        <td style="padding:10px 30px;">
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0"
                                border="0"
                                style="background-color:#111827; border:1px solid #253041; border-radius:14px;">
                                <tr>
                                    <td style="padding:22px 24px;">
                                        <div
                                            style="font-size:17px; font-weight:700; color:#ffffff; margin-bottom:14px;">
                                            Card & Billing Details
                                        </div>
                                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0"
                                            border="0">
                                            <tr>
                                                <td style="padding:6px 0; font-size:14px; color:#94a3b8; width:170px;">
                                                    Card Holder</td>
                                                <td style="padding:6px 0; font-size:14px; color:#ffffff;">
                                                    {{ optional($booking->primary_card)->card_holder_name ?: 'N/A' }}
                                                </td>
                                                {{-- $booking->primary_card->card_holder_name ?? 'N/A' }}</td> --}}
                                            </tr>

                                            <tr>
                                                <td style="padding:6px 0; font-size:14px; color:#94a3b8;">Card Number
                                                </td>
                                                <td style="padding:6px 0; font-size:14px; color:#ffffff;">
                                                    {{ $booking->card_last_four ?? 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <td style="padding:6px 0; font-size:14px; color:#94a3b8;">Billing
                                                    Address</td>
                                                <td style="padding:6px 0; font-size:14px; color:#ffffff;">
                                                    {{ $booking->billing_address ?? 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <td style="padding:6px 0; font-size:14px; color:#94a3b8;">Email</td>
                                                <td style="padding:6px 0; font-size:14px; color:#ffffff;">
                                                    {{ $booking->customer_email ?? 'N/A' }}</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    {{-- Terms & Policies (Newly Added Section) --}}
                    <tr>
                        <td style="padding:20px 30px;">
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0"
                                border="0"
                                style="background-color:#0f172a; border:1px solid #1e293b; border-radius:14px;">
                                <tr>
                                    <td style="padding:22px 24px; font-size:12px; color:#9ca3af; line-height:20px;">

                                        <div
                                            style="font-size:16px; font-weight:700; color:#ffffff; margin-bottom:16px; border-bottom:1px solid #1e293b; padding-bottom:10px;">
                                            Important Information & Policies
                                        </div>

                                        <div style="margin-bottom:12px;">
                                            <span style="color:#ffffff; font-weight:700;">Please Note:</span> Review
                                            the
                                            names, dates, cities, and departure/arrival times carefully.
                                        </div>

                                        <div style="margin-bottom:12px;">
                                            <span style="color:#ffffff; font-weight:700;">Important:</span> Your
                                            e-tickets will be sent to you via email within 24 hours, or sooner if there
                                            is no delay from the airline’s side. Please note that fares are not
                                            guaranteed until payment is received and tickets are issued. If there are
                                            any restrictions, updates, or concerns from the airline, we will contact you
                                            via email or phone. If you wish to make any changes to this itinerary after
                                            the tickets have been issued, you will be responsible for any additional
                                            penalties, fare differences, and applicable fees.
                                        </div>

                                        <div style="margin-bottom:12px;">
                                            <span style="color:#ffffff; font-weight:700;">Baggage fees may
                                                apply:</span>
                                            Please check with the airline for the most up-to-date baggage policies.
                                        </div>

                                        <div style="margin-bottom:12px;">
                                            <span style="color:#ffffff; font-weight:700;">Note:</span> As agreed, your
                                            credit card may be charged in split transactions, not exceeding the total
                                            amount. All transactions are for service fees and are 100% non-refundable.
                                            Airline tickets are non-refundable; however, you may be eligible for a
                                            refund within 24 hours of purchase, depending on the airline's policy.
                                        </div>

                                        <div style="margin-bottom:12px;">
                                            <span style="color:#ffffff; font-weight:700;">Disclaimer:</span>
                                            {{ $booking->agency_merchant_name }} is an independent travel agency with
                                            no
                                            third-party association.
                                            We shall not be associated or considered as an airline or an ally of any of
                                            the airlines or brands. {{ $booking->agency_merchant_name }} are shown on
                                            your bank account
                                            details in most cases. However, sometimes we have to split the payment with
                                            the airline. {{ $booking->agency_merchant_name }} and the airline or
                                            another
                                            company of that
                                            organization both will appear as recipients on your account. All the service
                                            fees and convenience fees are non-refundable.
                                        </div>

                                        <div style="margin-bottom:12px;">
                                            <span style="color:#ffffff; font-weight:700;">For Assistance:</span> In
                                            case
                                            of any discrepancy and if an amendment is required, please feel free to
                                            contact us at {{ $booking->agency_phone }} or email us at
                                            {{ $booking->agencyMerchant->support_mail ?? 'N/A' }}
                                            within 24 hours and we will be happy to assist you.
                                        </div>

                                        <div
                                            style="margin-top:20px; margin-bottom:8px; font-size:14px; font-weight:700; color:#e5e7eb;">
                                            Review Your Itinerary Carefully:
                                        </div>
                                        <ul style="margin-top:0; padding-left:20px; margin-bottom:16px;">
                                            <li style="margin-bottom:6px;">Passenger names must be the same as on the
                                                passport (International Travel) OR any government-approved photo ID
                                                proof for Domestic travel.</li>
                                            <li style="margin-bottom:6px;">We advise all passengers to ensure to have
                                                all travel documents including passports, and required visas issued and
                                                presented at the time of travel.</li>
                                            <li style="margin-bottom:6px;">All passengers are recommended to be present
                                                at the airport 3 hours before departure for international departures,
                                                and 2 before domestic travel.</li>
                                            <li style="margin-bottom:6px;">All international flights must be confirmed
                                                72 hours before departure.</li>
                                            <li style="margin-bottom:6px;">Review departure/arrival dates, times,
                                                origin/destination cities, stopovers, and connections.</li>
                                        </ul>
                                        <div style="margin-bottom:16px;">
                                            Airline tickets are non-refundable, non-changeable, and non-cancellable in
                                            most cases, an airline may allow a ticket to be changed for a fee, plus the
                                            increased cost of the new ticket.
                                        </div>

                                        <div
                                            style="margin-top:16px; margin-bottom:6px; font-size:14px; font-weight:700; color:#e5e7eb;">
                                            For Changes Query:</div>
                                        <div style="margin-bottom:16px;">
                                            Call us at {{ $booking->agencyMerchant->contact_number ?? 'N/A' }} to make
                                            any kind of changes in the itinerary. Any
                                            changes to the itinerary should be done prior to departure of the flight.
                                            The airline's rules will be quoted to the passenger before processing any
                                            modification to the itinerary which will include penalty, supplier fee and
                                            fare difference. Please note some reservations will be non-refundable and
                                            non-changeable. Additionally, once change is processed the add collect will
                                            be non-refundable and non-transferable.
                                        </div>

                                        <div
                                            style="margin-top:16px; margin-bottom:6px; font-size:14px; font-weight:700; color:#e5e7eb;">
                                            For Cancellations and Refunds:</div>
                                        <div style="margin-bottom:16px;">
                                            Call us at {{ $booking->agencyMerchant->contact_number ?? 'N/A' }}. Booking
                                            should be cancelled at least 24 hours
                                            before the scheduled departure time of your flight to avoid a no-show.
                                            Cancellations can only be processed over the phone. Please note cancellation
                                            should be processed 24 hours prior to the departure of the flight.
                                            Additionally, some reservations will be non-refundable and non-changeable.
                                            Refund of any reservation will depend upon the fare rules of the ticketed
                                            fare and refund/cancellation penalty and supplier fees. Cancellation/refund
                                            penalty can be a new charge or can be adjusted from an existing ticket value
                                            based on the type of itinerary booked and fare rules involved. Any ticket
                                            refund after 24 hours of booking may take up to two billing cycles from the
                                            date of refund processed. If flights are not cancelled before scheduled
                                            departure time, the entire money gets fortified. Refunds are always issued
                                            to the original form of payment and refund credit will appear on one of the
                                            next two billing statements depending upon the bank processing time and the
                                            billing cycle of the credit card company. In some cases, it may be more
                                            depending upon airlines or consolidators involved and on type of booking.
                                        </div>

                                        <div
                                            style="margin-top:16px; margin-bottom:6px; font-size:14px; font-weight:700; color:#e5e7eb;">
                                            Seat Assignments:</div>
                                        <div style="margin-bottom:16px;">
                                            Most airlines have restricted rules for advance seat assignment and can only
                                            be done with a fee. Some fare restrictions only allow seat assignment at the
                                            airport during the time of check-in. Please refer to each operating airline
                                            for the most restricted rules. Call us at +1 888-476-0932 for seat
                                            assignment, if applicable.
                                        </div>

                                        <div
                                            style="margin-top:16px; margin-bottom:6px; font-size:14px; font-weight:700; color:#e5e7eb;">
                                            Baggage Policy:</div>
                                        <div style="margin-bottom:16px;">
                                            Your reservation may have a restricted baggage allowance and some airlines
                                            may charge an additional fee for each allowed checked-in or carry-on bag.
                                            Please refer to each operating airline for the most restricted rules. Call
                                            us at {{ $booking->agencyMerchant->contact_number ?? 'N/A' }} for baggage,
                                            if applicable.
                                        </div>

                                        <div
                                            style="margin-top:16px; margin-bottom:6px; font-size:14px; font-weight:700; color:#e5e7eb;">
                                            Visa/Travel Documents:</div>
                                        <div style="margin-bottom:16px;">
                                            All customers are advised to verify travel documents (transit visa/entry
                                            visa) for the country through which they are transiting or entering. We will
                                            not be responsible if proper travel documents are not available, and you are
                                            denied entry or transit into a Country. We request you to consult the
                                            embassy of the country(s) you are visiting or transiting through. Please
                                            visit TSA for any questions regarding this, as well as information on
                                            check-in procedures and airport security.
                                        </div>

                                        <div
                                            style="margin-top:16px; margin-bottom:6px; font-size:14px; font-weight:700; color:#e5e7eb;">
                                            Check-In:</div>
                                        <div style="margin-bottom:16px;">
                                            We recommend arriving at the airport 3 hours before your departure for
                                            international flights and 2 hours before your departure for domestic
                                            flights. For the most updated check-in rules, please contact airlines or TSA
                                            directly.
                                        </div>

                                        <div
                                            style="margin-top:20px; padding-top:16px; border-top:1px solid #1e293b; color:#e5e7eb;">
                                            <span style="font-weight:700;">Still, have questions?</span> Call us at
                                            {{ $booking->agencyMerchant->contact_number ?? 'N/A' }}. Our agents are
                                            available 24 hours a day, 7 days a week to
                                            assist you. You can also email us at <a
                                                href="mailto:{{ $booking->agencyMerchant->support_mail ?? 'N/A' }}"
                                                style="color:#60a5fa; text-decoration:none;">{{ $booking->agencyMerchant->support_mail ?? 'N/A' }}</a><br><br>
                                            We value your business and look forward to serving your travel needs in the
                                            near future.
                                        </div>

                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    {{-- Footer --}}
                    <tr>
                        <td style="padding:10px 30px 32px 30px;">
                            <div style="font-size:14px; color:#cbd5e1; line-height:26px;">
                                Best Regards,<br>
                                <span style="font-weight:700; color:#ffffff;">Reservation Desk</span><br>

                                @if ($booking->agent)
                                    {{ $booking->agent->name ?? 'N/A' }} ({{ $booking->agent_custom_id ?? 'N/A' }})
                                @else
                                    Support Team
                                @endif
                                <br>
                                Support Contact:
                                {{ $booking->agencyMerchant->contact_number ?? 'N/A' }}
                                <br>
                                Support Email: {{ $booking->agencyMerchant->support_mail ?? 'N/A' }}
                            </div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>

</html>
