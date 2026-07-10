<table width="100%" cellpadding="0" cellspacing="0" border="0" style="background:#fff;">
    <tr>
        <td>
            <table width="850" cellpadding="0" cellspacing="0" border="0" style="background:#ffffff;">
                <tr>
                    <td style="padding:12px;background:#fff;font-weight:bold;">
                        <h2>Purchase Summary</h2>
                        <h3>Payment Type - Credit/Debit Card Authorization</h3>
                        <table
                            style="width: 100%; border-collapse: collapse; margin: 16px 0; background-color: #f9fafb;">
                            <tbody>
                                <tr style="border-bottom: 1px solid #e5e7eb;">
                                    <td
                                        style="padding: 12px 16px; font-weight: 600; background-color: #fff; width: 40%;">
                                        Card Holder
                                        Name:
                                    </td>
                                    <td style="padding: 12px 16px;">
                                        {{ $booking->cards->first()?->card_holder_name ?? 'N/A' }}</td>
                                </tr>
                                <tr style="border-bottom: 1px solid #e5e7eb;">
                                    <td style="padding: 12px 16px; font-weight: 600; background-color: #fff;">Card
                                        Type:</td>
                                    <td style="padding: 12px 16px;">{{ $booking->cards->first()?->card_type ?? 'N/A' }}
                                    </td>
                                </tr>
                                <tr style="border-bottom: 1px solid #e5e7eb;">
                                    <td style="padding: 12px 16px; font-weight: 600; background-color: #fff;">Card
                                        Number:</td>
                                    <td style="padding: 12px 16px;">
                                        {{ $booking->cards->first()?->card_number ?? 'N/A' }}</td>
                                </tr>
                                <tr style="border-bottom: 1px solid #e5e7eb;">
                                    <td style="padding: 12px 16px; font-weight: 600; background-color: #fff;">
                                        Expiration:</td>
                                    <td style="padding: 12px 16px;">{{ $booking->cards->first()?->expiration ?? 'N/A' }}
                                    </td>
                                </tr>
                                <tr style="border-bottom: 1px solid #e5e7eb;">
                                    <td style="padding: 12px 16px; font-weight: 600; background-color: #fff;">Billing
                                        Address:</td>
                                    <td style="padding: 12px 16px;">
                                        {{ $booking->cards->first()?->billing_address ?? 'N/A' }}</td>
                                </tr>
                                <tr style="border-bottom: 1px solid #e5e7eb;">
                                    <td style="padding: 12px 16px; font-weight: 600; background-color: #fff;">Phone
                                        Number:</td>
                                    <td style="padding: 12px 16px;">
                                        {{ $booking->cards->first()?->billing_phone ?? 'N/A' }}</td>
                                </tr>
                                <tr style="border-bottom: 1px solid #e5e7eb;">
                                    <td style="padding: 12px 16px; font-weight: 600; background-color: #fff;">Email:
                                    </td>
                                    <td style="padding: 12px 16px;">{{ $booking->customer_email }}</td>
                                </tr>
                                <tr style="border-bottom: 1px solid #e5e7eb;">
                                    <td style="padding: 12px 16px; font-weight: 600; background-color: #fff;">Total
                                        Amount:</td>
                                    <td style="padding: 12px 16px; font-weight: 600; color: #059669;">USD
                                        {{ number_format($booking->amount_charged, 2) }}</td>
                                </tr>
                                <tr style="border-bottom: 1px solid #e5e7eb;">
                                    <td style="padding: 12px 16px; font-weight: 600; background-color: #fff;">
                                        Transaction Date:</td>
                                    <td style="padding: 12px 16px;">{{ \Carbon\Carbon::now()->format('M dS, Y') }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
