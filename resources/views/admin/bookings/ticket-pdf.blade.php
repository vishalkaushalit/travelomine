<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');
    
    .ticket-wrapper {
        font-family: 'Inter', system-ui, -apple-system, sans-serif;
        max-width: 850px;
        margin: 0 auto;
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.08);
        overflow: hidden;
        color: #111827;
        border: 1px solid #e5e7eb;
    }

    .ticket-header {
        background: #111827;
        color: #fff;
        padding: 30px 40px;
    }

    .ticket-header-table {
        width: 100%;
        border-collapse: collapse;
    }

    .ticket-header-table td {
        vertical-align: middle;
    }

    .airline-logo-box {
        background: #fff;
        padding: 8px;
        border-radius: 12px;
        display: inline-block;
    }

    .airline-logo-box img {
        height: 70px;
        max-width: auto;
        object-fit: contain;
    }

    .brand-fallback {
        font-size: 24px;
        font-weight: 700;
        letter-spacing: -0.5px;
        color: #fff;
    }

    .header-info {
        text-align: right;
    }

    .header-info h1 {
        margin: 0 0 5px 0;
        font-size: 22px;
        font-weight: 600;
        letter-spacing: 1px;
        text-transform: uppercase;
        color: #e5e7eb;
    }

    .pnr-badge {
        display: inline-block;
        background: rgba(255,255,255,0.1);
        padding: 6px 12px;
        border-radius: 6px;
        font-size: 16px;
        font-weight: 600;
        letter-spacing: 1px;
    }
    
    .ticket-body {
        padding: 40px;
        background: #fff;
    }

    .section-title {
        font-size: 14px;
        text-transform: uppercase;
        letter-spacing: 1.5px;
        color: #6b7280;
        font-weight: 600;
        margin-bottom: 20px;
        border-bottom: 1px solid #e5e7eb;
        padding-bottom: 8px;
    }

    .flight-card {
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        padding: 24px;
        margin-bottom: 24px;
        background: #fafafa;
    }

    .flight-header-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 20px;
    }

    .flight-number {
        font-weight: 700;
        color: #111827;
        font-size: 16px;
    }

    .flight-date {
        font-weight: 500;
        color: #4b5563;
        font-size: 14px;
        background: #e5e7eb;
        padding: 6px 12px;
        border-radius: 20px;
        display: inline-block;
    }

    .flight-route-table {
        width: 100%;
        border-collapse: collapse;
    }

    .flight-route-table td {
        vertical-align: middle;
    }

    .location {
        width: 35%;
    }

    .location-right {
        text-align: right;
    }

    .time {
        font-size: 28px;
        font-weight: 700;
        color: #111827;
        line-height: 1.2;
    }

    .city {
        font-size: 16px;
        font-weight: 600;
        color: #374151;
        margin-top: 4px;
    }

    .airport {
        font-size: 13px;
        color: #6b7280;
        margin-top: 2px;
    }

    .flight-path {
        width: 30%;
        text-align: center;
        padding: 0 20px;
        position: relative;
    }

    .line {
        width: 100%;
        height: 2px;
        background: #d1d5db;
        position: relative;
        margin-top: 15px;
    }

    .line::before, .line::after {
        content: '';
        position: absolute;
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #9ca3af;
        top: -3px;
    }

    .line::before { left: 0; }
    .line::after { right: 0; }

    .path-arrow {
        position: absolute;
        top: 2px;
        left: 50%;
        transform: translateX(-50%);
        font-size: 18px;
        color: #9ca3af;
        background: #fafafa;
        padding: 0 10px;
    }

    .passenger-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 10px;
    }

    .passenger-table th {
        text-align: left;
        padding: 12px 15px;
        color: #6b7280;
        font-weight: 600;
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 2px solid #e5e7eb;
    }

    .passenger-table td {
        padding: 15px;
        border-bottom: 1px solid #f3f4f6;
        font-size: 14px;
        color: #111827;
    }

    .passenger-table tr:last-child td {
        border-bottom: none;
    }

    .passenger-name {
        font-weight: 600;
    }

    .seat-badge {
        background: #e0e7ff;
        color: #000;
        font-weight: 500;
        padding: 4px 10px;
        border-radius: 6px;
        font-size: 17px;
        font-family: Cambria, Cochin, Georgia, Times, 'Times New Roman', serif;
        display: inline-block;
        letter-spacing:2px;
    }

    .booking-summary-table {
        width: 100%;
        margin-bottom: 30px;
        background: #f9fafb;
        border-radius: 8px;
        border: 1px solid #e5e7eb;
        border-collapse: collapse;
    }

    .booking-summary-table td {
        padding: 20px;
        vertical-align: top;
    }

    .summary-label {
        font-size: 12px;
        color: #6b7280;
        text-transform: uppercase;
        font-weight: 600;
        letter-spacing: 0.5px;
        margin-bottom: 4px;
        display: block;
    }

    .summary-value {
        font-size: 15px;
        font-weight: 600;
        color: #111827;
        display: block;
    }
