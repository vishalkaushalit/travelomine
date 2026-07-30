<p style="margin: 0 0 12px 0;">Dear {{ $booking->customer_name ?? 'Passenger' }},</p>
<p style="margin: 0 0 12px 0;">Greetings of the day !!</p>
<p style="margin: 0 0 12px 0;">As per our conversation and as agreed, we have booked your reservation with
    <strong>{{ $booking->airline_name ?? ($booking->segments->first()?->airline_name ?? 'the airline') }}</strong> under
    Confirmation <strong>{{ $booking->airline_pnr ? $booking->airline_pnr : ($booking->gk_pnr ?: 'N/A') }}</strong>.
    Please see the details below.
</p>

<p style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 12px 16px; margin: 16px 0;">
    <strong>Total cost for all passengers:</strong> <span
        style="color: #16a34a; font-weight: 700; font-size: 16px;">{{ $booking->currency ?? 'USD' }}
        {{ number_format($booking->amount_charged, 2) }}</span> <small style="color: #64748b;">(all incl. taxes and
        fees)</small>
</p>

<p style="margin: 0 0 12px 0;">
    As per our telephonic conversation I, <b>{{ $booking->customer_name ?? '' }}</b>, authorize
    <b>
        {{ $booking->airline_name ?? ($booking->segments->first()?->airline_name ?? 'the airline') }} /
        {{ $booking->agency_merchant_name ?? '' }}
    </b>
    to process the above-mentioned charges under their respective merchants for charging my
    <b>******{{ $booking->cards->first()?->card_last_four ?? '****' }}</b> card for the booking of the below-mentioned
    itinerary with {{ $booking->airline_name ?? ($booking->segments->first()?->airline_name ?? 'the airline') }}.
</p>


<p style="margin: 16px 0 12px 0;">
    This payment authorization is for the amount indicated above and is valid for one-time use only. I certify that
    I am <b>{{ $booking->customer_name ?? '' }}</b>, an authorized user of this card and that I will not dispute the
    payment with my credit/debit card company/bank.
</p>
<p
    style="background: #eff6ff; border-left: 4px solid #3b82f6; padding: 10px 14px; border-radius: 4px; color: #1e40af; font-size: 13px; margin: 16px 0;">
    📌 <strong>Action Required:</strong> Kindly confirm your acceptance of the terms and agreement to the declaration by
    replying to this email with <strong>'I Agree'</strong> or <strong>'I Authorize'</strong>.
</p>

<h4 style="margin: 24px 0 10px 0; color: #0f172a; font-size: 15px; font-weight: 700;">Charges Description:</h4>

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

    <div
        style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 10px 14px; margin-bottom: 8px; font-size: 13px;">
        <strong>Charge {{ $index + 1 }}:</strong> {{ $booking->currency ?? 'USD' }}
        <strong>{{ number_format((float) $amount, 2) }}</strong>
        <span style="color: #64748b;">({{ $merchantName }}, incl. taxes & fees)</span>
    </div>
@endforeach

<h4 style="margin: 24px 0 10px 0; color: #0f172a; font-size: 15px; font-weight: 700;">Passenger Details:</h4>

