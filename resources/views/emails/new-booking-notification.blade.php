@component('mail::message')
    # New Booking Created

    Hello {{ $notifiable->name ?? 'User' }},

    A new booking has been created in the system and requires your attention.

    ## Booking Details

    **Booking Reference:** {{ $booking->booking_reference }}

    **Customer Information:**
    - Name: {{ $booking->customer_name }}
    - Email: {{ $booking->customer_email }}
    - Phone: {{ $booking->customer_phone }}

    **Booking Information:**
    - Service Type: {{ $booking->service_type }}
    - Service Provided: {{ $booking->service_provided }}
    - Booking Portal: {{ ucfirst($booking->booking_portal) }}
    - Call Type: {{ $booking->call_type }}
    - Booking Date: {{ $booking->booking_date->format('M d, Y') }}

    **Flight Details:**
    - From: {{ $booking->departure_city }}
    - To: {{ $booking->arrival_city }}
    - Departure Date: {{ $booking->departure_date ? $booking->departure_date->format('M d, Y') : 'N/A' }}
    @if ($booking->flight_type === 'roundtrip')
        - Return Date: {{ $booking->return_date ? $booking->return_date->format('M d, Y') : 'N/A' }}
    @endif
    - Airline: {{ $booking->airline_name }}
    - Flight Number: {{ $booking->flight_number ?? 'N/A' }}
    - Cabin Class: {{ $booking->cabin_class }}

    **Passengers:**
    - Adults: {{ $booking->adults }}
    - Children: {{ $booking->children }}
    - Infants: {{ $booking->infants }}
    - Total: {{ $booking->total_passengers }}

    **Financial Details:**
    - Currency: {{ $booking->currency }}
    - Amount Charged: {{ $booking->currency }} {{ number_format($booking->amount_charged, 2) }}
    - Amount Paid to Airline: {{ $booking->currency }} {{ number_format($booking->amount_paid_airline, 2) }}
    @if ($booking->total_mco)
        - Total MCO: {{ $booking->currency }} {{ number_format($booking->total_mco, 2) }}
    @endif

    **Status:** {{ ucfirst(str_replace('_', ' ', $booking->status)) }}

    **Created By:** {{ $booking->user->name ?? 'System' }}

    @component('mail::button', ['url' => route('bookings.show', $booking->id)])
        View Booking
    @endcomponent

    Please review the booking details in the system and proceed with necessary actions.

    Thank you,
    {{ config('app.name') }}
@endcomponent
