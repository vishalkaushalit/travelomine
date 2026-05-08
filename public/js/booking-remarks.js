// booking-remarks.js - Handles all remark functionality

$(document).ready(function() {
    console.log('Booking remarks JS loaded');
    
    // Initialize remark type change handler
    initRemarkTypeHandler();
    
    // Initialize form submission handler
    initRemarkFormHandler();
});

function initRemarkTypeHandler() {
    // Use event delegation in case the modal is loaded dynamically
    $(document).on('change', '#remark_type', function() {
        if ($(this).val() === 'modification') {
            $('#modificationFields').slideDown();
        } else {
            $('#modificationFields').slideUp();
        }
    });
}

function initRemarkFormHandler() {
    // Handle form submission
    $(document).on('submit', '#addRemarkForm', function(e) {
        e.preventDefault();
        
        const bookingId = $('#remark_booking_id').val();
        if (!bookingId) {
            showNotification('error', 'Booking ID not found');
            return;
        }
        
        // Validate remark text
        const remarkText = $('#remark_text').val().trim();
        if (!remarkText) {
            showNotification('error', 'Please enter remark text');
            return;
        }
        
        const formData = {
            remark_text: remarkText,
            remark_type: $('#remark_type').val(),
            amount_changed: $('#amount_changed').val() || null,
            _token: $('meta[name="csrf-token"]').attr('content')
        };
        
        // Add modification details if type is modification
        if (formData.remark_type === 'modification') {
            formData.old_value = $('#old_value').val();
            formData.new_value = $('#new_value').val();
        }
        
        const submitBtn = $('#submitRemarkBtn');
        const originalText = submitBtn.html();
        submitBtn.prop('disabled', true);
        submitBtn.html('<span class="spinner-border spinner-border-sm"></span> Adding...');
        
        $.ajax({
            url: '/agent/bookings/' + bookingId + '/add-remark',
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: JSON.stringify(formData),
            contentType: 'application/json',
            success: function(response) {
                if (response.success) {
                    // Close modal
                    $('#addRemarkModal').modal('hide');
                    
                    // Reset form
                    $('#addRemarkForm')[0].reset();
                    $('#modificationFields').hide();
                    
                    // Show success message
                    showNotification('success', response.message || 'Remark added successfully!');
                    
                    // Reload remarks timeline
                    loadRemarks(bookingId);
                } else {
                    showNotification('error', response.message || 'Failed to add remark');
                }
            },
            error: function(xhr) {
                let errorMsg = 'An error occurred while adding remark';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                    errorMsg = Object.values(xhr.responseJSON.errors).flat().join(', ');
                }
                showNotification('error', errorMsg);
                console.error('Error:', xhr.responseJSON);
            },
            complete: function() {
                submitBtn.prop('disabled', false);
                submitBtn.html(originalText);
            }
        });
    });
}

// Function to load remarks dynamically
function loadRemarks(bookingId) {
    if (!bookingId) return;
    
    $.ajax({
        url: '/agent/bookings/' + bookingId + '/remarks',
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        },
        success: function(response) {
            if (response.success && response.remarks) {
                // Update remarks count badge
                const remarksCount = $('#remarksCount');
                if (remarksCount.length) {
                    remarksCount.text(response.remarks.length);
                }
                
                // Reload the timeline container if it exists
                const container = $('#remarksTimelineContainer');
                if (container.length && response.html) {
                    container.html(response.html);
                } else if (container.length) {
                    // Fallback: reload the page
                    location.reload();
                }
            }
        },
        error: function(error) {
            console.error('Error loading remarks:', error);
        }
    });
}

// Notification helper (Bootstrap 4 compatible)
function showNotification(type, message) {
    // Check if SweetAlert is available
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            icon: type,
            title: type === 'success' ? 'Success!' : 'Error!',
            text: message,
            timer: 2000,
            showConfirmButton: false
        });
    } else {
        // Fallback to Bootstrap alert
        const alertHtml = `
            <div class="alert alert-${type === 'success' ? 'success' : 'danger'} alert-dismissible fade show position-fixed" 
                 style="top: 20px; right: 20px; z-index: 9999; min-width: 300px;" role="alert">
                ${message}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        `;
        $('body').append(alertHtml);
        setTimeout(function() {
            $('.alert').fadeOut('slow', function() {
                $(this).remove();
            });
        }, 3000);
    }
}