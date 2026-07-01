{{-- resources/views/admin/bookings/ticket-edit.blade.php --}}
@extends('layouts.admin')

@section('title', 'Edit Ticket - ' . ($booking->booking_reference ?? $booking->id))

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-9">
            <div class="card">
                <div class="card-header">
                    <h4>Edit Ticket Details</h4>
                    <p class="text-muted">Edit the ticket details below. Changes will be reflected in the generated PDF.</p>
                </div>
                <div class="card-body">
                    <form id="ticketForm" action="{{ route('admin.bookings.ticket.generate', $booking->id) }}" method="POST">
                        @csrf
                         {{-- Passengers --}}
                        <div class="section-box">
                            <div class="form-group">
                                <label for="flight_type">Flight Type</label>
                                <p>current flight type is : <span class="summary-value" style="color: red; font-size: 1.2em; font-weight: bold;">{{ ucwords(str_replace('_', ' ', $booking->flight_type ?? 'not defined')) }}</span></p>
                                <select name="flight_type" id="flight_type" class="form-control">
                                    <option value="multi_city" {{ $booking->flight_type == 'multi_city' ? 'selected' : '' }}>Multi City</option>
                                    <option value="round_trip" {{ $booking->flight_type == 'round_trip' ? 'selected' : '' }}>Round Trip</option>
                                    <option value="one_way" {{ $booking->flight_type == 'one_way' ? 'selected' : '' }}>One Way</option>
                                </select>
                            </div>
                            <h5 class="section-title">Passenger Details</h5>
                            @foreach($booking->passengers as $index => $passenger)
                            <div class="passenger-card">
                                <h6>Passenger {{ $index + 1 }}</h6>
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Title</label>
                                            <select name="passengers[{{ $index }}][title]" class="form-control">
                                               <option value="Mr"
                                               {{ old("passengers.{$index}.title", $passenger->title) == 'Mr' ? 'selected' : '' }}>
                                               Mr
                                               </option>

<option value="Mrs"
    {{ old("passengers.{$index}.title", $passenger->title) == 'Mrs' ? 'selected' : '' }}>
    Mrs
</option>

<option value="Miss"
    {{ old("passengers.{$index}.title", $passenger->title) == 'Miss' ? 'selected' : '' }}>
    Miss
</option>

<option value="Dr"
    {{ old("passengers.{$index}.title", $passenger->title) == 'Dr' ? 'selected' : '' }}>
    Dr
</option>

<option value="Master"
    {{ old("passengers.{$index}.title", $passenger->title) == 'Master' ? 'selected' : '' }}>
    Master
</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>First Name</label>
                                            <input type="text" name="passengers[{{ $index }}][first_name]" class="form-control" 
                                                   value="{{ old("passengers.{$index}.first_name", $passenger->first_name) }}">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Last Name</label>
                                            <input type="text" name="passengers[{{ $index }}][last_name]" class="form-control" 
                                                   value="{{ old("passengers.{$index}.last_name", $passenger->last_name) }}">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Passenger Type</label>
                                            <select name="passengers[{{ $index }}][passenger_type]" class="form-control">
                                               <option value="ADT"
    {{ old("passengers.{$index}.passenger_type", $passenger->passenger_type) == 'ADT' ? 'selected' : '' }}>
    Adult
</option>

<option value="CHD"
    {{ old("passengers.{$index}.passenger_type", $passenger->passenger_type) == 'CHD' ? 'selected' : '' }}>
    Child
</option>

<option value="INF"
    {{ old("passengers.{$index}.passenger_type", $passenger->passenger_type) == 'INF' ? 'selected' : '' }}>
    Infant
</option>

<option value="INL"
    {{ old("passengers.{$index}.passenger_type", $passenger->passenger_type) == 'INL' ? 'selected' : '' }}>
    Infant (Lap)
