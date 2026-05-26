<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .header {
            background-color: #007bff;
            color: white;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .content {
            margin-bottom: 20px;
        }
        .details {
            background-color: #f8f9fa;
            padding: 15px;
            border-left: 4px solid #007bff;
            margin: 15px 0;
        }
        .message-box {
            background-color: #fff3cd;
            padding: 15px;
            border-left: 4px solid #ffc107;
            margin: 15px 0;
            border-radius: 3px;
        }
        .action-button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 15px 0;
        }
        .footer {
            border-top: 1px solid #ddd;
            padding-top: 15px;
            font-size: 12px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>New Booking Assignment for Changes</h2>
        </div>

        <div class="content">
            <p>Hello Changes Team,</p>

            <p>A new booking has been assigned to the changes team for processing.</p>

            <div class="details">
                <strong>Assignment Details:</strong><br><br>
                <strong>Assigned By:</strong> {{ $assignedByName }}<br>
                <strong>Booking Reference:</strong> {{ $booking->booking_reference }}<br>
                <strong>Customer:</strong> {{ $booking->customer_name }}<br>
                <strong>Email:</strong> {{ $booking->customer_email ?? 'N/A' }}<br>
                <strong>Phone:</strong> {{ $booking->customer_phone ?? 'N/A' }}<br>
                <strong>Flight Type:</strong> {{ ucfirst($booking->flight_type ?? 'N/A') }}<br>
                <strong>Route:</strong> {{ $booking->departure_city }} → {{ $booking->arrival_city }}<br>
                <strong>Departure Date:</strong> {{ $booking->departure_date ? $booking->departure_date->format('d M Y') : 'N/A' }}<br>
                <strong>Passengers:</strong> {{ $booking->total_passengers }} (Adults: {{ $booking->adults }}, Children: {{ $booking->children }}, Infants: {{ $booking->infants }})<br>
                <strong>Amount:</strong> {{ $booking->currency ?? 'USD' }} {{ number_format($booking->amount_charged, 2) }}
            </div>

            <div class="message-box">
                <strong>Change Request Details from Agent:</strong><br><br>
                {!! nl2br(e($assignment->message)) !!}
            </div>

            <p>
                <a href="{{ url('/charge/assignments/' . $assignment->id) }}" class="action-button">View Assignment</a>
            </p>

            <p>Please review the assignment and take necessary action as soon as possible.</p>
        </div>

        <div class="footer">
            <p>This is an automated email. Please do not reply to this message.</p>
            <p>&copy; {{ date('Y') }} Calling Genie. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
