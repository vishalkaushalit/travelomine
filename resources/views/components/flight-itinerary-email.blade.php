<table width="100%" cellpadding="0" cellspacing="0" border="0" style="background:#f3f4f6; margin-bottom: 30px;">
    <tr>
        <td>
            <table width="850" cellpadding="0" cellspacing="0" border="0" style="background:#ffffff; ">
                <tr>
                    <td style="background:#1e3a8a; color:#fff; padding:20px; font-size:24px; font-weight:bold;">
                        Flight Itinerary
                    </td>
                </tr>
                @foreach ($booking->segments as $index => $segment)
                    <!-- Flight Segment {{ $index + 1 }} -->
                    <tr>
                        <td>
                            <div style="font-size:18px; font-weight:bold; margin:20px 0; text-align:center;">
                                ✈ {{ \Carbon\Carbon::parse($segment->departure_date)->format('D, M d') }},
                                {{ $segment->from_city }} → {{ $segment->to_city }}
                            </div>
                        </td>
                    </tr>
            </table>
            <table cellpadding="0" cellspacing="0" width="850px"
                style=" {{ !$loop->last ? 'margin-bottom:25px;' : '' }}; background:#fff;">
                <tr>
                    <!-- Airline Code & Logo Section -->
                    <td width="130" valign="top"
                        style="border-right:1px solid #dcdcdc; padding:15px; text-align:center;">
                        @if ($segment->airline && $segment->airline->logo)
                            <img src="{{ asset('storage/' . $segment->airline->logo) }}"
                                style="max-width:60px; display:block; margin:0 auto 5px;">
                        @else
                            <div style="font-size:28px; font-weight:bold; color:#d71920;">
                                {{ $segment->airline_code }}
                            </div>
                        @endif
                        <div style="font-weight:bold;">{{ $segment->airline_code }}{{ $segment->flight_number }}</div>
                        <div style="font-size:13px; color:#666;">
                            {{ $segment->airline->name ?? $segment->airline_code }}
                        </div>
                    </td>
                    <!-- Flight Details Section -->
                    <td style="padding:15px;">
                        <div style="margin-bottom:10px;">
                            <span style="font-size:18px; font-weight:bold;">
                                {{ \Carbon\Carbon::parse($segment->departure_time)->format('g:i A') }}
                            </span>
                            {{ $segment->from_airport }} {{ $segment->from_city }}
                        </div>

                        <div style="margin-bottom:15px;">
                            <span style="font-size:18px; font-weight:bold;">
                                {{ \Carbon\Carbon::parse($segment->arrival_time)->format('g:i A') }}
                            </span>
                            {{ $segment->to_airport }} {{ $segment->to_city }}
                        </div>

                        <div style="font-size:13px; color:#666;">
                            Operated by {{ $segment->airline->name ?? $segment->airline_code }}
                        </div>
                    </td>

                    <!-- Duration & Class Section -->
                    <td width="120" valign="top" style="padding:15px; text-align:right;">
                        <div style="font-size:18px; font-weight:bold;">
                            {{ $duration ?? '' }}
                        </div>

                        <div style="margin-top:8px; color:#666;">
                            {{ $segment->cabin_class }}
                        </div>

                    </td>
                </tr>
            </table>
            @endforeach

            <!-- Optional: Show status for each segment -->
            @foreach ($booking->segments as $segment)
                @if ($loop->first)
                    <div style="margin:15px 0; text-align:center; font-size:13px; color:#666; max-width:850px">
                        Status: <span style="color:#047857; font-weight:bold;">Confirmed</span>
                    </div>
                @endif
            @endforeach
        </td>
    </tr>
</table>
