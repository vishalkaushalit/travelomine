<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dispute Status Updated</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background: #ffffff;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }
        .header .status-badge {
            display: inline-block;
            background: {{ $statusColor }};
            color: white;
            padding: 8px 20px;
            border-radius: 25px;
            font-size: 18px;
            font-weight: bold;
            margin-top: 15px;
        }
        .content {
            padding: 30px;
        }
        .info-box {
            background: #f8f9fa;
            border-left: 4px solid {{ $statusColor }};
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
        }
        .info-box h3 {
            margin-top: 0;
            color: {{ $statusColor }};
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        table th {
            background: #f8f9fa;
            padding: 12px;
            text-align: left;
            font-weight: 600;
            border-bottom: 2px solid #dee2e6;
        }
        table td {
            padding: 12px;
            border-bottom: 1px solid #dee2e6;
        }
        .remark-box {
            background: #fff3cd;
            border: 1px solid #ffc107;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .remark-box strong {
            color: #856404;
        }
        .footer {
            background: #f8f9fa;
            padding: 20px;
            text-align: center;
            font-size: 14px;
            color: #6c757d;
            border-top: 1px solid #dee2e6;
        }
        .btn {
            display: inline-block;
            padding: 12px 30px;
            background: {{ $statusColor }};
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-weight: 600;
            margin: 10px 0;
        }
        .btn:hover {
            opacity: 0.9;
        }
        .alert-reminder {
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .recipient-info {
            background: #e7f3ff;
            border: 1px solid #b6d4fe;
            padding: 10px;
            border-radius: 5px;
            margin: 10px 0;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>🔔 Dispute Status Update</h1>
            <div class="status-badge">
                {{ $statusEmoji }} {{ $chargebackRecord->status }}
            </div>
        </div>

        <!-- Content -->
        <div class="content">
            <p>Dear {{ $recipientType === 'agent' ? $booking->user->name ?? 'Agent' : ucfirst(str_replace('_', ' ', $recipientType)) }},</p>
            
            <p>A dispute status has been updated for the following booking:</p>

            <!-- Booking Summary -->
            <div class="info-box">
                <h3>📋 Booking Information</h3>
                <table>
                    <tr>
                        <th>Booking ID:</th>
                        <td><strong>#{{ $booking->id }}</strong></td>
                    </tr>
                    <tr>
                        <th>Customer:</th>
                        <td>{{ $booking->customer_name ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th>Email:</th>
                        <td>{{ $booking->customer_email ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th>Phone:</th>
                        <td>{{ $booking->customer_phone ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th>Service:</th>
                        <td>{{ $booking->service_provided ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th>Booking Date:</th>
                        <td>{{ $booking->booking_date ? \Carbon\Carbon::parse($booking->booking_date)->format('d M Y H:i') : 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th>Agent:</th>
                        <td>{{ $booking->user->name ?? 'N/A' }} ({{ $booking->user->email ?? 'N/A' }})</td>
                    </tr>
                </table>
            </div>

            <!-- Status Update Details -->
            <div class="info-box">
                <h3>{{ $statusEmoji }} Dispute Status Update</h3>
                <table>
                    <tr>
                        <th>New Status:</th>
                        <td>
                            <span style="color: {{ $statusColor }}; font-weight: bold;">
                                {{ $statusEmoji }} {{ $chargebackRecord->status }}
                            </span>
                        </td>
                    </tr>
                    @if($chargebackRecord->status === 'Alert' && $chargebackRecord->time_remaining)
                    <tr>
                        <th>Time Remaining:</th>
                        <td>
                            <span style="color: #e74c3c; font-weight: bold;">
                                ⏰ {{ $chargebackRecord->time_remaining }}
                            </span>
                        </td>
                    </tr>
                    @endif
                    <tr>
                        <th>Updated By:</th>
                        <td>{{ $chargebackRecord->user->name ?? 'System' }}</td>
                    </tr>
                    <tr>
                        <th>Updated At:</th>
                        <td>{{ $chargebackRecord->created_at->format('d M Y H:i:s') }}</td>
                    </tr>
                </table>
            </div>

            <!-- Remarks -->
            @if($chargebackRecord->remarks)
            <div class="remark-box">
                <strong>💬 Remarks:</strong><br>
                {{ $chargebackRecord->remarks }}
            </div>
            @endif

            <!-- Alert Specific Reminder -->
            @if($chargebackRecord->status === 'Alert' && $chargebackRecord->time_remaining)
            <div class="alert-reminder">
                <strong>⚠️ Action Required:</strong><br>
                This alert case must be resolved within <strong>{{ $chargebackRecord->time_remaining }}</strong>. 
                Please take necessary action to avoid automatic refund and merchant HIT.
            </div>
            @endif

            <!-- Role-specific message -->
            @if($recipientType === 'agent')
            <p>As the agent who created this booking, please review the dispute status and take appropriate action if needed.</p>
            @elseif($recipientType === 'mis')
            <p>This notification is for your monitoring and record-keeping purposes.</p>
            @elseif($recipientType === 'mis_manager')
            <p>Please oversee this case and ensure timely resolution by the concerned team.</p>
            @endif

            <!-- CTA Button -->
            <div style="text-align: center;">
                <a href="{{ route('support.bookings.show', $booking->id) }}" class="btn">
                    🔍 View Booking Details
                </a>
            </div>

            <div class="recipient-info">
                <small>
                    <strong>📧 Notification sent to:</strong> 
                    {{ ucfirst(str_replace('_', ' ', $recipientType)) }}
                </small>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p><strong>Flight Booking CRM</strong> - Customer Support Service</p>
            <p>This is an automated notification. Please do not reply to this email.</p>
            <p>© {{ date('Y') }} Flight Booking CRM. All rights reserved.</p>
        </div>
    </div>
</body>
</html>