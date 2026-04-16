{{-- Include this partial inside resources/views/agent/bookings/create.blade.php, preferably near the end of the form. --}}

<input type="hidden" name="source_booking_id" id="source_booking_id" value="{{ old('source_booking_id') }}">
<input type="hidden" id="bookingFlowMode" value="{{ old('source_booking_id') ? 'update' : 'new' }}">

<div class="modal fade" id="pnrLookupModal" tabindex="-1" aria-labelledby="pnrLookupModalLabel" aria-hidden="true"
    data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="pnrLookupModalLabel">Find Existing Booking</h5>
                <button type="button" class="close pnr-modal-close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info mb-3">
                    Select any non-New Booking service type, then search using GK PNR or Airline PNR.
                </div>

                <div id="pnrLookupMessage"></div>

                <div class="form-group">
                    <label for="lookup_service_type">Selected Service Type</label>
                    <input type="text" id="lookup_service_type" class="form-control" readonly>
                </div>

                <div class="form-group">
                    <label for="lookup_pnr">Enter GK PNR / Airline PNR <span class="text-danger">*</span></label>
                    <input type="text" id="lookup_pnr" class="form-control" placeholder="Example: ABC123 / GK9876">
                    <small class="form-text text-muted">Only your own existing booking should be matched.</small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary pnr-modal-close"
                    data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="lookupPnrBtn">Search Booking</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const SERVICE_TYPE_NEW = 'New Booking';
    const form = document.getElementById('bookingForm');
    const serviceTypeSelect = document.querySelector('select[name="service_type"]');
    const serviceProvidedSelect = document.querySelector('select[name="service_provided"]');
    const flightTypeSelect = document.getElementById('flight_type');
    const sourceBookingIdInput = document.getElementById('source_booking_id');
    const flowModeInput = document.getElementById('bookingFlowMode');
    const modalElement = document.getElementById('pnrLookupModal');
    const lookupServiceType = document.getElementById('lookup_service_type');
    const lookupPnr = document.getElementById('lookup_pnr');
    const lookupBtn = document.getElementById('lookupPnrBtn');
    const lookupMessage = document.getElementById('pnrLookupMessage');
    
    // Make sure routes are defined - adjust these URLs if needed
    const formActionForUpdate = '/agent/booking-updates';
    const formActionForNew = '/agent/bookings';
    const searchUrl = '/agent/booking-updates/search';
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || document.querySelector('input[name="_token"]')?.value;
    
    let previousServiceType = serviceTypeSelect ? serviceTypeSelect.value : '';
    let modalInstance = null;
    let lookupLocked = false;

    if (!form || !serviceTypeSelect || !modalElement) {
        console.error('Required elements not found');
        return;
    }

    if (window.bootstrap) {
        modalInstance = new bootstrap.Modal(modalElement);
    }

    function showMessage(type, text) {
        lookupMessage.innerHTML = '<div class="alert alert-' + type + ' mb-3">' + text + '</div>';
    }

    function clearMessage() {
        lookupMessage.innerHTML = '';
    }

    function setFormActionByMode(mode) {
        form.action = mode === 'update' ? formActionForUpdate : formActionForNew;
        if (flowModeInput) flowModeInput.value = mode;
    }

    function resetPaymentSection() {
        // Reset payment fields
        const paymentFields = [
            'currency',
            'amount_charged',
            'amount_paid_airline',
            'total_mco',
            'full_payment_charge_amount'
        ];

        paymentFields.forEach(function (id) {
            const el = document.getElementById(id);
            if (el) el.value = '';
        });

        // Reset payment blocks
        const fullPaymentBlock = document.getElementById('full_payment_block');
        const splitPaymentBlock = document.getElementById('split_payment_block');
        
        if (fullPaymentBlock) {
            fullPaymentBlock.querySelectorAll('input, select').forEach(function(input) {
                if (input.type !== 'radio' && input.type !== 'checkbox') {
                    input.value = '';
                }
            });
        }
        
        if (splitPaymentBlock) {
            splitPaymentBlock.querySelectorAll('input, select').forEach(function(input) {
                if (input.type !== 'radio' && input.type !== 'checkbox') {
                    input.value = '';
                }
            });
        }

        // Reset to full payment
        const fullRadio = document.getElementById('payment_type_full');
        if (fullRadio) {
            fullRadio.checked = true;
            if (typeof togglePaymentBlocks === 'function') togglePaymentBlocks();
        }
    }

    function setInputValue(selector, value) {
        const el = document.querySelector(selector);
        if (!el) return;

        if (el.type === 'checkbox') {
            el.checked = !!value;
        } else {
            el.value = value ?? '';
            el.dispatchEvent(new Event('input', { bubbles: true }));
            el.dispatchEvent(new Event('change', { bubbles: true }));
        }
    }

    function ensurePassengerRows(data) {
        // Set passenger counts
        const adultsCount = document.getElementById('adults_count');
        const childrenCount = document.getElementById('children_count');
        const infantsCount = document.getElementById('infants_count');
        const infantInLapCount = document.getElementById('infant_in_lap_count');
        
        if (adultsCount) adultsCount.value = data.adults ?? 1;
        if (childrenCount) childrenCount.value = data.children ?? 0;
        if (infantsCount) infantsCount.value = data.infants ?? 0;
        if (infantInLapCount) infantInLapCount.value = data.infant_in_lap ?? 0;

        // Trigger passenger form update
        if (typeof updatePassengerForms === 'function') {
            updatePassengerForms();
        } else {
            // Manually trigger the event
            [adultsCount, childrenCount, infantsCount, infantInLapCount].forEach(input => {
                if (input) input.dispatchEvent(new Event('input', { bubbles: true }));
            });
        }

        // Fill passenger details
        const passengers = data.passengers || [];
        passengers.forEach(function (passenger, index) {
            setInputValue('[name="passengers[' + index + '][passenger_type]"]', passenger.passenger_type);
            setInputValue('[name="passengers[' + index + '][title]"]', passenger.title);
            setInputValue('[name="passengers[' + index + '][first_name]"]', passenger.first_name);
            setInputValue('[name="passengers[' + index + '][middle_name]"]', passenger.middle_name);
            setInputValue('[name="passengers[' + index + '][last_name]"]', passenger.last_name);
            setInputValue('[name="passengers[' + index + '][gender]"]', passenger.gender);
            setInputValue('[name="passengers[' + index + '][dob]"]', passenger.dob);
            setInputValue('[name="passengers[' + index + '][passport_number]"]', passenger.passport_number);
            setInputValue('[name="passengers[' + index + '][passport_expiry]"]', passenger.passport_expiry);
            setInputValue('[name="passengers[' + index + '][nationality]"]', passenger.nationality);
            setInputValue('[name="passengers[' + index + '][seat_preference]"]', passenger.seat_preference);
            setInputValue('[name="passengers[' + index + '][meal_preference]"]', passenger.meal_preference);
            setInputValue('[name="passengers[' + index + '][special_assistance]"]', passenger.special_assistance);
        });
    }

    function ensureSegments(data) {
        const flightType = document.getElementById('flight_type');
        if (flightType && data.flight_type) {
            flightType.value = data.flight_type;
            if (typeof buildSegments === 'function') {
                buildSegments();
            } else {
                flightType.dispatchEvent(new Event('change', { bubbles: true }));
            }
        }

        const segments = data.segments || [];
        const container = document.getElementById('segments_container');
        
        // Wait for segments to be built
        setTimeout(function() {
            segments.forEach(function (segment, index) {
                setInputValue('[name="segments[' + index + '][from_city]"]', segment.from_city);
                setInputValue('[name="segments[' + index + '][to_city]"]', segment.to_city);
                setInputValue('[name="segments[' + index + '][departure_date]"]', segment.departure_date);
                setInputValue('[name="segments[' + index + '][return_date]"]', segment.return_date);
                setInputValue('[name="segments[' + index + '][airline_name]"]', segment.airline_name);
                setInputValue('[name="segments[' + index + '][flight_number]"]', segment.flight_number);
                setInputValue('[name="segments[' + index + '][segment_pnr]"]', segment.segment_pnr);
                setInputValue('[name="segments[' + index + '][cabin_class]"]', segment.cabin_class);
            });
        }, 100);
    }

    function fillBookingForm(data, selectedServiceType) {
        if (sourceBookingIdInput) sourceBookingIdInput.value = data.source_booking_id || '';
        setFormActionByMode('update');

        // Basic info
        setInputValue('[name="booking_date"]', data.booking_date);
        setInputValue('[name="call_type"]', data.call_type);
        setInputValue('[name="service_provided"]', data.service_provided);
        setInputValue('[name="service_type"]', selectedServiceType || data.service_type);
        setInputValue('[name="booking_portal"]', data.booking_portal);
        setInputValue('[name="language"]', data.language);
        setInputValue('[name="email_auth_taken"]', data.email_auth_taken);
        
        // Customer info
        setInputValue('[name="customer_name"]', data.customer_name);
        setInputValue('[name="customer_email"]', data.customer_email);
        setInputValue('[name="customer_phone"]', data.customer_phone);
        setInputValue('[name="billing_phone"]', data.billing_phone);
        setInputValue('[name="billing_address"]', data.billing_address);
        
        // PNR info
        setInputValue('[name="gk_pnr"]', data.gk_pnr);
        setInputValue('[name="airline_pnr"]', data.airline_pnr);
        
        // Options
        setInputValue('[name="hotel_required"]', data.hotel_required);
        setInputValue('[name="cab_required"]', data.cab_required);
        setInputValue('[name="insurance_required"]', data.insurance_required);

        // Trigger service provided change
        if (serviceProvidedSelect) {
            serviceProvidedSelect.dispatchEvent(new Event('change', { bubbles: true }));
        }

        // Fill segments and passengers
        ensureSegments(data);
        ensurePassengerRows(data);
        
        // Reset payment and remarks
        resetPaymentSection();
        setInputValue('[name="agent_remarks"]', '');
        
        // Clear any existing error messages
        const errorAlert = document.querySelector('.alert-danger');
        if (errorAlert) errorAlert.remove();
    }

    async function searchBookingByPnr() {
        const selectedServiceType = serviceTypeSelect.value;
        const pnr = (lookupPnr.value || '').trim();

        clearMessage();

        if (!pnr) {
            showMessage('danger', 'Please enter a PNR first.');
            return;
        }

        if (!searchUrl) {
            showMessage('danger', 'Search URL not configured.');
            return;
        }

        lookupBtn.disabled = true;
        lookupBtn.innerText = 'Searching...';

        try {
            const response = await fetch(searchUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({
                    pnr: pnr,
                    service_type: selectedServiceType
                })
            });

            const result = await response.json();

            if (!response.ok || !result.success) {
                throw new Error(result.message || 'Booking not found.');
            }

            // Booking found - prefill the form
            fillBookingForm(result.data.booking, selectedServiceType);
            showMessage('success', 'Booking found! Form has been prefilled. Please review and update payment details.');
            lookupLocked = true;
            previousServiceType = selectedServiceType;

            setTimeout(function () {
                if (modalInstance) modalInstance.hide();
            }, 1500);
            
        } catch (error) {
            // BOOKING NOT FOUND - Allow manual entry
            console.log('Booking not found:', error.message);
            
            // Clear any source booking ID
            if (sourceBookingIdInput) sourceBookingIdInput.value = '';
            
            // Set form action to NEW booking (not update)
            setFormActionByMode('new');
            
            // Keep the selected service type (don't revert)
            previousServiceType = selectedServiceType;
            lookupLocked = false;
            
            // Show message that booking wasn't found and manual entry is required
            showMessage('warning', 'No existing booking found for PNR: ' + pnr + '. You can manually enter all details for this new booking. The selected service type "' + selectedServiceType + '" has been kept.');
            
            // Close the modal after 2 seconds to allow manual entry
            setTimeout(function () {
                if (modalInstance) modalInstance.hide();
                
                // Focus on first input field for manual entry
                const firstInput = document.querySelector('#bookingForm input:not([type="hidden"])');
                if (firstInput) {
                    firstInput.focus();
                }
            }, 2000);
        } finally {
            lookupBtn.disabled = false;
            lookupBtn.innerText = 'Search Booking';
        }
    }

    function openLookupModal() {
        if (lookupServiceType) lookupServiceType.value = serviceTypeSelect.value;
        if (lookupPnr) lookupPnr.value = '';
        clearMessage();
        if (modalInstance) {
            modalInstance.show();
        }
    }

    function revertToNewBookingMode() {
        if (sourceBookingIdInput) sourceBookingIdInput.value = '';
        lookupLocked = false;
        setFormActionByMode('new');
    }

    // Initialize form action
    setFormActionByMode(flowModeInput && flowModeInput.value === 'update' ? 'update' : 'new');

    // Watch for service type changes
    serviceTypeSelect.addEventListener('change', function () {
        const selected = this.value;

        if (!selected || selected === SERVICE_TYPE_NEW) {
            previousServiceType = selected;
            revertToNewBookingMode();
            return;
        }

        if (lookupLocked && sourceBookingIdInput && sourceBookingIdInput.value) {
            previousServiceType = selected;
            setFormActionByMode('update');
            return;
        }

        openLookupModal();
    });

    if (lookupBtn) {
        lookupBtn.addEventListener('click', searchBookingByPnr);
    }

    if (lookupPnr) {
        lookupPnr.addEventListener('keypress', function (event) {
            if (event.key === 'Enter') {
                event.preventDefault();
                searchBookingByPnr();
            }
        });
    }

    // Close modal handlers - Don't reset service type if we're in manual entry mode
    document.querySelectorAll('.pnr-modal-close').forEach(function (btn) {
        btn.addEventListener('click', function () {
            // Only reset service type if we didn't just search and find nothing
            if (!lookupLocked && serviceTypeSelect && serviceTypeSelect.value !== SERVICE_TYPE_NEW) {
                // If we're in manual entry mode (lookup failed), keep the selected service type
                const manualEntryMode = sourceBookingIdInput && !sourceBookingIdInput.value;
                if (!manualEntryMode) {
                    serviceTypeSelect.value = previousServiceType === SERVICE_TYPE_NEW ? '' : previousServiceType;
                }
            }
        });
    });
});
</script>
@endpush
