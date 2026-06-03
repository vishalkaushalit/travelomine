<table width="100%" cellpadding="0" cellspacing="0" border="0"
       style="border:1px solid #dcdcdc;margin-top:20px;font-family:Arial,sans-serif;">

    <tr>
        <td style="padding:12px;background:#f5f5f5;font-weight:bold;">
            Flight Details
        </td>

        <td align="right"
            style="padding:12px;background:#f5f5f5;">
            PNR:
            {{ $booking->airline_pnr ?: $booking->gk_pnr }}
        </td>
    </tr>

</table>

@foreach($booking->segments as $index => $segment)

<table width="100%" cellpadding="0" cellspacing="0" border="0"
       style="border-left:1px solid #dcdcdc;border-right:1px solid #dcdcdc;">

    <tr>

        <td style="padding:15px;font-size:14px;">
            {{ \Carbon\Carbon::parse($segment->departure_date)->format('D, M d') }}
        </td>

        <td align="center">
            <span style="
                background:#d1fae5;
                color:#047857;
                padding:5px 10px;
                font-weight:bold;
                ">
                Confirmed
            </span>
        </td>

        <td align="right" style="padding:15px;">

            @if($segment->airline && $segment->airline->logo)

                <img
                    src="{{ asset('storage/'.$segment->airline->logo) }}"
                    width="80"
                    style="display:block;">

            @endif

            <strong>
                {{ $segment->airline_code }}
                {{ $segment->flight_number }}
            </strong>

            {{ $segment->cabin_class }}

        </td>

    </tr>

</table>

<table width="100%" cellpadding="15" cellspacing="0" border="0"
       style="
       border-left:1px solid #dcdcdc;
       border-right:1px solid #dcdcdc;
       ">

    <tr>

        <td width="40%" valign="top">

            <div style="font-size:42px;font-weight:bold;">
                {{ $segment->from_airport }}
            </div>

            <div style="font-size:22px;font-weight:bold;">
                {{ \Carbon\Carbon::parse($segment->departure_time)->format('g:i A') }}
            </div>

            <div>
                {{ $segment->from_city }}
            </div>

        </td>

        <td width="20%" align="center">
            ✈
        </td>

        <td width="40%" align="right" valign="top">

            <div style="font-size:42px;font-weight:bold;">
                {{ $segment->to_airport }}
            </div>

            <div style="font-size:22px;font-weight:bold;">
                {{ \Carbon\Carbon::parse($segment->arrival_time)->format('g:i A') }}
            </div>

            <div>
                {{ $segment->to_city }}
            </div>

        </td>

    </tr>

</table>

<table width="100%" cellpadding="0" cellspacing="0" border="0"
       style="
       border-left:1px solid #dcdcdc;
       border-right:1px solid #dcdcdc;
       border-bottom:1px solid #dcdcdc;
       ">

    <tr>
        <td style="padding:15px;font-weight:bold;">

            Duration:
            {{ $duration ?? '' }}

        </td>
    </tr>

</table>

@endforeach