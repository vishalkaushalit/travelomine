    <h3>Authorization for {{ $booking->segments->first()?->airline_name ?? 'the airline' }} exchange/upgrade Confirmation.
    </h3>

    <p>Dear {{ $booking->customer_name ?? 'Passeneger' }},</p>
    <p>Greetings of the day !!</p>
    <p>As per our conversation and as agreed, we have booked your exchange/upgrade with
        {{ $booking->segments->first()?->airline_name ?? 'the airline' }} under
        Confirmation {{ $booking->airline_pnr ? $booking->airline_pnr : $booking->gk_pnr }}. Please see the details
        below.
    </p>
    <p>
        Total cost for all passengers: {{ $booking->currency ?? 'USD' }}
        {{ number_format($booking->amount_charged, 2) }} (all incl. taxes and fees).
    </p>

    <p>
        As per our telephonic conversation I,<b> {{ $booking->customer_name ?? '' }}</b>, authorize
        <b>
            {{ $booking->segments->first()?->airline_name ?? 'the airline' }} /
            {{ $booking->agency_merchant_name ?? '' }}
        </b>
        to process the above-mentioned charges under their respective merchants for charging my
        ******{{ $booking->cards->first()?->card_last_four ?? '****' }} card for the booking the
        below-mentioned
        itinerary
        with {{ $booking->segments->first()?->airline_name ?? 'the airline' }}.
    </p>
    <p>
        This payment authorization is for the amount indicated above and is valid for one-time use only. I certify that
        I am
        <b>{{ $booking->customer_name ?? '' }}</b>, an authorized user of this card and
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

    <table style="width: 100%; border: 1px solid #000; border-collapse: collapse; margin: 16px 0;">
        <thead style="border: 1px solid #000;">
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

    <h4>Purchase Summary:</h4>

    <h6>Payment Type - Credit/Debit Card Authorization</h6>

    <table style="width: 100%; border-collapse: collapse; margin: 16px 0; background-color: #f9fafb;">
        <tbody>
            <tr style="border-bottom: 1px solid #e5e7eb;">
                <td style="padding: 12px 16px; font-weight: 600; background-color: #f3f4f6; width: 40%;">Card Holder
                    Name:
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
