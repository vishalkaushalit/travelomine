<body style="margin:0;padding:30px;background:#f3f4f6;">

    <table width="100%" cellpadding="0" cellspacing="0" border="0">
        <tr>
            <td align="center">
                <table width="850" cellpadding="0" cellspacing="0" border="0"
                    style="background:#ffffff; border:1px solid #dcdcdc; border-radius:12px; overflow:hidden;">

                    <!-- Header -->
                    <tr>
                        <td style="background:#1e3a8a; color:#fff; padding:20px; font-size:24px; font-weight:bold;">
                            Payment Authorization
                        </td>
                    </tr>

                    <!-- Body Content -->
                    <tr>
                        <td style="padding:30px;">
                            {!! $mainContent !!}
                            @include('components.flight-itinerary-email')
                            {!! $purchaseSummary !!}
                            @include('components.flight-terms')
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background:#f8f9fa; padding:15px; text-align:center; font-size:12px; color:#666;">
                            Booking Reference: {{ $booking->booking_reference }}
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>

</body>
