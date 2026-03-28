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
                        <td style="background:linear-gradient(135deg,#111827 0%,#1f2937 100%); padding:32px 30px; text-align:center; border-bottom:1px solid #243042;">
                            <div style="font-size:28px; line-height:36px; font-weight:700; color:#ffffff;">
                                Booking Confirmation
                            </div>
                            <div style="font-size:14px; color:#9ca3af; line-height:24px;">
                                Your reservation and payment authorization details are listed below.
                            </div>
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
                                            Total Cost for all passengers: {{ $booking->currency }} {{
                                            number_format($booking->amount_charged, 2) }}
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
                                <span style="color:#93c5fd; font-weight:700;">{{ $booking->currency }} {{
                                    number_format($booking->amount_charged, 2) }}</span>
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
                                            <span style="font-weight:700; color:#ffffff;">{{ $booking->customer_name
                                                }}</span>,
                                            authorize Travelomile to process the above-mentioned charges under their
                                            respective merchants for charging my {{ $booking->card_holder_name }}, card ending in
                                            <span style="font-weight:700; color:#93c5fd;">{{ $booking->card_last_four
                                                }}</span>
                                            for the amount of
                                            <span style="font-weight:700; color:#93c5fd;">{{ $booking->currency }} {{
                                                number_format($booking->amount_charged, 2) }}</span>
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
                                                        {{ $booking->airline_name }}
                                                    </div>
                                                    <div style="font-size:22px; font-weight:700; color:#ffffff;">
                                                        {{ $booking->currency }} {{
                                                        number_format($booking->amount_paid_airline, 2) }}
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
                                                        {{$booking->agency_merchant_name}}</div>
                                                    <div style="font-size:22px; font-weight:700; color:#ffffff;">
                                                        {{ $booking->currency }} {{ number_format($booking->total_mco,
                                                        2) }}
                                                    </div>
                                                    <div style="font-size:12px; color:#64748b; margin-top:6px;">
                                                        Includes all taxes
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
                                    @foreach($booking->passengers as $index => $p)
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
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0"
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
                                                <td style="padding:6px 0; font-size:14px; color:#ffffff;">{{
                                                    $booking->cards->first()->card_holder_name ?? 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <td style="padding:6px 0; font-size:14px; color:#94a3b8;">Card Number
                                                </td>
                                                <td style="padding:6px 0; font-size:14px; color:#ffffff;">{{
                                                    $booking->card_last_four }}</td>
                                            </tr>
                                            <tr>
                                                <td style="padding:6px 0; font-size:14px; color:#94a3b8;">Billing
                                                    Address</td>
                                                <td style="padding:6px 0; font-size:14px; color:#ffffff;">{{
                                                    $booking->billing_address }}</td>
                                            </tr>
                                            <tr>
                                                <td style="padding:6px 0; font-size:14px; color:#94a3b8;">Email</td>
                                                <td style="padding:6px 0; font-size:14px; color:#ffffff;">{{
                                                    $booking->customer_email }}</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    {{-- Footer --}}
                    <tr>
                        <td style="padding:24px 30px 32px 30px;">
                            <div style="font-size:14px; color:#cbd5e1; line-height:26px;">
                                Best Regards,<br>
                                <span style="font-weight:700; color:#ffffff;">Reservation Desk</span><br>
                                {{-- {{ Auth::user()->name ?? 'Support Team' }} --}}
                                {{ Auth::user()->name ?? 'Support Team' }}
                            </div>
                        </td>
                    </tr>

                </table>

            </td>
        </tr>
    </table>

</body>

</html>