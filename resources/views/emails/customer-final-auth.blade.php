<body style="margin:0;padding:30px;">
    <table width="100%" cellpadding="0" cellspacing="0" border="0">
        <tr>
            <td>
                <table width="850" cellpadding="0" cellspacing="0" border="0"
                    style="background:#ffffff; border:1px 1px 0 1px solid #dcdcdc; border-radius:12px; overflow:hidden;">

                    <!-- Header -->
                    <tr>
                        <td style="background:#1e3a8a; color:#fff; padding:20px; font-size:24px; font-weight:bold;">
                            Payment Authorization
                        </td>
                    </tr>
                    <!-- Body Content -->
                    <tr>
                        <td style="padding:30px 30px 0 30px;">
                            {!! $mainContent !!}

                            @include('components.flight-itinerary-email')
                            {!! $purchaseSummary !!}
                            @include('components.flight-terms')
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>


