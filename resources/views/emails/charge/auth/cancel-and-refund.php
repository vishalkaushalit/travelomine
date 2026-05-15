    <h2 style="color: #1f2937; margin-bottom: 8px;">Booking Cancellation & Refund Confirmation</h2>
    <p style="color: #6b7280; margin: 16px 0;">Booking Reference: {{ $booking->airline_pnr ? $booking->airline_pnr : $booking->gk_pnr }}</p>

    <p>Dear {{ $booking->customer_name ?? 'Passenger' }},</p>
    
    <p>Thank you for contacting us. We have processed the cancellation of your booking with {{ $booking->segments->first()?->airline_name ?? 'the airline' }}. Please find the refund details below.</p>
    
    <p style="background-color: #fef3c7; padding: 12px; border-left: 4px solid #f59e0b; border-radius: 4px; margin: 16px 0;">
        <strong style="color: #92400e;">Refund Amount:</strong> <span style="color: #78350f; font-size: 18px;">{{ $booking->currency ?? 'USD' }} {{ number_format($booking->amount_charged, 2) }}</span>
    </p>
    
    <p>This refund will be processed to your original payment method ending in ******{{ $booking->cards->first()?->card_last_four ?? '****' }}. By replying to this email with <strong>'I Agree'</strong>, you confirm acceptance of these terms.</p>

    <hr style="border: none; border-top: 1px solid #e5e7eb; margin: 24px 0;">
    
    <h3 style="color: #1f2937; font-size: 16px; margin-top: 24px;">Refund Breakdown</h3>

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
            {{ $index + 1 }}. <strong>{{ $merchantName }}</strong><br>
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
    
    <h3 style="color: #1f2937; font-size: 16px;">Cancelled Flights</h3>

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
    
    <h3 style="color: #1f2937; font-size: 16px;">Refund Summary</h3>

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
            <tr style="border-bottom: 1px solid #e5e7eb; background-color: #dcfce7;">
                <td style="padding: 12px; font-weight: 600; background-color: #bbf7d0;">Refund Amount:</td>
                <td style="padding: 12px; font-weight: 600; color: #15803d;">
                    {{ $booking->currency ?? 'USD' }} {{ number_format($booking->amount_charged, 2) }}
                </td>
            </tr>
            <tr style="border-bottom: 1px solid #e5e7eb;">
                <td style="padding: 12px; font-weight: 600; background-color: #f3f4f6;">Cancellation Date:</td>
                <td style="padding: 12px;">{{ \Carbon\Carbon::now()->format('M d, Y') }}</td>
            </tr>
        </tbody>
    </table>

    <hr style="border: none; border-top: 1px solid #e5e7eb; margin: 24px 0;">
    
    <h3 style="color: #1f2937; font-size: 16px;">Cancellation & Refund Policy</h3>

    <p style="margin: 12px 0;">
        <strong>Refund Processing Timeline:</strong> Your refund will be processed within 24 hours. Please allow 1-2 billing cycles for the amount to appear in your account. Processing times may vary depending on your bank and credit card company.
    </p>

    <p style="margin: 12px 0;">
        <strong>Non-Refundable Fees:</strong> Service charges and convenience fees are non-refundable. The refund amount reflects the airline's cancellation policy terms.
    </p>

    <p style="margin: 12px 0;">
        <strong>Cancellation Confirmation:</strong> Your airline cancellation confirmation will be sent separately. Please retain all confirmation numbers for your records.
    </p>

    <p style="margin: 12px 0;">
        <strong>Note:</strong> Some fare types may be subject to airline penalties and restrictions. The refund amount shown reflects applicable deductions per the airline's policy.
    </p>

    <hr style="border: none; border-top: 1px solid #e5e7eb; margin: 24px 0;">
    
    <h3 style="color: #1f2937; font-size: 16px;">Important Information</h3>

    <ul style="margin: 12px 0; padding-left: 20px; color: #374151;">
        <li>Refunds are issued to the original form of payment</li>
        <li>Do not dispute the charge with your bank or credit card company</li>
        <li>Keep your cancellation confirmation for reference</li>
        <li>Contact us immediately if you do not receive your refund within 2 billing cycles</li>
        <li>Some airline tickets are non-refundable; check the original fare rules</li>
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
            The charges may appear separately on your billing statement. All service fees and convenience fees are non-refundable.
        </em>
    </p>
