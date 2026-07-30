<div style="margin-top: 30px; padding-top: 20px; border-top: 2px dashed #e2e8f0; font-size: 13px; color: #475569; line-height: 1.6;">
    <h4 style="margin: 0 0 10px 0; color: #0f172a; font-size: 15px; font-weight: 700;">Important Information & Terms:</h4>
    <p style="margin-bottom: 12px; color: #475569;">
        Please review the following information carefully. If you have any questions or concerns, please contact us immediately at <strong>{{ $booking->agencyMerchant->contact_number ?? '+1 888-476-0932' }}</strong> or email us at <strong>{{ $booking->agencyMerchant->support_mail ?? '' }}</strong>.
    </p>

    <h5 style="margin: 14px 0 6px 0; color: #1e293b; font-size: 13px; font-weight: 700;">Please Note:</h5>
    <ul style="margin: 0 0 14px 0; padding-left: 20px; color: #475569;">
        <li style="margin-bottom: 4px;">Review the names, dates, cities, and departure/arrival times carefully.</li>
        <li style="margin-bottom: 4px;">Baggage fees may apply. Please check with the airline for the most up-to-date baggage policies.</li>
        <li style="margin-bottom: 4px;">Flight schedules, times, and numbers are subject to change by the airline. We recommend checking flight status prior to departure.</li>
    </ul>

    <h5 style="margin: 14px 0 6px 0; color: #1e293b; font-size: 13px; font-weight: 700;">Cancellations & Refunds:</h5>
    <p style="margin-bottom: 12px; color: #475569;">
        All service and convenience fees are non-refundable. Airline tickets are non-refundable in most cases; depending on fare rules, you may be eligible for credit or partial refund. Bookings must be canceled over the phone at least 24 hours prior to scheduled departure.
    </p>

    <h5 style="margin: 14px 0 6px 0; color: #1e293b; font-size: 13px; font-weight: 700;">Disclaimer:</h5>
    <p style="margin-bottom: 16px; color: #475569;">
        {{ $booking->agency_merchant_name ?? 'Our Agency' }} is an independent travel agency. We are not an airline or direct ally of any airline brand. Charges may appear under {{ $booking->agency_merchant_name ?? 'Our Agency' }} or the airline on your payment statement.
    </p>

    <div style="margin-top: 18px; padding: 14px 18px; background: #f8fafc; border-radius: 8px; border: 1px solid #e2e8f0; color: #334155; font-size: 13px; line-height: 1.5;">
        <strong style="color: #0f172a;">Best Regards,</strong><br>
        Reservation Desk<br>
        <span style="color: #64748b;">{{ $booking->user->alias_name ?? 'Travel Desk' }}</span><br>
        Phone: <strong>{{ $booking->agencyMerchant->contact_number ?? '+1 888-476-0932' }}</strong>
        @if (!empty($booking->user->extension_number))
            || Ext: <strong>{{ $booking->user->extension_number }}</strong>
        @endif
    </div>
</div>
