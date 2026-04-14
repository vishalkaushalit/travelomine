<?php

return [
    'security_key' => env('NMI_SECURITY_KEY'),
    'api_url' => env('NMI_API_URL', 'https://macpayments.transactiongateway.com/api/transact.php'),
];
