<x-mail::message>
# Payment Request for Booking #{{ $link->booking_id }}

Hello {{ $link->customer_name }},

We have prepared your secure payment link for your booking. Please review the booking details and complete the payment using the button below.

<x-mail::panel>
**Amount Due:** ${{ number_format($link->amount, 2) }} {{ $link->currency }}  
**Booking ID:** #{{ $link->booking_id }}  
**Merchant:** {{ $merchant->name ?? 'N/A' }}  
**Link Expiry:** {{ $link->expires_at?->format('d M Y, h:i A') ?? 'No expiry' }}
</x-mail::panel>

## Booking Details

**Customer Name:** {{ $link->customer_name }}  
**Billing Email:** {{ $link->billing_email ?? 'N/A' }}  
**Billing Phone:** {{ $link->billing_phone ?? 'N/A' }}  
**Billing Address:** {{ $link->billing_address ?? 'N/A' }}

@if($booking)
**Service:** {{ $booking->service_provided ?? 'Flight' }}  
**Booking Type:** {{ $booking->service_type ?? 'N/A' }}  
@endif

@if($booking && method_exists($booking, 'segments') && $booking->segments && $booking->segments->count())
### Travel Itinerary

@foreach($booking->segments as $segment)
- **{{ $segment->from_city }} → {{ $segment->to_city }}** on {{ \Carbon\Carbon::parse($segment->departure_date)->format('d M Y') }} ({{ $segment->cabin_class }})
@endforeach
@endif

@if($booking && method_exists($booking, 'passangers') && $booking->passangers && $booking->passangers->count())
### Passenger Details

@foreach($booking->passangers as $passenger)
- **{{ $loop->iteration }}.** [{{ $passenger->passenger_type ?? 'N/A' }}] {{ $passenger->first_name }} {{ $passenger->middle_name ? $passenger->middle_name.' ' : '' }}{{ $passenger->last_name }} — {{ ucfirst($passenger->gender ?? 'N/A') }}{{ $passenger->dob ? ', DOB: '.\Carbon\Carbon::parse($passenger->dob)->format('d M Y') : '' }}
@endforeach
@endif

<x-mail::button :url="$paymentUrl" color="success">
Pay ${{ number_format($link->amount, 2) }} Now
</x-mail::button>

If the button does not work, copy and paste this link into your browser:

{{ $paymentUrl }}

<x-mail::subcopy>
This is a secure one-time payment link for booking #{{ $link->booking_id }}. Do not share this link with anyone. If you did not expect this email, please contact our support team.
</x-mail::subcopy>

Thanks,<br>
{{ $merchant->name ?? 'N/A' }}
</x-mail::message>
