<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>New Booking Assigned</title>
</head>
<body style="font-family: Arial, sans-serif; background:#f8f9fa; padding:20px; color:#333;">
    <div style="max-width:600px; margin:0 auto; background:#ffffff; border:1px solid #e5e5e5; border-radius:8px; overflow:hidden;">
        <div style="background:#0d6efd; color:#fff; padding:18px 24px;">
            <h2 style="margin:0; font-size:20px;">New Booking Assigned To Charging Team</h2>
        </div>

        <div style="padding:24px;">
            <p style="margin-top:0;">Hello Charging Team,</p>

            <p>
                A new booking has been assigned for charging review.
            </p>

            <table style="width:100%; border-collapse:collapse; margin:20px 0;">
                <tr>
                    <td style="padding:10px; border:1px solid #ddd; width:40%;"><strong>Booking Ref</strong></td>
                    <td style="padding:10px; border:1px solid #ddd;">{{ $booking->booking_reference ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td style="padding:10px; border:1px solid #ddd;"><strong>Booking ID</strong></td>
                    <td style="padding:10px; border:1px solid #ddd;">{{ $booking->id }}</td>
                </tr>
                <tr>
                    <td style="padding:10px; border:1px solid #ddd;"><strong>Merchant</strong></td>
                    <td style="padding:10px; border:1px solid #ddd;">{{ $merchantName }}</td>
                </tr>
                <tr>
                    <td style="padding:10px; border:1px solid #ddd;"><strong>Assigned Charger</strong></td>
                    <td style="padding:10px; border:1px solid #ddd;">{{ $assignedChargerName }}</td>
                </tr>
                <tr>
                    <td style="padding:10px; border:1px solid #ddd;"><strong>Assigned By</strong></td>
                    <td style="padding:10px; border:1px solid #ddd;">{{ $agentName }}</td>
                </tr>
                <tr>
                    <td style="padding:10px; border:1px solid #ddd;"><strong>Assigned At</strong></td>
                    <td style="padding:10px; border:1px solid #ddd;">{{ $assignment->assigned_at }}</td>
                </tr>
                <tr>
                    <td style="padding:10px; border:1px solid #ddd;"><strong>Status</strong></td>
                    <td style="padding:10px; border:1px solid #ddd;">{{ ucfirst($assignment->status) }}</td>
                </tr>
            </table>

            <p style="margin-bottom:0;">
                Please check the system for booking details.
            </p>
        </div>

        <div style="background:#f1f1f1; padding:14px 24px; font-size:12px; color:#666;">
            This is an automated notification email.
        </div>
    </div>
</body>
</html>