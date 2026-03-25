nmi_transactions table has relation with bookings table
so we nee to make a better relation 

fields in nmi_trabnsactions table are : 

        'merchant_id', -> $bookings->agency_merchant_id  
        'booking_id', -> airline_pnr if blank gk_pnr
        'payment_link_id', 
        'order_id',
        'transaction_id',
        'type',
        'customer_first_name', $bookings - customer_first_name
        'customer_last_name', $bookings - customer_last_name
        'email', - working fine already
        'card_last4', - nmi
        'card_brand', - nmi
        'address1', - 
        'city',
        'state',
        'zip',
        'country',
        'amount',
        'currency',
        'status',
        'processed_at',
        'raw_response',