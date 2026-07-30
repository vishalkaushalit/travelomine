<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>E-Ticket</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        /* Additional styles for PDF rendering */
        body {
            margin: 0;
            padding: 20px;
            font-family: 'Source Sans Pro', Arial, sans-serif;
        }

        .ticket-container {
            max-width: 800px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .ticket-header {
            background: linear-gradient(135deg, #1a237e, #0d47a1);
            padding: 25px 30px;
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .ticket-header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }

        .ticket-header .badge {
            background: #ffc107;
            color: #000;
            padding: 6px 18px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 14px;
        }

        .ticket-body {
            padding: 30px;
        }

        .flight-info {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 25px;
        }

        .info-group {
            border-bottom: 2px solid #e9ecef;
            padding-bottom: 10px;
        }

        .info-group label {
            font-size: 12px;
            color: #6c757d;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 600;
        }

        .info-group .value {
            font-size: 18px;
            font-weight: 600;
            color: #1a237e;
            margin-top: 4px;
        }

        .passenger-details {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }

        .passenger-details table {
            width: 100%;
            border-collapse: collapse;
        }

        .passenger-details td {
            padding: 8px 12px;
            border-bottom: 1px solid #dee2e6;
        }

        .passenger-details tr:last-child td {
            border-bottom: none;
        }

        .passenger-details .label {
            font-weight: 600;
            color: #495057;
        }

        .ticket-footer {
            background: #f8f9fa;
            padding: 20px 30px;
            text-align: center;
            border-top: 2px solid #e9ecef;
            font-size: 12px;
            color: #6c757d;
        }

        .status-badge {
            display: inline-block;
            padding: 4px 16px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
        }

        .status-badge.confirmed {
            background: #d4edda;
            color: #155724;
        }

        .flight-route {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 15px 0;
        }

        .flight-route .city {
            font-size: 20px;
            font-weight: 700;
            color: #1a237e;
        }

        .flight-route .arrow {
            font-size: 24px;
            color: #0d47a1;
        }

        .flight-route .date-time {
            font-size: 14px;
            color: #6c757d;
        }

        .qr-code {
            text-align: center;
            margin: 20px 0;
        }

        .editable-section {
            border: 2px dashed #dee2e6;
            padding: 10px;
            margin: 10px 0;
            border-radius: 6px;
            position: relative;
        }

        .section-label {
            position: absolute;
            top: -12px;
            left: 10px;
            background: white;
            padding: 0 8px;
            font-size: 11px;
            color: #6c757d;
            font-weight: 600;
        }

        /* Hide edit indicators in PDF */
        .section-label,
        .editable-section {
            border: none !important;
        }

        .editable-section .section-label {
            display: none !important;
        }
    </style>
</head>

<body>
    {!! $content !!}
</body>

</html>
