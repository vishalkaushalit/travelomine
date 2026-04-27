<x-mail::message>
# Authorization for Seat Assignment Confirmation

Dear {{ $booking->customer_name ?? 'Valued Customer' }},

Greetings of the day!

As per our conversation and as agreed, we have assigned the seats on your reservation with the airline under Confirmation **# {{ $confirmationNumber }}**. Please see the details below.

<x-mail::panel>
**Total cost for all passengers:** USD {{ number_format($totalCost, 2) }} (all incl. taxes and fees).
</x-mail::panel>

As per our telephonic conversation I, **{{ strtoupper($booking->customer_name ?? 'CUSTOMER') }}**, authorize the airline/TraveloMile to process the above-mentioned charges under their respective merchants for charging my **{{ $cardLastFour }}** card for assigning the seats on the below-mentioned itinerary.

This payment authorization is for the amount indicated above and is valid for one-time use only. I certify that I am **{{ strtoupper($booking->customer_name ?? 'CUSTOMER') }}**, an authorized user of this card and that I will not dispute the payment with my credit/debit card company/bank.

Kindly confirm your acceptance of the terms and agreement to the declaration by replying to this email with **'I Agree'** or **'I Authorize'**.

## Charges Description

1. USD {{ number_format($totalCost, 2) }} (TraveloMile, all incl. taxes and fees)

## Passenger Details

| S. No. | Type | First Name | Middle Name | Last Name | Gender | DOB | Price |
|--------|------|-----------|------------|-----------|--------|-----|-------|
@foreach($booking->passangers ?? [] as $passenger)
| {{ $loop->iteration }} | {{ $passenger->passenger_type ?? 'ADT' }} | {{ strtoupper($passenger->first_name) }} | {{ strtoupper($passenger->middle_name ?? '') }} | {{ strtoupper($passenger->last_name) }} | {{ ucfirst($passenger->gender ?? 'N/A') }} | {{ \Carbon\Carbon::parse($passenger->dob)->format('M d, Y') ?? 'N/A' }} | USD {{ number_format($totalCost / count($booking->passangers ?? [1]), 2) }} |
@endforeach

## Seat Selection

@foreach($seatAssignments as $passengerName => $seats)
**{{ strtoupper($passengerName) }}**
{{ implode(', ', (array)$seats) }}

@endforeach

## Flight Itinerary

@if($booking->segments && $booking->segments->count())
### Outbound Flight

@foreach($booking->segments->where('segment_type', 'outbound') as $segment)
**{{ \Carbon\Carbon::parse($segment->departure_date)->format('D, M d') }}**

**{{ $segment->flight_number }}** - {{ $segment->cabin_class }}

**{{ strtoupper($segment->from_code) }}** - {{ $segment->from_city }}  
{{ \Carbon\Carbon::parse($segment->departure_time)->format('h:i A') }} — {{ $segment->from_airport }}

↓ Duration: {{ $segment->duration ?? 'N/A' }}

**{{ strtoupper($segment->to_code) }}** - {{ $segment->to_city }}  
{{ \Carbon\Carbon::parse($segment->arrival_time)->format('h:i A') }} — {{ $segment->to_airport }}

@if($segment->transit_duration)
{{ $segment->transit_duration }} transit at {{ $segment->to_city }}
@endif

@endforeach

### Return Flight

@foreach($booking->segments->where('segment_type', 'return') as $segment)
**{{ \Carbon\Carbon::parse($segment->departure_date)->format('D, M d') }}**

**{{ $segment->flight_number }}** - {{ $segment->cabin_class }}

**{{ strtoupper($segment->from_code) }}** - {{ $segment->from_city }}  
{{ \Carbon\Carbon::parse($segment->departure_time)->format('h:i A') }} — {{ $segment->from_airport }}

↓ Duration: {{ $segment->duration ?? 'N/A' }}

**{{ strtoupper($segment->to_code) }}** - {{ $segment->to_city }}  
{{ \Carbon\Carbon::parse($segment->arrival_time)->format('h:i A') }} — {{ $segment->to_airport }}

@if($segment->transit_duration)
{{ $segment->transit_duration }} transit at {{ $segment->to_city }}
@endif

@endforeach
@endif

## Purchase Summary

### Payment Type - Credit/Debit Card Authorization

| Field | Details |
|-------|---------|
| Card Holder Name | {{ strtoupper($booking->customer_name ?? 'N/A') }} |
| Card Type | {{ $cardType }} |
| Card Number | {{ $cardLastFour }} |
| Billing Address | {{ $booking->billing_address ?? 'N/A' }} |
| Phone Number | {{ $booking->phone ?? 'N/A' }} |
| Email | {{ $booking->email ?? 'N/A' }} |
| Total Amount | USD {{ number_format($totalCost, 2) }} |
| Transaction Date | {{ now()->format('M d, Y') }} |

---

**Please Note:**
- Review the names, dates, cities, and departure/arrival times carefully.
- Baggage fees may apply. Please check with the airline for the most up-to-date baggage policies.

**Important:**
Your e-tickets will be sent to you via email within 24 hours, or sooner if there is no delay from the airline's side. Please note that fares are not guaranteed until payment is received and tickets are issued.

**Note:**
As agreed, your credit card may be charged in split transactions, not exceeding the total amount. All transactions are for service fees and are **100% non-refundable**. Airline tickets are non-refundable; however, you may be eligible for a refund within 24 hours of purchase, depending on the airline's policy.

**Disclaimer:**
TraveloMile is an independent travel agency with no third-party association. All service fees and convenience fees are non-refundable.

**For Assistance:**
In case of any discrepancies or if an amendment is required, please contact us within 24 hours at **+1 888-476-0932** or email us at **reservation@travelomile.com**

**Important Information:**
- Passenger names must be the same as on the passport (International Travel) OR any government-approved photo ID proof for Domestic travel.
- All passengers are recommended to be present at the airport 3 hours before departure for international departures, and 2 hours before domestic travel.
- All international flights must be confirmed 72 hours before departure.

**For Changes or Cancellations:**
Call us at **+1 888-476-0932**. All changes must be made prior to the flight's departure.

Still have questions? Call us at **+1 888-476-0932**. Our agents are available 24 hours a day, 7 days a week to assist you.

We value your business and look forward to serving your travel needs soon.

Best Regards,  
**Reservation Desk**  
TraveloMile
</x-mail::message>
