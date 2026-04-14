@component('mail::message')

Hey, Booking #{{ $booking->id }} status has been updated to {{ str_replace('_', ' ', $booking->status) }}.

<table style="width: 100%; border-collapse: collapse; margin: 20px 0;">
    <thead>
        <tr style="background-color: #f8f9fa; border-bottom: 2px solid #dee2e6;">
            <th style="text-align: left; padding: 12px; border: 1px solid #dee2e6;">Field</th>
            <th style="text-align: left; padding: 12px; border: 1px solid #dee2e6;">Value</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td style="padding: 12px; border: 1px solid #dee2e6; font-weight: 600;">Booking Reference</td>
            <td style="padding: 12px; border: 1px solid #dee2e6;">{{ $booking->booking_reference }}</td>
        </tr>
        <tr>
            <td style="padding: 12px; border: 1px solid #dee2e6; font-weight: 600;">Booking Date</td>
            <td style="padding: 12px; border: 1px solid #dee2e6;">{{ $booking->booking_date ? $booking->booking_date->format('Y-m-d') : 'N/A' }}</td>
        </tr>
        <tr>
            <td style="padding: 12px; border: 1px solid #dee2e6; font-weight: 600;">Customer Name</td>
            <td style="padding: 12px; border: 1px solid #dee2e6;">{{ $booking->customer_name }}</td>
        </tr>
        <tr>
            <td style="padding: 12px; border: 1px solid #dee2e6; font-weight: 600;">Customer Email</td>
            <td style="padding: 12px; border: 1px solid #dee2e6;">{{ $booking->customer_email }}</td>
        </tr>
        <tr>
            <td style="padding: 12px; border: 1px solid #dee2e6; font-weight: 600;">Customer Phone</td>
            <td style="padding: 12px; border: 1px solid #dee2e6;">{{ $booking->customer_phone }}</td>
        </tr>
        <tr>
            <td style="padding: 12px; border: 1px solid #dee2e6; font-weight: 600;">Billing Phone</td>
            <td style="padding: 12px; border: 1px solid #dee2e6;">{{ $booking->billing_phone }}</td>
        </tr>
        <tr>
            <td style="padding: 12px; border: 1px solid #dee2e6; font-weight: 600;">Billing Address</td>
            <td style="padding: 12px; border: 1px solid #dee2e6;">{{ nl2br(e($booking->billing_address)) }}</td>
        </tr>
        <tr>
            <td style="padding: 12px; border: 1px solid #dee2e6; font-weight: 600;">Flight Type</td>
            <td style="padding: 12px; border: 1px solid #dee2e6;">{{ $booking->flight_type }}</td>
        </tr>
        <tr>
            <td style="padding: 12px; border: 1px solid #dee2e6; font-weight: 600;">Departure City</td>
            <td style="padding: 12px; border: 1px solid #dee2e6;">{{ $booking->departure_city }}</td>
        </tr>
        <tr>
            <td style="padding: 12px; border: 1px solid #dee2e6; font-weight: 600;">Arrival City</td>
            <td style="padding: 12px; border: 1px solid #dee2e6;">{{ $booking->arrival_city }}</td>
        </tr>
        <tr>
            <td style="padding: 12px; border: 1px solid #dee2e6; font-weight: 600;">Departure Date</td>
            <td style="padding: 12px; border: 1px solid #dee2e6;">{{ isset($booking->departure_date) && $booking->departure_date ? $booking->departure_date->format('Y-m-d') : 'N/A' }}</td>
        </tr>
        <tr>
            <td style="padding: 12px; border: 1px solid #dee2e6; font-weight: 600;">Return Date</td>
            <td style="padding: 12px; border: 1px solid #dee2e6;">{{ isset($booking->return_date) && $booking->return_date ? $booking->return_date->format('Y-m-d') : 'N/A' }}</td>
        </tr>
        <tr>
            <td style="padding: 12px; border: 1px solid #dee2e6; font-weight: 600;">GK PNR</td>
            <td style="padding: 12px; border: 1px solid #dee2e6;">{{ $booking->gk_pnr ?: 'N/A' }}</td>
        </tr>
        <tr>
            <td style="padding: 12px; border: 1px solid #dee2e6; font-weight: 600;">Airline PNR</td>
            <td style="padding: 12px; border: 1px solid #dee2e6;">{{ $booking->airline_pnr ?: 'N/A' }}</td>
        </tr>
        <tr>
            <td style="padding: 12px; border: 1px solid #dee2e6; font-weight: 600;">Currency</td>
            <td style="padding: 12px; border: 1px solid #dee2e6;">{{ $booking->currency }}</td>
        </tr>
        <tr>
            <td style="padding: 12px; border: 1px solid #dee2e6; font-weight: 600;">Amount Charged</td>
            <td style="padding: 12px; border: 1px solid #dee2e6;">{{ $booking->currency }} {{ number_format($booking->amount_charged, 2) }}</td>
        </tr>
        <tr>
            <td style="padding: 12px; border: 1px solid #dee2e6; font-weight: 600;">Total MCO</td>
            <td style="padding: 12px; border: 1px solid #dee2e6;">{{ $booking->currency }} {{ number_format($booking->total_mco, 2) }}</td>
        </tr>
        <tr>
            <td style="padding: 12px; border: 1px solid #dee2e6; font-weight: 600;">Status</td>
            <td style="padding: 12px; border: 1px solid #dee2e6;">{{ ucfirst(str_replace('_', ' ', $booking->status)) }}</td>
        </tr>
       
        @if (!empty($booking->agent_remarks))
        <tr>
            <td style="padding: 12px; border: 1px solid #dee2e6; font-weight: 600;">Agent Remarks</td>
            <td style="padding: 12px; border: 1px solid #dee2e6;">{{ nl2br(e($booking->agent_remarks)) }}</td>
        </tr>
        @endif
        @if (!empty($booking->charging_remarks))
        <tr>
            <td style="padding: 12px; border: 1px solid #dee2e6; font-weight: 600;">Charging Remarks</td>
            <td style="padding: 12px; border: 1px solid #dee2e6;">{{ nl2br(e($booking->charging_remarks)) }}</td>
        </tr>
        @endif
        @if (!empty($booking->mis_remarks))
        <tr>
            <td style="padding: 12px; border: 1px solid #dee2e6; font-weight: 600;">MIS Remarks</td>
            <td style="padding: 12px; border: 1px solid #dee2e6;">{{ nl2br(e($booking->mis_remarks)) }}</td>
        </tr>
        @endif
    </tbody>
</table>

@component('mail::button', ['url' => url('agent/bookings/'.$booking->id)])
View Booking Details
@endcomponent

Thanks!
@endcomponent
