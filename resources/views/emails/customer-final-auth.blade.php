<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Payment Authorization</title>
</head>

<body style="margin:0; padding:0; background-color:#f3f4f6;">

    <table width="100%" cellpadding="0" cellspacing="0" border="0" bgcolor="#f3f4f6">
        <tr>
            <td align="center" style="padding:20px;">

                <!-- Container -->
                <table width="600" cellpadding="0" cellspacing="0" border="0" bgcolor="#ffffff"
                    style="border-collapse:collapse;">

                    <!-- Header -->
                    <tr>
                        <td align="center" bgcolor="#0f172a" style="padding:30px 20px;">
                            <h1 style="margin:0; font-size:20px; color:#ffffff; font-family:Arial, sans-serif;">
                                PAYMENT AUTHORIZATION
                            </h1>
                            <p style="margin:10px 0 0; font-size:14px; color:#cbd5f5; font-family:Arial, sans-serif;">
                                Booking Reference:
                                <strong style="color:#ffffff;">
                                    {{ $booking->booking_reference }}
                                </strong>
                            </p>
                        </td>
                    </tr>

                    <!-- Body -->
                    <tr>
                        <td style="padding:30px 20px; font-family:Arial, sans-serif; color:#333333; font-size:15px; line-height:1.6;">

                            <!-- Dynamic Content -->
                            <table width="100%" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <td>
                                        {!! $emailBody !!}
                                    </td>
                                </tr>
                            </table>

                            <!-- Authorization Box -->
                            <table width="100%" cellpadding="0" cellspacing="0" border="0"
                                style="margin-top:25px; background:#f8fafc; border-left:4px solid #0f172a;">
                                <tr>
                                    <td style="padding:20px;">

                                        <p style="margin:0 0 15px; font-size:14px; color:#444;">
                                            Kindly reply to this email with your acknowledgement to confirm that you
                                            authorize the above booking and payment charges.
                                        </p>

                                        <p style="margin:0 0 8px; font-size:12px; font-weight:bold; color:#666;">
                                            YOU MAY REPLY WITH:
                                        </p>

                                        <!-- Quote Box -->
                                        <table width="100%" cellpadding="0" cellspacing="0" border="0"
                                            style="border:1px dashed #cccccc; background:#ffffff;">
                                            <tr>
                                                <td style="padding:15px; font-size:14px; font-style:italic; color:#000;">
                                                    "I acknowledge and authorize this booking and the related payment
                                                    charges."
                                                </td>
                                            </tr>
                                        </table>

                                    </td>
                                </tr>
                            </table>

                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td align="center" bgcolor="#f9fafb"
                            style="padding:20px; font-family:Arial, sans-serif;">

                            <p style="margin:0 0 8px; font-size:14px; color:#333; font-weight:bold;">
                                Travelomile
                            </p>

                            <p style="margin:0 0 8px; font-size:12px; color:#777;">
                                If you did not request this booking, please contact our support team immediately.
                            </p>

                            <p style="margin:0; font-size:12px; color:#aaa;">
                                © 2026 Travelomile. All Rights Reserved.
                            </p>

                        </td>
                    </tr>

                </table>

            </td>
        </tr>
    </table>

</body>

</html>