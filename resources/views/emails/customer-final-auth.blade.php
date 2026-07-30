<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Authorization & Booking Confirmation</title>
</head>
<body style="margin: 0; padding: 25px 10px; background-color: #f1f5f9; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; color: #334155; line-height: 1.6; -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%;">
    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color: #f1f5f9; width: 100%;">
        <tr>
            <td align="center" style="padding: 0;">
                <!-- One Single Container (Responsive 650px) -->
                <table width="650" cellpadding="0" cellspacing="0" border="0" style="max-width: 650px; width: 100%; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; overflow: hidden; box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05);">
                    
                    <!-- Header Banner -->
                    <tr>
                        <td style="background: #0f172a; color: #ffffff; padding: 24px 30px;">
                            <table width="100%" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <td valign="middle">
                                        <div style="font-size: 22px; font-weight: 800; letter-spacing: -0.5px; color: #ffffff; line-height: 1.2;">
                                            Reservation
                                        </div>
                                        <div style="font-size: 12px; color: #94a3b8; margin-top: 4px; text-transform: uppercase; letter-spacing: 0.8px; font-weight: 600;">
                                            Payment Authorization & Booking Confirmation
                                        </div>
                                    </td>
                                    <td align="right" valign="middle">
                                        <table cellpadding="0" cellspacing="0" border="0" style="background: rgba(255, 255, 255, 0.08); border: 1px solid rgba(255, 255, 255, 0.18); border-radius: 8px;">
                                            <tr>
                                                <td style="padding: 8px 14px; font-size: 12px; color: #f1f5f9; text-align: right; line-height: 1.4;">
                                                    Ref: <strong style="color: #ffffff;">{{ $booking->booking_reference ?? 'N/A' }}</strong><br>
                                                    PNR: <strong style="color: #38bdf8;">{{ $booking->airline_pnr ? $booking->airline_pnr : ($booking->gk_pnr ?: 'N/A') }}</strong>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Body Content Area -->
                    <tr>
                        <td style="padding: 30px 30px 20px 30px; background: #ffffff;">
                            
                            <!-- Main Content (Agreement, Charges, Passenger Table) -->
                            <div style="font-size: 14px; color: #334155; line-height: 1.7;">
                                {!! $mainContent !!}
                            </div>

                            <!-- Flight Itinerary Screenshot / Details Component -->
                            @include('components.flight-itinerary-email')

                            <!-- Purchase Summary / Charges -->
                            @if(!empty(trim($purchaseSummary)))
                                <div style="font-size: 14px; color: #334155; line-height: 1.7; margin-top: 20px;">
                                    {!! $purchaseSummary !!}
                                </div>
                            @endif

                            <!-- Terms and Conditions Component -->
                            @include('components.flight-terms')

                        </td>
                    </tr>

                    <!-- Footer Bar -->
                    <tr>
                        <td style="background: #f8fafc; border-top: 1px solid #e2e8f0; padding: 20px 30px; text-align: center; font-size: 12px; color: #64748b;">
                            <p style="margin: 0; font-weight: 500; line-height: 1.5;">
                                Thank you for choosing {{ $booking->agency_merchant_name ?? 'our services' }}. If you have any questions regarding this authorization, please reply directly to this email or contact customer support.
                            </p>
                            <p style="margin: 8px 0 0 0; color: #94a3b8; font-size: 11px;">
                                &copy; {{ date('Y') }} {{ $booking->agency_merchant_name ?? config('app.name') }}. All rights reserved.
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>