<div style="width: 100%; overflow-x: auto; margin: 12px 0;">
    <table width="100%" cellpadding="0" cellspacing="0" border="0"
        style="width: 100%; border: 1px solid #e2e8f0; border-collapse: collapse; border-radius: 8px; overflow: hidden; font-size: 12px;">
        <thead>
            <tr style="background-color: #f8fafc; border-bottom: 1px solid #e2e8f0; color: #475569;">
                <th style="padding: 10px 8px; text-align: center; font-weight: 700; border-right: 1px solid #e2e8f0;">#
                </th>
                <th style="padding: 10px 8px; text-align: center; font-weight: 700; border-right: 1px solid #e2e8f0;">
                    Type</th>
                <th style="padding: 10px 8px; text-align: left; font-weight: 700; border-right: 1px solid #e2e8f0;">
                    First Name</th>
                <th style="padding: 10px 8px; text-align: left; font-weight: 700; border-right: 1px solid #e2e8f0;">
                    Middle</th>
                <th style="padding: 10px 8px; text-align: left; font-weight: 700; border-right: 1px solid #e2e8f0;">Last
                    Name</th>
                <th style="padding: 10px 8px; text-align: center; font-weight: 700; border-right: 1px solid #e2e8f0;">
                    Gender</th>
                <th style="padding: 10px 8px; text-align: center; font-weight: 700; border-right: 1px solid #e2e8f0;">
                    DOB</th>
                <th style="padding: 10px 8px; text-align: right; font-weight: 700;">Price</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($booking->passengers as $index => $passenger)
                <tr style="border-bottom: 1px solid #f1f5f9; color: #1e293b;">
                    <td style="padding: 10px 8px; text-align: center; border-right: 1px solid #f1f5f9;">
                        {{ $index + 1 }}</td>
                    <td
                        style="padding: 10px 8px; text-align: center; border-right: 1px solid #f1f5f9; font-weight: 600; color: #4f46e5;">
                        {{ $passenger->type ?? 'ADT' }}</td>
                    <td style="padding: 10px 8px; text-align: left; border-right: 1px solid #f1f5f9; font-weight: 600;">
                        {{ $passenger->first_name }}</td>
                    <td style="padding: 10px 8px; text-align: left; border-right: 1px solid #f1f5f9; color: #64748b;">
                        {{ $passenger->middle_name ?? '-' }}</td>
                    <td style="padding: 10px 8px; text-align: left; border-right: 1px solid #f1f5f9; font-weight: 600;">
                        {{ $passenger->last_name }}</td>
                    <td style="padding: 10px 8px; text-align: center; border-right: 1px solid #f1f5f9;">
                        {{ $passenger->gender ?? '-' }}</td>
                    <td style="padding: 10px 8px; text-align: center; border-right: 1px solid #f1f5f9;">
                        {{ $passenger->dob ? \Carbon\Carbon::parse($passenger->dob)->format('M d, Y') : '-' }}
                    </td>
                    <td style="padding: 10px 8px; text-align: right; font-weight: 700; color: #0f172a;">
                        USD
                        {{ number_format(($booking->amount_charged ?? 0) / max($booking->passengers->count(), 1), 2) }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<h4 style="margin: 24px 0 10px 0; color: #0f172a; font-size: 15px; font-weight: 700;">Purchase Summary:</h4>
<div style="font-size: 12px; color: #64748b; margin-bottom: 8px; font-weight: 600;">Payment Type: Credit / Debit Card
    Authorization</div>

<table width="100%" cellpadding="0" cellspacing="0" border="0"
    style="width: 100%; border: 1px solid #e2e8f0; border-collapse: collapse; border-radius: 8px; overflow: hidden; font-size: 13px; margin: 10px 0;">
    <tbody>
        <tr style="border-bottom: 1px solid #f1f5f9;">
            <td
                style="padding: 10px 14px; font-weight: 600; background-color: #f8fafc; color: #475569; width: 38%; border-right: 1px solid #e2e8f0;">
                Card Holder Name:</td>
            <td style="padding: 10px 14px; color: #0f172a; font-weight: 600;">
                {{ $booking->cards->first()?->card_holder_name ?? 'N/A' }}</td>
        </tr>
        <tr style="border-bottom: 1px solid #f1f5f9;">
            <td
                style="padding: 10px 14px; font-weight: 600; background-color: #f8fafc; color: #475569; border-right: 1px solid #e2e8f0;">
                Card Type:</td>
            <td style="padding: 10px 14px; color: #0f172a;">{{ $booking->cards->first()?->card_type ?? 'N/A' }}</td>
        </tr>
        <tr style="border-bottom: 1px solid #f1f5f9;">
            <td
                style="padding: 10px 14px; font-weight: 600; background-color: #f8fafc; color: #475569; border-right: 1px solid #e2e8f0;">
                Card Number:</td>
            <td style="padding: 10px 14px; color: #0f172a; font-family: monospace;">
                {{ $booking->cards->first()?->card_number ?? 'N/A' }}</td>
        </tr>
        <tr style="border-bottom: 1px solid #f1f5f9;">
            <td
                style="padding: 10px 14px; font-weight: 600; background-color: #f8fafc; color: #475569; border-right: 1px solid #e2e8f0;">
                Expiration:</td>
            <td style="padding: 10px 14px; color: #0f172a;">{{ $booking->cards->first()?->expiration ?? 'N/A' }}</td>
        </tr>
        <tr style="border-bottom: 1px solid #f1f5f9;">
            <td
                style="padding: 10px 14px; font-weight: 600; background-color: #f8fafc; color: #475569; border-right: 1px solid #e2e8f0;">
                Billing Address:</td>
            <td style="padding: 10px 14px; color: #0f172a;">{{ $booking->cards->first()?->billing_address ?? 'N/A' }}
            </td>
        </tr>
        <tr style="border-bottom: 1px solid #f1f5f9;">
            <td
                style="padding: 10px 14px; font-weight: 600; background-color: #f8fafc; color: #475569; border-right: 1px solid #e2e8f0;">
                Phone Number:</td>
            <td style="padding: 10px 14px; color: #0f172a;">{{ $booking->cards->first()?->billing_phone ?? 'N/A' }}
            </td>
        </tr>
        <tr style="border-bottom: 1px solid #f1f5f9;">
            <td
                style="padding: 10px 14px; font-weight: 600; background-color: #f8fafc; color: #475569; border-right: 1px solid #e2e8f0;">
                Email:</td>
            <td style="padding: 10px 14px; color: #0f172a;">{{ $booking->customer_email }}</td>
        </tr>
        <tr style="border-bottom: 1px solid #f1f5f9;">
            <td
                style="padding: 10px 14px; font-weight: 600; background-color: #f8fafc; color: #475569; border-right: 1px solid #e2e8f0;">
                Total Amount:</td>
            <td style="padding: 10px 14px; font-weight: 700; color: #16a34a; font-size: 15px;">USD
                {{ number_format($booking->amount_charged, 2) }}</td>
        </tr>
        <tr>
            <td
                style="padding: 10px 14px; font-weight: 600; background-color: #f8fafc; color: #475569; border-right: 1px solid #e2e8f0;">
                Transaction Date:</td>
            <td style="padding: 10px 14px; color: #0f172a;">{{ \Carbon\Carbon::now()->format('M d, Y') }}</td>
        </tr>
    </tbody>
</table>