</option>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Ticket Number</label>
                                            <input type="text" name="passengers[{{ $index }}][ticket_number]" class="form-control" 
                                                   value="{{ old("passengers.{$index}.ticket_number", $passenger->ticket_number) }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Seat Number</label>
                                            <input type="text" name="passengers[{{ $index }}][seat_number]" class="form-control" 
                                                   value="{{ old("passengers.{$index}.seat_number", $passenger->seat_number) }}">
                                        </div>
                                    </div>
                                </div>
                                
                                {{-- Show Cabin Class with Passenger --}}
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>Passenger Cabin Class</label>
                                            <input type="text" class="form-control" 
                                                   value="{{ $booking->cabin_class ?? 'N/A' }}" disabled>
                                            <small class="text-muted">Cabin class applied to all passengers</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>

                        {{-- Flight Segments --}}
                        <div class="section-box">
                            <h5 class="section-title">Flight Segments</h5>
                            @foreach($booking->segments as $index => $segment)
                            <div class="segment-card">
                                <h6>Segment {{ $index + 1 }}</h6>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Flight Number</label>
                                            <input type="text" name="segments[{{ $index }}][flight_number]" class="form-control" 
                                                   value="{{ old("segments.{$index}.flight_number", $segment->flight_number) }}">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Airline Name</label>
                                            <input type="text" name="segments[{{ $index }}][airline_name]" class="form-control" 
                                                   value="{{ old("segments.{$index}.airline_name", $segment->airline_name) }}">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Airline Code</label>
                                            <input type="text" name="segments[{{ $index }}][airline_code]" class="form-control" 
                                                   value="{{ old("segments.{$index}.airline_code", $segment->airline_code ?? '') }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>From City</label>
                                            <input type="text" name="segments[{{ $index }}][from_city]" class="form-control" 
                                                   value="{{ old("segments.{$index}.from_city", $segment->from_city) }}">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>From Airport</label>
                                            <input type="text" name="segments[{{ $index }}][from_airport]" class="form-control" 
                                                   value="{{ old("segments.{$index}.from_airport", $segment->from_airport) }}">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>To City</label>
                                            <input type="text" name="segments[{{ $index }}][to_city]" class="form-control" 
                                                   value="{{ old("segments.{$index}.to_city", $segment->to_city) }}">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>To Airport</label>
                                            <input type="text" name="segments[{{ $index }}][to_airport]" class="form-control" 
                                                   value="{{ old("segments.{$index}.to_airport", $segment->to_airport) }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Departure Time</label>
                                            <input type="datetime-local" name="segments[{{ $index }}][departure_time]" class="form-control" 
                                                   value="{{ old("segments.{$index}.departure_time", $segment->departure_time ? \Carbon\Carbon::parse($segment->departure_time)->format('Y-m-d\TH:i') : '') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Arrival Time</label>
                                            <input type="datetime-local" name="segments[{{ $index }}][arrival_time]" class="form-control" 
                                                   value="{{ old("segments.{$index}.arrival_time", $segment->arrival_time ? \Carbon\Carbon::parse($segment->arrival_time)->format('Y-m-d\TH:i') : '') }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        {{-- Right Sidebar --}}
        <div class="col-md-3">
            <div class="card sticky-top">
                <div class="card-header">
                    <h5>Ticket Options</h5>
                </div>
                <div class="card-body">
                    {{-- Optional Fields --}}
                    <div class="optional-fields">
                        <h6>Additional Fields</h6>
                        <p class="text-muted small">Select fields to include in ticket</p>
                        
                    
                            


                        <div class="form-group">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="showPassport" 
                                       name="optional_fields[passport_number]" 
                                       {{ isset($optionalFields['passport_number']) && $optionalFields['passport_number'] ? 'checked' : '' }}
                                       onchange="toggleOptionalField('passport')">
                                <label class="custom-control-label" for="showPassport">Passport Numbers</label>
                            </div>
                            <div id="passportFields" style="{{ isset($optionalFields['passport_number']) && $optionalFields['passport_number'] ? '' : 'display:none;' }} margin-top:10px;">
                                @foreach($booking->passengers as $index => $passenger)
                                <div class="form-group">
                                    <label>Passenger {{ $index + 1 }} Passport</label>
                                    <input type="text" name="passport_numbers[{{ $index }}]" class="form-control form-control-sm" 
                                           placeholder="Enter passport number"
                                           value="{{ old("passport_numbers.{$index}", $ticketData['passport_numbers'][$index] ?? '') }}">
                                </div>
                                @endforeach
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="showBaggage" 
                                       name="optional_fields[baggage]" 
                                       {{ isset($optionalFields['baggage']) && $optionalFields['baggage'] ? 'checked' : '' }}
                                       onchange="toggleOptionalField('baggage')">
                                <label class="custom-control-label" for="showBaggage">Baggage Details</label>
                            </div>
                            <div id="baggageFields" style="{{ isset($optionalFields['baggage']) && $optionalFields['baggage'] ? '' : 'display:none;' }} margin-top:10px;">
                                <div class="form-group">
                                    <label>Baggage Information</label>
                                    <textarea name="baggage_info" class="form-control form-control-sm" rows="3" 
                                              placeholder="Enter baggage details">{{ old('baggage_info', $ticketData['baggage_info'] ?? '') }}</textarea>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="showPet" 
                                       name="optional_fields[pet]" 
                                       {{ isset($optionalFields['pet']) && $optionalFields['pet'] ? 'checked' : '' }}
                                       onchange="toggleOptionalField('pet')">
                                <label class="custom-control-label" for="showPet">Pet Information</label>
                            </div>
                            <div id="petFields" style="{{ isset($optionalFields['pet']) && $optionalFields['pet'] ? '' : 'display:none;' }} margin-top:10px;">
                                <div class="form-group">
                                    <label>Pet Details</label>
                                    <textarea name="pet_info" class="form-control form-control-sm" rows="3" 
                                              placeholder="Enter pet details">{{ old('pet_info', $ticketData['pet_info'] ?? '') }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <hr>
                    
                    {{-- Action Buttons --}}
                    <div class="action-buttons">
                        <button type="submit" form="ticketForm" class="btn btn-primary btn-block">
                            <i class="fas fa-file-pdf"></i> Generate PDF
                        </button>
                        <button type="button" class="btn btn-secondary btn-block mt-2" onclick="window.history.back()">
                            <i class="fas fa-arrow-left"></i> Cancel
                        </button>
                    </div>
                    
                    <hr>
                    
                    {{-- Quick Actions --}}
                    <div class="quick-actions">
                        <h6>Quick Actions</h6>
                        <button type="button" class="btn btn-outline-info btn-sm btn-block" onclick="resetFields()">
                            <i class="fas fa-undo"></i> Reset to Original
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.section-box {
    background: #f8f9fa;
    padding: 20px;
    margin-bottom: 25px;
    border-radius: 8px;
    border: 1px solid #e9ecef;
}

.section-title {
    color: #003366;
    border-bottom: 2px solid #003366;
    padding-bottom: 10px;
    margin-bottom: 20px;
}

.passenger-card, .segment-card {
    background: white;
    padding: 15px;
    margin-bottom: 15px;
    border-radius: 6px;
    border: 1px solid #dee2e6;
}

.passenger-card h6, .segment-card h6 {
    color: #003366;
    margin-bottom: 15px;
    border-bottom: 1px solid #eee;
    padding-bottom: 8px;
}

.sticky-top {
    top: 80px;
    z-index: 100;
}

.custom-switch {
    margin-bottom: 10px;
}

.custom-control-input:checked ~ .custom-control-label::before {
    background-color: #003366;
    border-color: #003366;
}

.action-buttons .btn-primary {
    background: #003366;
    border-color: #003366;
}

.action-buttons .btn-primary:hover {
    background: #002244;
    border-color: #002244;
}
</style>

<script>
function toggleOptionalField(type) {
    const checkbox = document.querySelector(`input[name="optional_fields[${type}]"]`);
    const fieldContainer = document.getElementById(type + 'Fields');
    
    if (checkbox.checked) {
        fieldContainer.style.display = 'block';
    } else {
        fieldContainer.style.display = 'none';
        // Clear the fields when unchecked
        if (type === 'passport') {
            document.querySelectorAll('input[name^="passport_numbers"]').forEach(input => input.value = '');
        } else if (type === 'baggage') {
            document.querySelector('textarea[name="baggage_info"]').value = '';
        } else if (type === 'pet') {
            document.querySelector('textarea[name="pet_info"]').value = '';
        }
    }
}

function resetFields() {
    if (confirm('Are you sure you want to reset all fields to their original values?')) {
        location.reload();
    }
}

// Show cabin class for each passenger
document.addEventListener('DOMContentLoaded', function() {
    const cabinClass = document.querySelector('input[name="cabin_class"]').value || 'N/A';
    document.querySelectorAll('.passenger-card').forEach(card => {
        const cabinField = card.querySelector('input[disabled]');
        if (cabinField) {
            cabinField.value = cabinClass;
        }
    });
});
</script>
@endsection