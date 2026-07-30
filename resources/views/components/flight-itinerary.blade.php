<div class="itinerary-card border rounded mb-4 overflow-hidden">
    <div class="itinerary-header bg-primary text-white p-3 d-flex justify-content-between align-items-center">
        <span class="font-weight-bold"><i class="bi bi-airplane-engines mr-1"></i> Flight Details / Itinerary</span>
        <span>PNR: <strong>{{ $booking->airline_pnr ? $booking->airline_pnr : ($booking->gk_pnr ?: 'N/A') }}</strong></span>
    </div>

    @if ($booking->itinerary_image)
        <div class="p-4 text-center bg-light">
            <h6 class="mb-3 text-secondary text-left font-weight-bold">
                Airline: {{ $booking->airline_name ?? 'N/A' }} 
                @if($booking->airline_code) ({{ $booking->airline_code }}) @endif
            </h6>
            <img src="{{ asset('storage/' . $booking->itinerary_image) }}" alt="Flight Itinerary Screenshot" class="img-fluid rounded border shadow-sm" style="max-height: 650px;">
        </div>
    @elseif (isset($booking->segments) && $booking->segments->count() > 0)
        @foreach ($booking->segments as $index => $segment)
            <div class="flight-segment-card">

                <div class="segment-top">

                    <div class="segment-date">
                        {{ \Carbon\Carbon::parse($segment->departure_date)->format('D, M d') }}
                    </div>

                    <div class="segment-status">
                        Confirmed
                    </div>
                    <div class="segment-airline">
                        @if ($segment->airline && $segment->airline->logo)
                            <img src="{{ asset('storage/' . $segment->airline->logo) }}" alt="" class="airline-logo">
                        @endif
                        <strong>
                            {{ $segment->airline_code }}
                            {{ $segment->flight_number }}
                        </strong>

                        <span>
                            {{ $segment->cabin_class }}
                        </span>

                    </div>

                </div>

                <div class="segment-middle">

                    <div class="airport-left">

                        <div class="airport-code">
                            {{ $segment->from_airport }}
                        </div>

                        <div class="airport-time">
                            {{ \Carbon\Carbon::parse($segment->departure_time)->format('g:i A') }}
                        </div>

                        <div class="airport-name">
                            {{ $segment->from_city }}
                        </div>

                    </div>

                    <div class="segment-line"></div>

                    <div class="airport-right">

                        <div class="airport-code">
                            {{ $segment->to_airport }}
                        </div>

                        <div class="airport-time">
                            {{ \Carbon\Carbon::parse($segment->arrival_time)->format('g:i A') }}
                        </div>

                        <div class="airport-name">
                            {{ $segment->to_city }}
                        </div>

                    </div>

                </div>

                <div class="segment-duration">

                    Duration:

                    @php
                        $duration = '';

                        if ($segment->departure_time && $segment->arrival_time) {
                            $departure = \Carbon\Carbon::parse($segment->departure_time);
                            $arrival = \Carbon\Carbon::parse($segment->arrival_time);

                            $minutes = $departure->diffInMinutes($arrival);

                            $hours = floor($minutes / 60);
                            $mins = $minutes % 60;

                            $duration = $hours . 'h ' . $mins . 'm';
                        }
                    @endphp

                    {{ $duration }}

                </div>

            </div>

            @if (isset($booking->segments[$index + 1]))
                @php

                    $currentArrival = \Carbon\Carbon::parse(
                        $segment->departure_date->format('Y-m-d') .
                            ' ' .
                            \Carbon\Carbon::parse($segment->arrival_time)->format('H:i:s'),
                    );

                    $nextDeparture = \Carbon\Carbon::parse(
                        $booking->segments[$index + 1]->departure_date->format('Y-m-d') .
                            ' ' .
                            \Carbon\Carbon::parse($booking->segments[$index + 1]->departure_time)->format('H:i:s'),
                    );

                    $layoverMinutes = $currentArrival->diffInMinutes($nextDeparture);

                    $layoverHours = floor($layoverMinutes / 60);

                    $layoverRemain = $layoverMinutes % 60;

                @endphp

                <div class="layover-box">

                    {{ $layoverHours }}h {{ $layoverRemain }}m

                    TRANSIT AT

                    {{ $segment->to_city }}

                </div>
            @endif
        @endforeach
    @else
        <div class="p-4 text-center text-muted">
            <p class="mb-0">No flight itinerary image or segments available.</p>
        </div>
    @endif
</div>
<style>
    /* Your CSS styles here */
    .flight-segment-card {
        background: #fff;
        padding: 18px;
        border-bottom: 1px solid #e5e7eb;
    }

    .segment-top {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }

    .segment-date {
        font-size: 14px;
        font-weight: 700;
    }

    .segment-status {
        background: #d1fae5;
        color: #047857;
        padding: 6px 12px;
        font-weight: 700;
    }

    .segment-airline {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .airline-logo {
        width: 90px;
        max-height: 100%;
        object-fit: contain;
    }

    .segment-middle {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .airport-code {
        font-size: 52px;
        font-weight: 800;
        line-height: 1;
    }

    .airport-time {
        font-size: 28px;
        font-weight: 700;
    }

    .airport-name {
        font-size: 16px;
        color: #6b7280;
    }

    .segment-line {
        flex: 1;
        height: 1px;
        background: #d1d5db;
        margin: 0 30px;
    }

    .segment-duration {
        margin-top: 20px;
        font-size: 22px;
        font-weight: 600;
    }

    .layover-box {
        text-align: center;
        padding: 20px;
        font-size: 24px;
        font-weight: 700;
        border-top: 1px solid #ddd;
        border-bottom: 1px solid #ddd;
        background: #fafafa;
    }
</style>
