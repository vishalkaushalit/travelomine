<h6>Please Note:</h6>
    <p>
        Review the names, dates, cities, and departure/arrival times carefully.<br>
        Baggage fees may apply. Please check with the airline for the most up-to-date baggage policies.
    </p>
    <h4>Important:</h4>
    <p>
        Your e-tickets cancellation confirmation will be sent to you via email within 24 hours. Please note that refunds
        are
        not guaranteed until the airline processes the cancellation. If there are any restrictions, updates, or concerns
        from the airline, we will contact you via email or phone. If you wish to make any changes to this cancellation
        request, you must contact us immediately at +1 888-476-0932.
    </p>
    <h4>Note:</h4>
    <p>
        As agreed, your refund will be processed back to the original form of payment. All service fees and convenience
        fees
        are non-refundable. Airline tickets are non-refundable in most cases; however, depending on the airline's
        cancellation policy, you may be eligible for a partial or full refund.
    </p>
    <h4>Disclaimer:</h4>
    <p>
        {{ $booking->agency_merchant_name }} is an independent travel Agency with no third-party association. We shall
        not
        be associated or
        considered as an airline or an ally of any of the airlines or brands. {{ $booking->agency_merchant_name }} is
        shown
        on your bank account
        details in most cases. However, sometimes we have to split the payment with the airline.
        {{ $booking->agency_merchant_name }} and the airline
        or another company of that organization both will appear as recipients on your account. All the service fee and
        convenience fee are non-refundable.
    </p>
    <h4>For Assistance:</h4>
    <p>
        In case of any discrepancies or if an amendment is required, please contact us within 24 hours at
        {{ $booking->agencyMerchant->contact_number ?? '+1 888-476-0932' }}
        or email us at {{ $booking->agencyMerchant->support_mail ?? '' }}.
        We will be happy to assist you.
    </p>
    <h4>For Cancellations and Refunds:</h4>
    <p>
        Call us at {{ $booking->agencyMerchant->contact_number ?? '+1 888-476-0932' }}. Bookings must be
        canceled at least 24 hours before the scheduled
        departure time. Cancellations can only be processed over the phone. Please note that some reservations are
        non-refundable and non-changeable. Refunds depend on the fare rules, cancellation penalties, and supplier fees.
    </p>
    <p>
        Refunds processed after 24 hours of cancellation request may take up to two billing cycles to appear on your
        statement. Refunds are always issued to the original form of payment and usually appear within one or two
        billing
        statements, depending on your bank and credit card company.
    </p>
    <p>
        Still have questions? Call us at
        {{ $booking->agencyMerchant->contact_number ?? '+1 888-476-0932' }}. Our agents are available 24
        hours a day, 7 days a
        week to assist you. You can also email us at {{ $booking->agencyMerchant->support_mail ?? '' }}.
    </p>
    <p>
        We value your business and look forward to serving your travel needs soon.
    </p>
    <p>
        Best Regards<br>
        Reservation Desk<br>
        {{ $booking->user->alias_name ?? '' }}<br>
        {{ $booking->agencyMerchant->contact_number ?? '+1 888-476-0932' }} ||
        {{ $booking->user->extension_number ?? '' }}<br>
    </p>
