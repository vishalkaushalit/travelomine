<style>
    body {
        font-family: DejaVu Sans, sans-serif;
        font-size: 12px;
        color: #333;
    }

    .ticket-card {
        border: 1px solid #d9e2ec;
        border-radius: 12px;
        overflow: hidden;
    }

    .ticket-header {
        background: #003366;
        color: #fff;
        padding: 12px 15px;
    }

    .ticket-header h2 {
        margin: 0;
        font-size: 18px;
    }

    .status-badge {
        background: #ffffff20;
        border: 1px solid #ffffff40;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 11px;
        color: #fff;
    }

    .section {
        padding: 15px;
    }

    .meta-box {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        margin-bottom: 15px;
    }

    .meta-box td {
        padding: 10px;
    }

    .flight-leg {
        border: 1px solid #e6edf4;
        margin-bottom: 12px;
        padding: 12px;
        border-radius: 8px;
    }

    .flight-number {
        background: #e8f0fe;
        color: #003366;
        padding: 4px 10px;
        font-size: 11px;
        border-radius: 20px;
        font-weight: bold;
    }

    .time {
        font-size: 16px;
        font-weight: bold;
        color: #003366;
    }

    .airport {
        font-size: 12px;
        color: #555;
    }

    .passenger-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 15px;
    }

    .passenger-table th {
        background: #003366;
        color: white;
        padding: 8px;
        border: 1px solid #ddd;
    }

    .passenger-table td {
        padding: 8px;
        border: 1px solid #ddd;
    }

    .footer {
        border-top: 1px solid #ddd;
        padding-top: 10px;
        margin-top: 20px;
    }

    .flight_icon {
        font-size: 20px;
        color: #003366;
    }
</style>

<div class="ticket-card">

    {{-- HEADER --}}
    <table width="100%" cellpadding="0" cellspacing="0" class="ticket-header">
        <tr>
            <td>
                <h2>{{ $booking->airline_name }} e-Ticket</h2>
            </td>
            <td align="right">
                <span class="status-badge">
                    {{ ucfirst($booking->status) }}
                </span>
            </td>
        </tr>
    </table>

    <div class="section">

        {{-- ROUTE --}}
        <table width="100%" cellpadding="5" cellspacing="0">
            <tr>
                <td width="65%">
                    <strong style="font-size:16px;">
                        {{ $booking->departure_city }}
                        →
                        {{ $booking->arrival_city }}
                    </strong>
                    <br>

                    {{ \Carbon\Carbon::parse($booking->departure_date)->format('d M Y') }}

                    @if ($booking->return_date)
                        -
                        {{ \Carbon\Carbon::parse($booking->return_date)->format('d M Y') }}
                    @endif
                </td>

                <td align="right">
                    {{ ucfirst($booking->flight_type) }}
                    <br>
                    {{ $booking->total_passengers }} Passenger(s)
                </td>
            </tr>
        </table>

        {{-- BOOKING INFO --}}
        <table width="100%" cellpadding="0" cellspacing="0" class="meta-box">
            <tr>
                <td width="33%">
                    <strong>Ticket Number</strong><br>
                    {{ optional($booking->passengers->first())->ticket_number }}
                </td>

                <td width="33%">
                    <strong>PNR</strong><br>
                    {{ $booking->airline_pnr }}
                </td>

                <td width="34%">
                    <strong>Cabin Class</strong><br>
                    {{ $booking->cabin_class }}
                </td>
            </tr>
        </table>

        {{-- FLIGHTS --}}
        @foreach ($booking->segments as $segment)
            <div class="flight-leg">

                <table width="100%">
                    <tr>
                        <td colspan="3">
                            <span class="flight-number">
                                {{ $segment->flight_number }}
                            </span>
                        </td>
                    </tr>

                    <tr>
                        <td width="40%">
                            <div class="time">
                                {{ date('h:i A', strtotime($segment->departure_time)) }}
                            </div>

                            <div class="airport">
                                {{ $segment->from_city }}
                                ({{ $segment->from_airport }})
                            </div>
                        </td>

                        <td width="20%" align="center">
                            <span class="flight_icon">✈</span>
                        </td>

                        <td width="40%" align="right">
                            <div class="time">
                                {{ date('h:i A', strtotime($segment->arrival_time)) }}
                            </div>

                            <div class="airport">
                                {{ $segment->to_city }}
                                ({{ $segment->to_airport }})
                            </div>
                        </td>
                    </tr>
                </table>

            </div>
        @endforeach

        {{-- PASSENGERS --}}
        <h3>Passenger Details</h3>

        <table class="passenger-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Type</th>
                    <th>Ticket Number</th>
                    <th>Passport</th>
                </tr>
            </thead>

            <tbody>
                @foreach ($booking->passengers as $passenger)
                    <tr>
                        <td>
                            {{ $passenger->title }}
                            {{ $passenger->first_name }}
                            {{ $passenger->last_name }}
                        </td>

                        <td>
                            {{ $passenger->passenger_type }}
                        </td>

                        <td>
                            {{ $passenger->ticket_number }}
                        </td>

                        <td>
                            {{ $passenger->passport_number }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{-- FOOTER --}}
        <table width="100%" class="footer">
            <tr>
                <td width="40%">
                    Ticket:
                    {{ optional($booking->passengers->first())->ticket_number }}
                </td>

                <td width="30%" align="center">
                    {{ $booking->airline_name }}
                </td>

                <td width="30%" align="right">
                    PNR:
                    {{ $booking->airline_pnr }}
                </td>
            </tr>
        </table>

    </div>

</div>
