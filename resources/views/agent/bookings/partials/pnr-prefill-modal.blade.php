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
    
    // Check if essential elements exist - if not, exit gracefully
    if (!form) {
        console.warn('Booking form not found - modal functionality disabled');
        return;
    }
    
    if (!serviceTypeSelect) {
        console.warn('Service type select not found - modal functionality disabled');
        return;
    }
    
    if (!modalElement) {
        console.warn('Modal element not found - modal functionality disabled');
        return;
    }
    
    console.log('Modal script initialized successfully');
    
    // Make sure routes are defined
    const formActionForUpdate = '/agent/booking-updates';
    const formActionForNew = '/agent/bookings';
    const searchUrl = '/agent/booking-updates/search';
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || document.querySelector('input[name="_token"]')?.value;
    
    let previousServiceType = serviceTypeSelect.value;
    let modalInstance = null;
    let lookupLocked = false;
    
    // Initialize Bootstrap modal if available
    if (window.bootstrap && modalElement) {
        modalInstance = new bootstrap.Modal(modalElement);
    }
    
    function showMessage(type, text) {
        if (lookupMessage) {
            lookupMessage.innerHTML = '<div class="alert alert-' + type + ' mb-3">' + text + '</div>';
        } else {
            console.log(type + ': ' + text);
        }
    }
    
    function clearMessage() {
        if (lookupMessage) {
            lookupMessage.innerHTML = '';
        }
    }
    
    function setFormActionByMode(mode) {
        if (form) {
            form.action = mode === 'update' ? formActionForUpdate : formActionForNew;
        }
        if (flowModeInput) {
            flowModeInput.value = mode;
        }
        console.log('Form action set to:', mode === 'update' ? formActionForUpdate : formActionForNew);
    }
    
    function resetPaymentSection() {
        // Reset payment fields
        const paymentFieldIds = ['currency', 'amount_charged', 'amount_paid_airline', 'total_mco'];
        
        paymentFieldIds.forEach(function (id) {
            const el = document.getElementById(id);
            if (el) el.value = '';
        });
        
        // Reset full payment charge amount
        const fullPaymentCharge = document.getElementById('full_payment_charge_amount');
        if (fullPaymentCharge) fullPaymentCharge.value = '';
        
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
            // Trigger change event if togglePaymentBlocks exists
            if (typeof window.togglePaymentBlocks === 'function') {
                window.togglePaymentBlocks();
            } else if (typeof togglePaymentBlocks === 'function') {
                togglePaymentBlocks();
            }
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
        const passengerInputs = [adultsCount, childrenCount, infantsCount, infantInLapCount];
        passengerInputs.forEach(input => {
            if (input) input.dispatchEvent(new Event('input', { bubbles: true }));
        });
        
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
            flightType.dispatchEvent(new Event('change', { bubbles: true }));
        }
        
        const segments = data.segments || [];
        
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
        
        // Email auth taken (checkbox)
        const emailAuthCheckbox = document.getElementById('email_auth_taken');
        if (emailAuthCheckbox) emailAuthCheckbox.checked = !!data.email_auth_taken;
        
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
        const hotelRequired = document.querySelector('[name="hotel_required"]');
        const cabRequired = document.querySelector('[name="cab_required"]');
        const insuranceRequired = document.querySelector('[name="insurance_required"]');
        if (hotelRequired) hotelRequired.checked = !!data.hotel_required;
        if (cabRequired) cabRequired.checked = !!data.cab_required;
        if (insuranceRequired) insuranceRequired.checked = !!data.insurance_required;
        
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
        const pnr = (lookupPnr?.value || '').trim();
        
        if (lookupMessage) clearMessage();
        
        if (!pnr) {
            showMessage('danger', 'Please enter a PNR first.');
            return;
        }
        
        if (!searchUrl) {
            showMessage('danger', 'Search URL not configured.');
            return;
        }
        
        if (lookupBtn) {
            lookupBtn.disabled = true;
            lookupBtn.innerText = 'Searching...';
        }
        
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
                const firstInput = document.querySelector('#bookingForm input:not([type="hidden"]):not([type="submit"])');
                if (firstInput) {
                    firstInput.focus();
                }
            }, 2000);
        } finally {
            if (lookupBtn) {
                lookupBtn.disabled = false;
                lookupBtn.innerText = 'Search Booking';
            }
        }
    }
    
    function openLookupModal() {
        if (lookupServiceType && serviceTypeSelect) {
            lookupServiceType.value = serviceTypeSelect.value;
        }
        if (lookupPnr) lookupPnr.value = '';
        if (lookupMessage) clearMessage();
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
    const initialMode = (flowModeInput && flowModeInput.value === 'update') ? 'update' : 'new';
    setFormActionByMode(initialMode);
    
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
    
    // Add event listeners only if elements exist
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
    
    // Close modal handlers
    document.querySelectorAll('.pnr-modal-close').forEach(function (btn) {
        btn.addEventListener('click', function () {
            // Only reset service type if we didn't just search and find nothing
            if (!lookupLocked && serviceTypeSelect && serviceTypeSelect.value !== SERVICE_TYPE_NEW) {
                const manualEntryMode = sourceBookingIdInput && !sourceBookingIdInput.value;
                if (!manualEntryMode) {
                    serviceTypeSelect.value = previousServiceType === SERVICE_TYPE_NEW ? '' : previousServiceType;
                }
            }
        });
    });
    
    console.log('Modal script setup complete');
});
</script>
@endpush