<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Preview Authorization Email | {{ $booking->booking_reference }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        :root {
            --brand-primary: #1a237e;
            --brand-primary-soft: #283593;
            --brand-accent: #ffb300;
            --brand-bg: #f3f5fb;
            --text-main: #1f2933;
            --text-muted: #6b7280;
            --border-soft: #e0e7ff;
        }

        body {
            margin: 0;
            padding: 0;
            background: radial-gradient(circle at top, #e8ebff 0, #f8fafc 55%, #f3f5fb 100%);
            font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            color: var(--text-main);
        }
        table{
            width: 100% !important;
        
        }
        table tr th{
            padding: 6px 0;
            line-height: 1.6;
        }
        .page-shell {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .top-bar {
            background: rgba(26, 35, 126, 0.06);
            border-bottom: 1px solid rgba(26, 35, 126, 0.12);
            backdrop-filter: blur(8px);
        }

        .top-bar-inner {
            max-width: 960px;
            margin: 0 auto;
            padding: 10px 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 13px;
        }

        .top-bar-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 10px;
            border-radius: 999px;
            background: rgba(25, 118, 210, 0.06);
            color: #0b5394;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.06em;
        }

        .top-bar-actions a {
            font-size: 13px;
        }

        .preview-shell {
            flex: 1;
            display: flex;
            align-items: flex-start;
            justify-content: center;
            padding: 32px 12px 40px;
        }

        .email-card {
            width: 100%;
            max-width: 780px;
            background: #ffffff;
            border-radius: 18px;
            box-shadow:
                0 22px 45px rgba(15, 23, 42, 0.16),
                0 0 0 1px rgba(15, 23, 42, 0.02);
            overflow: hidden;
        }

        /* Header */
        .email-header {
            position: relative;
            padding: 22px 26px 20px;
            background: radial-gradient(circle at top left, #5c6bc0 0, #1a237e 38%, #121858 100%);
            color: #e8ebff;
        }

        .email-header-main {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
        }

        .brand-block {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .brand-logo {
            width: 32px;
            height: 32px;
            border-radius: 12px;
            background: radial-gradient(circle at 30% 0, #ffecb3 0, #ffb300 45%, #ff6f00 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #1a237e;
            font-weight: 800;
            font-size: 16px;
            box-shadow: 0 8px 18px rgba(0, 0, 0, 0.26);
        }

        .brand-text-title {
            font-size: 11px;
            letter-spacing: 0.22em;
            text-transform: uppercase;
            opacity: 0.7;
        }

        .brand-text-main {
            font-size: 17px;
            font-weight: 600;
            letter-spacing: 0.06em;
        }

        .status-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 3px 10px;
            border-radius: 999px;
            background: rgba(76, 175, 80, 0.12);
            color: #c8e6c9;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.12em;
            font-weight: 600;
        }

        .status-dot {
            width: 7px;
            height: 7px;
            border-radius: 999px;
            background: #4caf50;
            box-shadow: 0 0 0 4px rgba(76, 175, 80, 0.35);
        }

        .email-header-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 20px;
            font-size: 12px;
            opacity: 0.86;
        }

        .email-header-meta span {
            display: inline-flex;
            align-items: baseline;
            gap: 6px;
        }

        .badge-ref {
            padding: 3px 8px;
            border-radius: 999px;
            background: rgba(13, 71, 161, 0.28);
            font-weight: 600;
            font-size: 11px;
        }

        /* Body */
        .email-body {
            padding: 26px 26px 20px;
            background: linear-gradient(180deg, #f9fafb 0, #ffffff 38%, #f9fafb 100%);
        }

        .editable-content-area {
            border-radius: 12px;
            border: 1px solid rgba(148, 163, 184, 0.3);
            background: #ffffff;
            padding: 18px 18px 16px;
            font-size: 14px;
            color: var(--text-main);
        }

        .editable-content-area p {
            margin-bottom: 8px;
            color: var(--text-main);
        }

        .editable-content-area small,
        .editable-content-area span {
            color: var(--text-muted);
        }

        /* Itinerary card */
        .itinerary-card {
            margin-top: 24px;
            border-radius: 14px;
            border: 1px solid var(--border-soft);
            background: radial-gradient(circle at top left, #ffffff 0, #f4f6ff 52%, #edf2ff 100%);
            overflow: hidden;
        }

        .itinerary-header {
            padding: 10px 18px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: linear-gradient(90deg, #e8eaf6 0, #c5cae9 50%, #e8eaf6 100%);
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.12em;
            color: #1a237e;
            font-weight: 600;
        }

        .itinerary-header span:last-child {
            font-size: 11px;
            color: #283593;
            background: rgba(255, 255, 255, 0.7);
            padding: 3px 8px;
            border-radius: 999px;
        }

        .segment-row {
            padding: 16px 18px 8px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 18px;
        }

        .city-block {
            min-width: 140px;
        }

        .city-code {
            font-size: 12px;
            font-weight: 600;
            letter-spacing: 0.16em;
            text-transform: uppercase;
            margin: 0;
            color: #111827;
        }

        .city-name {
            font-size: 17px;
            font-weight: 500;
            text-transform: uppercase;
            margin: 0;
            color: #4b5563;
        }

        .flight-path {
            flex: 1;
            text-align: center;
            position: relative;
        }

        .flight-path-line {
            border-bottom: 1px dashed rgba(55, 65, 81, 0.35);
            margin-bottom: 6px;
        }

        .flight-icon {
            display: inline-block;
            font-size: 20px;
            color: #1a237e;
            background: #ffffff;
            padding: 0 10px;
            border-radius: 999px;
            box-shadow: 0 3px 7px rgba(15, 23, 42, 0.12);
            position: relative;
            top: 8px;
        }

        .segment-meta {
            padding: 0 18px 14px;
            display: flex;
            flex-wrap: wrap;
            gap: 8px 18px;
            font-size: 11px;
            color: var(--text-muted);
        }

        .segment-meta span {
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .segment-meta-label {
            font-weight: 600;
            color: #4b5563;
        }

        /* Auth box */
        .auth-box {
            margin-top: 24px;
            text-align: center;
            border-radius: 14px;
            padding: 18px 18px 16px;
            border: 1px solid rgba(245, 158, 11, 0.38);
            background: linear-gradient(
                135deg,
                rgba(255, 243, 205, 0.85) 0,
                rgba(255, 249, 230, 0.96) 45%,
                #ffffff 100%
            );
        }

        .auth-box p {
            margin-bottom: 4px;
        }

        .auth-box .amount-text {
            font-size: 22px;
            font-weight: 700;
            color: #92400e;
            letter-spacing: 0.04em;
        }

        .auth-box .sub-label {
            font-size: 12px;
            color: #92400e;
            text-transform: uppercase;
            letter-spacing: 0.16em;
        }

        /* Footer actions */
        .page-actions {
            text-align: center;
            margin-top: 18px;
        }

        .primary-send-btn {
            border-radius: 999px;
            padding: 10px 32px;
            font-weight: 600;
            font-size: 14px;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            border: none;
            background: linear-gradient(135deg, #1e40af 0, #1d4ed8 55%, #2563eb 100%);
            box-shadow:
                0 12px 25px rgba(37, 99, 235, 0.45),
                0 0 0 1px rgba(255, 255, 255, 0.1);
        }

        .primary-send-btn:hover {
            background: linear-gradient(135deg, #1d4ed8 0, #2563eb 45%, #1d4ed8 100%);
        }

        .helper-text {
            font-size: 11px;
            color: var(--text-muted);
            margin-top: 6px;
        }

        @media (max-width: 576px) {
            .preview-shell {
                padding: 18px 10px 28px;
            }

            .email-card {
                border-radius: 0;
                box-shadow: none;
            }

            .email-header,
            .email-body {
                padding: 18px 16px 16px;
            }

            .segment-row {
                flex-direction: column;
                align-items: stretch;
                gap: 12px;
            }

            .city-block {
                min-width: 0;
            }

            .email-header-meta {
                flex-direction: column;
                align-items: flex-start;
                gap: 6px;
            }
        }
    </style>
</head>
<body>
<div class="page-shell">
    <div class="top-bar">
        <div class="top-bar-inner">
            <div class="d-flex align-items-center gap-2">
                <span class="top-bar-badge">
                    <span>Preview</span>
                </span>
                <span class="text-muted">Customer-facing authorization email layout</span>
            </div>

            <div class="top-bar-actions">
                <a href="{{ route('charge.authorize.edit', $booking->id) }}" class="btn btn-sm btn-outline-primary">
                    ← Back to Editor
                </a>
            </div>
        </div>
    </div>

    <div class="preview-shell">
        <div class="email-card">

            <div class="email-header">
                <div class="email-header-main">
                    <div class="brand-block">
                        
                        <div>
                            <div class="brand-text-title">Payment Authorization</div>
                            <div class="brand-text-main">Flight Payment Consent</div>
                        </div>
                    </div>

                    <div class="text-end">
                        <div class="status-pill">
                            <span class="status-dot"></span>
                            <span>Ready to send</span>
                        </div>
                    </div>
                </div>

                <div class="email-header-meta">
                    <span>
                        <span class="badge-ref">Ref: {{ $booking->booking_reference }}</span>
                    </span>
                    <span>
                        <span class="text-xs">Previewing as customer email</span>
                    </span>
                </div>
            </div>

            <div class="email-body">
                <div class="editable-content-area">
                    {!! $finalContent !!}
                </div>

                <div class="itinerary-card">
                    <div class="itinerary-header">
                        <span>Flight details</span>
                        <span>PNR: {{ $booking->gk_pnr }}</span>
                    </div>

                    @foreach($booking->segments as $segment)
                        <div class="segment-row">
                            <div class="city-block text-start">
                                <p class="city-code">{{ $segment->from_airport ?? 'DEPARTURE' }}</p>
                                <p class="city-name">{{ $segment->from_city }}</p>
                            </div>

                            <div class="flight-path">
                                <div class="flight-path-line"></div>
                                <span class="flight-icon">✈</span>
                            </div>

                            <div class="city-block text-end">
                                <p class="city-code">{{ $segment->to_airport ?? 'ARRIVAL' }}</p>
                                <p class="city-name">{{ $segment->to_city }}</p>
                            </div>
                        </div>

                        <div class="segment-meta">
                            <span>
                                <span class="segment-meta-label">Flight</span>
                                {{ $segment->airline_name }} {{ $segment->flight_number }}
                            </span>
                            <span>
                                <span class="segment-meta-label">Date</span>
                                {{ \Carbon\Carbon::parse($segment->departure_date)->format('D, d M Y') }}
                            </span>
                        </div>
                    @endforeach
                </div>

                <div class="auth-box">
                    <p class="small text-muted mb-1">
                        By replying to this email or clicking the consent link, you authorize
                    </p>
                    <div class="amount-text">
                        {{ $booking->currency }} {{ number_format($booking->amount_charged, 2) }}
                    </div>
                    <p class="sub-label mb-0">
                        Total charge amount · All inclusive
                    </p>
                </div>
            </div>

        </div>
    </div>

    <div class="page-actions">
    <form action="{{ route('charge.authorize.send', $booking->id) }}" method="POST">
        @csrf
        <textarea name="final_content" style="display:none;">{{ $finalContent }}</textarea>
        <button type="submit" class="btn btn-primary primary-send-btn">
            Confirm &amp; Send to Customer
        </button>
    </form>
    <div class="helper-text">
        This sends the authorization email using the design shown above.
    </div>
</div>

</div>
</body>
</html>
