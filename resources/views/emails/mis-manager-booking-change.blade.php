@component('mail::message')
# Booking Change Notification

**Booking ID:** {{ $change->booking_id }}  
**Customer Name:** {{ $change->customer_name }}  
**Agent Name:** {{ $change->agent_name }}  
**MIS Manager:** {{ $change->mis_manager_name }}  
**Timestamp:** {{ $change->created_at->format('M d, Y H:i:s') }}

---

## Fields Changed:
@if($change->changed_fields && count($change->changed_fields) > 0)
| Field | Old Value | New Value |
|-------|-----------|-----------|
@foreach($change->changed_fields as $field)
| {{ $field }} | {{ $change->old_values[$field] ?? 'N/A' }} | {{ $change->new_values[$field] ?? 'N/A' }} |
@endforeach
@else
No field changes recorded
@endif

---

## Manager Remarks:
@if($change->manager_remark)
{{ $change->manager_remark }}
@else
No remarks provided
@endif

---

@component('mail::button', ['url' => route('admin.bookings.show', $change->booking_id)])
View Booking Details
@endcomponent

Thanks,  
{{ config('app.name') }}
@endcomponent
