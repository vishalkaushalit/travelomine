<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Authorization</title>
</head>

<body style="margin:0; padding:0; background-color:#eef2f7; font-family:Arial, Helvetica, sans-serif;">

    <table width="100%" cellpadding="0" cellspacing="0" border="0" bgcolor="#eef2f7">
        <tr>
            <td align="center" style="padding:24px 12px;">

                @php
                    $merchantName = $booking->agencyMerchant->name ?? 'Travelomile';
                    $merchantSupport = $booking->agencyMerchant->support_mail ?? null;
                    $merchantPhone = $booking->agencyMerchant->contact_number ?? null;
                @endphp

                <table width="100%" cellpadding="0" cellspacing="0" border="0" bgcolor="#ffffff"
                    style="max-width:680px; width:100%; border-collapse:collapse; background:#ffffff; border-radius:20px; overflow:hidden; box-shadow:0 10px 30px rgba(15, 23, 42, 0.08);">

                    <tr>
                        <td style="padding:0;">

                            <table width="100%" cellpadding="0" cellspacing="0" border="0"
                                style="background:linear-gradient(135deg, #0f172a 0%, #1e293b 55%, #334155 100%);">
                                <tr>
                                    <td align="center" style="padding:40px 32px 30px 32px;">
                                        <p
                                            style="margin:0 0 10px; font-size:12px; line-height:12px; letter-spacing:1.8px; text-transform:uppercase; color:#93c5fd; font-weight:bold;">
                                            Secure Payment Confirmation
                                        </p>
                                        <h1
                                            style="margin:0; font-size:30px; line-height:36px; color:#ffffff; font-weight:700;">
                                            Payment Authorization
                                        </h1>
                                        <p style="margin:14px 0 0; font-size:15px; line-height:22px; color:#cbd5e1;">
                                            {{ $merchantName }} for booking reference
                                            <span
                                                style="color:#ffffff; font-weight:bold;">{{ $booking->booking_reference }}</span>
                                        </p>
                                    </td>
                                </tr>
                            </table>

                        </td>
                    </tr>

                    <tr>
                        <td style="padding:32px 32px 20px 32px; color:#334155; font-size:15px; line-height:24px;">

                            <table width="100%" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <td style="padding:0; font-size:15px; line-height:24px; color:#334155;">
                                        {!! $emailBody !!}
                                    </td>
                                </tr>
                            </table>

                            <table width="100%" cellpadding="0" cellspacing="0" border="0"
                                style="margin-top:24px; border:1px solid #dbe4f0; background:#f8fbff; border-radius:16px;">
                                <tr>
                                    <td style="padding:22px 22px 18px 22px;">

                                        <p
                                            style="margin:0 0 14px; font-size:15px; line-height:24px; color:#334155; font-weight:600;">
                                            Authorization Required
                                        </p>

                                        <p style="margin:0 0 16px; font-size:14px; line-height:22px; color:#475569;">
                                            Kindly reply to this email with your acknowledgement to confirm that you
                                            authorize the above booking and payment charges.
                                        </p>

                                        <p
                                            style="margin:0 0 10px; font-size:12px; line-height:18px; font-weight:bold; letter-spacing:1px; text-transform:uppercase; color:#64748b;">
                                            You may reply with
                                        </p>

                                        <table width="100%" cellpadding="0" cellspacing="0" border="0"
                                            style="background:#ffffff; border:1px solid #cbd5e1; border-radius:12px;">
                                            <tr>
                                                <td
                                                    style="padding:16px 18px; font-size:15px; line-height:24px; color:#0f172a; font-style:italic;">
                                                    “I acknowledge and authorize this booking and the related payment
                                                    charges.”
                                                </td>
                                            </tr>
                                        </table>

                                    </td>
                                </tr>
                            </table>

                        </td>
                    </tr>

                    <tr>
                        <td style="padding:0 32px 28px 32px;">

                            <table width="100%" cellpadding="0" cellspacing="0" border="0"
                                style="border-top:1px solid #e2e8f0;">
                                <tr>
                                    <td style="padding-top:20px; text-align:center;">

                                        <p
                                            style="margin:0 0 8px; font-size:16px; line-height:24px; color:#0f172a; font-weight:700;">
                                            {{ $merchantName }}
                                        </p>

                                        <p style="margin:0 0 10px; font-size:13px; line-height:21px; color:#64748b;">
                                            If you did not request this booking, please contact our support team
                                            immediately.
                                        </p>

                                        @if ($merchantPhone || $merchantSupport)
                                            <p
                                                style="margin:0 0 10px; font-size:13px; line-height:21px; color:#475569;">
                                                @if ($merchantPhone)
                                                    <span style="font-weight:bold; color:#334155;">Phone:</span>
                                                    {{ $merchantPhone }}
                                                @endif
                                                @if ($merchantPhone && $merchantSupport)
                                                    <span style="color:#cbd5e1;">&nbsp;&nbsp;|&nbsp;&nbsp;</span>
                                                @endif
                                                @if ($merchantSupport)
                                                    <span style="font-weight:bold; color:#334155;">Email:</span>
                                                    {{ $merchantSupport }}
                                                @endif
                                            </p>
                                        @endif

                                        <p style="margin:0; font-size:12px; line-height:18px; color:#94a3b8;">
                                            © 2026 {{ $merchantName }}. All Rights Reserved.
                                        </p>

                                    </td>
                                </tr>
                            </table>

                        </td>
                    </tr>

                </table>

            </td>
        </tr>
    </table>

</body>

</html>
