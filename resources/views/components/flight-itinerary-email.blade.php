@if ($booking->itinerary_image)
    @php
        $rawPath = ltrim($booking->itinerary_image, '/\\');
        $cleanPath = preg_replace('/^(public\/|storage\/|app\/public\/)+/i', '', $rawPath);
        
        $candidatePaths = [
            storage_path('app/public/' . $cleanPath),
            storage_path('app/public/' . $rawPath),
            storage_path('app/' . $cleanPath),
            storage_path('app/' . $rawPath),
            public_path('storage/' . $cleanPath),
            public_path($rawPath),
            base_path($rawPath),
        ];

        $foundPath = null;
        foreach ($candidatePaths as $path) {
            if ($path && file_exists($path) && !is_dir($path)) {
                $foundPath = $path;
                break;
            }
        }

        $base64Image = null;
        if ($foundPath) {
            $ext = strtolower(pathinfo($foundPath, PATHINFO_EXTENSION));
            $mime = match($ext) {
                'jpg', 'jpeg' => 'image/jpeg',
                'png' => 'image/png',
                'gif' => 'image/gif',
                'webp' => 'image/webp',
                default => (function_exists('mime_content_type') ? @mime_content_type($foundPath) : 'image/png')
            };
            if (!$mime) $mime = 'image/png';
            $base64Image = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($foundPath));
        }
    @endphp

    <div style="margin: 24px 0; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; overflow: hidden; padding: 20px; text-align: center;">
        <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom: 14px;">
            <tr>
                <td align="left" style="font-weight: 700; font-size: 15px; color: #0f172a;">
                    ✈ Flight Details
                </td>
                <td align="right">
                    <span style="background: #e0e7ff; color: #3730a3; padding: 4px 10px; border-radius: 6px; font-size: 12px; font-weight: 600;">
                        {{ $booking->airline_name ?? 'Airline' }} @if($booking->airline_code)({{ $booking->airline_code }})@endif
                    </span>
                </td>
            </tr>
        </table>
        
        @if ($base64Image)
            <img src="{{ $base64Image }}" alt="Flight Itinerary" style="max-width: 100%; height: auto; border: 1px solid #cbd5e1; border-radius: 8px; display: block; margin: 0 auto; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);">
        @else
            <img src="{{ url('storage/' . $cleanPath) }}" alt="Flight Itinerary" style="max-width: 100%; height: auto; border: 1px solid #cbd5e1; border-radius: 8px; display: block; margin: 0 auto; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);">
        @endif
        
        <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-top: 14px; font-size: 13px; color: #475569;">
            <tr>
                <td align="center">
                    PNR: <strong style="color: #0f172a; font-family: monospace; font-size: 14px;">{{ $booking->airline_pnr ? $booking->airline_pnr : ($booking->gk_pnr ?: 'N/A') }}</strong> 
                    &nbsp;|&nbsp; Status: <span style="color: #16a34a; font-weight: 700;">Confirmed</span>
                </td>
            </tr>
        </table>
    </div>
@elseif (isset($booking->segments) && $booking->segments->count() > 0)
    <div style="margin: 24px 0; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; overflow: hidden;">
        <div style="background: #0f172a; color: #ffffff; padding: 12px 20px; font-weight: 700; font-size: 15px;">
            ✈ Flight Details
        </div>
        @foreach ($booking->segments as $index => $segment)
            <div style="padding: 14px 20px; border-bottom: 1px solid #f1f5f9;">
                <div style="font-size: 14px; font-weight: 700; color: #0f172a; margin-bottom: 6px;">
                    {{ \Carbon\Carbon::parse($segment->departure_date)->format('D, M d Y') }}: {{ $segment->from_city }} → {{ $segment->to_city }}
                </div>
                <div style="font-size: 13px; color: #475569; line-height: 1.5;">
                    <strong>Airline:</strong> {{ $segment->airline->name ?? ($segment->airline_name ?? $segment->airline_code) }} ({{ $segment->airline_code }}{{ $segment->flight_number }}) |
                    <strong>Time:</strong> {{ \Carbon\Carbon::parse($segment->departure_time)->format('g:i A') }} - {{ \Carbon\Carbon::parse($segment->arrival_time)->format('g:i A') }} |
                    <strong>Class:</strong> {{ $segment->cabin_class }}
                </div>
            </div>
        @endforeach
    </div>
@endif