</style>

<div class="ticket-wrapper">
    <div class="ticket-header">
        <table class="ticket-header-table">
            <tr>
                <td style="text-align: left;">
                    @php
                        $firstSegment = $booking->segments->first();
                    @endphp
                    @if($firstSegment && $firstSegment->airline && $firstSegment->airline->logo)
                        <div class="airline-logo-box">
                            <img src="{{ public_path('storage/'.$firstSegment->airline->logo) }}" alt="{{ $firstSegment->airline->airline_name ?? 'Airline' }}">
                        </div>
                    @else
                        <div class="brand-fallback">
                            {{ $booking->airline_name ?? 'Airline' }}
                        </div>
                    @endif
                </td>
                <td class="header-info">
                    <h1>E-Ticket </h1>
                    <div class="pnr-badge" style="background: #e07129 !important">PNR: {{ $booking->airline_pnr }}</div>
                </td>
            </tr>
        </table>
    </div>

    <div class="ticket-body">
        
        <table class="booking-summary-table">
            <tr>
                <td>
                    <span class="summary-label">Departure</span>
                    <span class="summary-value">{{ \Carbon\Carbon::parse($booking->departure_date)->format('d M Y') }}</span>
                </td>
                <td>
                    <span class="summary-label">Flight Type</span>
                    <span class="summary-value">{{ ucfirst($booking->flight_type) }}</span>
                </td>
                <td>
                    <span class="summary-label">Cabin Class</span>
                    <span class="summary-value">{{ $booking->cabin_class ?? 'Economy' }}</span>
                </td>
                <td>
                    <span class="summary-label">Passengers</span>
                    <span class="summary-value">{{ $booking->total_passengers }}</span>
                </td>
            </tr>
        </table>

        <div class="section-title">Flight Details</div>

        @foreach ($booking->segments as $segment)
            <div class="flight-card">
                <table class="flight-header-table">
                    <tr>
                        <td class="flight-number" style="text-align: left;">
                            Flight: {{ $segment->airline->airline_name ?? $booking->airline_name }} &bull; {{ $segment->flight_number }}
                        </td>
                        <td style="text-align: right;">
                            <div class="flight-date">
                                <!-- {{ \Carbon\Carbon::parse($segment->departure_time)->format('D, d M Y') }} -->
                                  <!-- departure date  -->
                                   {{ \Carbon\Carbon::parse($segment->departure_date)->format('D, d M Y') }}
                            </div>
                        </td>
                    </tr>
                </table>

                <table class="flight-route-table">
                    <tr>
                        <td class="location" style="text-align: left;">
                            <div class="time">{{ date('h:i A', strtotime($segment->departure_time)) }}</div>
                            <div class="city">{{ $segment->from_city }}</div>
                            <div class="airport">{{ $segment->from_airport }}</div>
                        </td>

                        <td class="flight-path">
                            <div class="line">
                                <span class="path-arrow"></span>
                            </div>
                        </td>

                        <td class="location location-right">
                            <div class="time">{{ date('h:i A', strtotime($segment->arrival_time)) }}</div>
                            <div class="city">{{ $segment->to_city }}</div>
                            <div class="airport">{{ $segment->to_airport }}</div>
                        </td>
                    </tr>
                </table>
            </div>
        @endforeach

        <div class="section-title">Passenger Details</div>

        <table class="passenger-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Type</th>
                    <th>Ticket Number</th>
                    <th>Seat</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($booking->passengers as $passenger)
                    <tr>
                        <td class="passenger-name">
                            {{ $passenger->title }} {{ $passenger->first_name }} {{ $passenger->last_name }}
                        </td>
                        <td>{{ ucfirst($passenger->passenger_type) }}</td>
                        <td>{{ $passenger->ticket_number ?? 'Pending' }}</td>
                        <td>
                            @if($passenger->seat_number)
                                <span class="seat-badge">{{ $passenger->seat_number }}</span>
                            @else
                                <span style="color:#9ca3af;">&mdash;</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{-- Optional Fields Section --}}
