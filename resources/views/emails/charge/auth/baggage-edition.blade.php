    <h2 style="color: #1f2937; margin-bottom: 8px;">Baggage Charge Authorization</h2>
    <p style="color: #6b7280; margin: 16px 0;">Confirmation #: {{ $booking->airline_pnr ? $booking->airline_pnr : $booking->gk_pnr }}</p>

    <p>Dear {{ $booking->customer_name ?? 'Passenger' }},</p>
    
    <p>Thank you for choosing us. We are confirming the baggage charges for your upcoming flights with {{ $booking->segments->first()?->airline_name ?? 'the airline' }}. Your additional baggage fees are detailed below and require your authorization.</p>
    
    <p><strong>Total Baggage Charge:</strong> {{ $booking->currency ?? 'USD' }} {{ number_format($booking->amount_charged, 2) }} (inclusive of taxes and fees)</p>
    
    <p>By replying to this email with <strong>'I Agree'</strong>, you authorize the baggage charges to be charged to the payment method ending in ******{{ $booking->cards->first()?->card_last_four ?? '****' }}.</p>

    <hr style="border: none; border-top: 1px solid #e5e7eb; margin: 24px 0;">
    
    <h3 style="color: #1f2937; font-size: 16px; margin-top: 24px;">Baggage Charge Breakdown</h3>

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

        <p style="color: #374151; margin: 8px 0;">
            {{ $index + 1 }}. <strong>{{ $merchantName }}</strong> - Baggage Fees<br>
            {{ $booking->currency ?? 'USD' }} {{ number_format((float) $amount, 2) }}
        </p>
    @endforeach

    <hr style="border: none; border-top: 1px solid #e5e7eb; margin: 24px 0;">
    
    <h3 style="color: #1f2937; font-size: 16px;">Passenger Information</h3>

    <table style="width: 100%; border-collapse: collapse; margin: 16px 0;">
        <thead>
            <tr style="background-color: #f3f4f6; border-bottom: 2px solid #e5e7eb;">
                <th style="padding: 12px; text-align: left; font-weight: 600; font-size: 14px;">Passenger</th>
                <th style="padding: 12px; text-align: left; font-weight: 600; font-size: 14px;">Type</th>
                <th style="padding: 12px; text-align: left; font-weight: 600; font-size: 14px;">DOB</th>
                <th style="padding: 12px; text-align: left; font-weight: 600; font-size: 14px;">Gender</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($booking->passengers as $index => $passenger)
                <tr style="border-bottom: 1px solid #e5e7eb;">
                    <td style="padding: 12px;">{{ $passenger->first_name }} {{ $passenger->last_name }}</td>
                    <td style="padding: 12px;">{{ $passenger->type ?? 'ADT' }}</td>
                    <td style="padding: 12px;">
                        {{ $passenger->dob ? \Carbon\Carbon::parse($passenger->dob)->format('M d, Y') : '-' }}
                    </td>
                    <td style="padding: 12px;">{{ $passenger->gender ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <hr style="border: none; border-top: 1px solid #e5e7eb; margin: 24px 0;">
    
    <h3 style="color: #1f2937; font-size: 16px;">Flight Itinerary</h3>

    <table style="width: 100%; border-collapse: collapse; margin: 16px 0;">
        <thead>
            <tr style="background-color: #f3f4f6; border-bottom: 2px solid #e5e7eb;">
                <th style="padding: 12px; text-align: left; font-weight: 600; font-size: 14px;">Flight</th>
                <th style="padding: 12px; text-align: left; font-weight: 600; font-size: 14px;">Route</th>
                <th style="padding: 12px; text-align: left; font-weight: 600; font-size: 14px;">Departure</th>
                <th style="padding: 12px; text-align: left; font-weight: 600; font-size: 14px;">Arrival</th>
                <th style="padding: 12px; text-align: left; font-weight: 600; font-size: 14px;">PNR</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($booking->segments as $index => $segment)
                <tr style="border-bottom: 1px solid #e5e7eb;">
                    <td style="padding: 12px;"><strong>{{ $segment->airline_name ?? '-' }}</strong> {{ $segment->flight_number ?? '-' }}</td>
                    <td style="padding: 12px;">{{ $segment->from_airport ?? '-' }} → {{ $segment->to_airport ?? '-' }}</td>
                    <td style="padding: 12px;">
                        {{ $segment->departure_date ? \Carbon\Carbon::parse($segment->departure_date)->format('M d, Y') : '-' }}
                    </td>
                    <td style="padding: 12px;">
                        {{ $segment->arrival_date ? \Carbon\Carbon::parse($segment->arrival_date)->format('M d, Y') : '-' }}
                    </td>
                    <td style="padding: 12px;">{{ $segment->airline_pnr ? $segment->airline_pnr : $booking->gk_pnr }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <hr style="border: none; border-top: 1px solid #e5e7eb; margin: 24px 0;">
    
    <h3 style="color: #1f2937; font-size: 16px;">Payment Summary</h3>

    <table style="width: 100%; border-collapse: collapse; margin: 16px 0; background-color: #f9fafb;">
        <tbody>
            <tr style="border-bottom: 1px solid #e5e7eb;">
                <td style="padding: 12px; font-weight: 600; background-color: #f3f4f6; width: 40%;">Cardholder Name:</td>
                <td style="padding: 12px;">{{ $booking->cards->first()?->card_holder_name ?? 'N/A' }}</td>
            </tr>
            <tr style="border-bottom: 1px solid #e5e7eb;">
                <td style="padding: 12px; font-weight: 600; background-color: #f3f4f6;">Card Type:</td>
                <td style="padding: 12px;">{{ $booking->cards->first()?->card_type ?? 'N/A' }}</td>
            </tr>
            <tr style="border-bottom: 1px solid #e5e7eb;">
                <td style="padding: 12px; font-weight: 600; background-color: #f3f4f6;">Card (Last 4):</td>
                <td style="padding: 12px;">{{ $booking->cards->first()?->card_last_four ?? 'N/A' }}</td>
            </tr>
            <tr style="border-bottom: 1px solid #e5e7eb;">
                <td style="padding: 12px; font-weight: 600; background-color: #f3f4f6;">Email:</td>
                <td style="padding: 12px;">{{ $booking->customer_email }}</td>
            </tr>
            <tr style="border-bottom: 1px solid #e5e7eb; background-color: #fef3c7;">
                <td style="padding: 12px; font-weight: 600; background-color: #fcd34d;">Baggage Charge:</td>
                <td style="padding: 12px; font-weight: 600; color: #92400e;">
                    {{ $booking->currency ?? 'USD' }} {{ number_format($booking->amount_charged, 2) }}
                </td>
            </tr>
            <tr style="border-bottom: 1px solid #e5e7eb;">
                <td style="padding: 12px; font-weight: 600; background-color: #f3f4f6;">Authorization Date:</td>
                <td style="padding: 12px;">{{ \Carbon\Carbon::now()->format('M d, Y') }}</td>
            </tr>
        </tbody>
    </table>

    <hr style="border: none; border-top: 1px solid #e5e7eb; margin: 24px 0;">
    
    <h3 style="color: #1f2937; font-size: 16px;">Baggage Information</h3>

    <p style="margin: 12px 0;">
        <strong>Baggage Allowance:</strong> Please check your airline confirmation for specific baggage allowances included with your ticket. These charges are for additional baggage beyond your included allowance.
    </p>

    <p style="margin: 12px 0;">
        <strong>Processing:</strong> This charge will be added to your booking and displayed on your airline confirmation. The baggage will be registered with the airline at check-in.
    </p>

    <p style="margin: 12px 0;">
        <strong>Refund Policy:</strong> Baggage charges are non-refundable once the baggage has been checked in with the airline. Cancellations must be made before your scheduled departure time.
    </p>

    <hr style="border: none; border-top: 1px solid #e5e7eb; margin: 24px 0;">
    
    <h3 style="color: #1f2937; font-size: 16px;">Important Notes</h3>

    <ul style="margin: 12px 0; padding-left: 20px; color: #374151;">
        <li>Verify passenger names match your travel documents exactly</li>
        <li>Each piece of baggage must not exceed airline size and weight restrictions</li>
        <li>Excess baggage charges may apply at the airport if limits are exceeded</li>
        <li>For international flights, baggage policies may vary by airline</li>
    </ul>

    <hr style="border: none; border-top: 1px solid #e5e7eb; margin: 24px 0;">
    
    <h3 style="color: #1f2937; font-size: 16px;">Need Assistance?</h3>

    <p style="margin: 12px 0;">
        <strong>Contact our support team:</strong>
    </p>
    <ul style="margin: 8px 0; padding-left: 20px;">
        <li><strong>Phone:</strong> {{ $booking->agencyMerchant->contact_number ?? '+1 888-476-0932' }} (24/7)</li>
        <li><strong>Email:</strong> {{ $booking->agencyMerchant->support_mail ?? 'support@travelomine.com' }}</li>
        <li><strong>Hours:</strong> Available 24/7 for your convenience</li>
    </ul>

    <p style="margin-top: 16px; color: #6b7280; font-size: 13px;">
        <em>
            {{ $booking->agency_merchant_name }} is an independent travel agency. We are not affiliated with any airline. 
            Baggage charges are set by the airline and may appear separately on your billing statement from {{ $booking->agency_merchant_name }} and the airline as per our payment arrangement.
        </em>
    </p>