@php
    $ticketData = $booking->ticket_data ?? [];
    $optionalFields = $ticketData['optional_fields'] ?? [];
    $hasOptionalFields = false;
    
    // Check if any optional field is enabled and has data
    if (isset($optionalFields['passport_number']) && $optionalFields['passport_number'] && !empty($ticketData['passport_numbers'])) {
        $hasOptionalFields = true;
    }
    if (isset($optionalFields['baggage']) && $optionalFields['baggage'] && !empty($ticketData['baggage_info'])) {
        $hasOptionalFields = true;
    }
    if (isset($optionalFields['pet']) && $optionalFields['pet'] && !empty($ticketData['pet_info'])) {
        $hasOptionalFields = true;
    }
@endphp

@if($hasOptionalFields)
    <div class="section-title" style="margin-top: 30px;">Additional Information</div>
    
    <div style="background: #f9fafb; border-radius: 8px; padding: 20px; border: 1px solid #e5e7eb;">
        
        {{-- Passport Numbers --}}
        @if(isset($optionalFields['passport_number']) && $optionalFields['passport_number'] && !empty($ticketData['passport_numbers']))
            <div style="margin-bottom: 15px;">
                <h6 style="font-weight: 600; color: #374151; margin-bottom: 10px; font-size: 14px;">Passport Numbers</h6>
                <table style="width: 100%; border-collapse: collapse;">
                    @foreach($booking->passengers as $index => $passenger)
                        @if(isset($ticketData['passport_numbers'][$index]) && !empty($ticketData['passport_numbers'][$index]))
                            <tr>
                                <td style="padding: 6px 10px; border-bottom: 1px solid #e5e7eb; font-size: 13px;">
                                    <strong>{{ $passenger->title }} {{ $passenger->first_name }} {{ $passenger->last_name }}</strong>
                                </td>
                                <td style="padding: 6px 10px; border-bottom: 1px solid #e5e7eb; font-size: 13px;">
                                    {{ $ticketData['passport_numbers'][$index] }}
                                </td>
                            </tr>
                        @endif
                    @endforeach
                </table>
            </div>
        @endif
        
        {{-- Baggage Information --}}
        @if(isset($optionalFields['baggage']) && $optionalFields['baggage'] && !empty($ticketData['baggage_info']))
            <div style="margin-bottom: 15px;">
                <h6 style="font-weight: 600; color: #374151; margin-bottom: 5px; font-size: 14px;">Baggage Information</h6>
                <p style="margin: 0; color: #4b5563; font-size: 13px; padding: 8px 12px; background: white; border-radius: 4px; border-left: 3px solid #003366;">
                    {{ $ticketData['baggage_info'] }}
                </p>
            </div>
        @endif
        
        {{-- Pet Information --}}
        @if(isset($optionalFields['pet']) && $optionalFields['pet'] && !empty($ticketData['pet_info']))
            <div style="margin-bottom: 0;">
                <h6 style="font-weight: 600; color: #374151; margin-bottom: 5px; font-size: 14px;">Pet Information</h6>
                <p style="margin: 0; color: #4b5563; font-size: 13px; padding: 8px 12px; background: white; border-radius: 4px; border-left: 3px solid #003366;">
                    {{ $ticketData['pet_info'] }}
                </p>
            </div>
        @endif
        
    </div>
@endif

{{-- Footer Note --}}
<div style="margin-top: 30px; padding: 15px; background: #f9fafb; border-radius: 8px; font-size: 12px; color: #6b7280; border: 1px solid #e5e7eb;">
    <p style="margin: 0;">
        <strong>Important:</strong> This is an electronic ticket confirmation. 
        Seat numbers shown are requests and may change upon check-in. 
        Please verify all details at the airport.
    </p>
    @if($hasOptionalFields)
        <p style="margin: 5px 0 0 0; font-size: 11px; color: #9ca3af;">
            Additional information provided as requested.
        </p>
    @endif
</div>

    </div>
</div>