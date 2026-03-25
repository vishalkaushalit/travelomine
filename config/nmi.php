<?php

return [
    // Private merchant API key (Permission: api)
    'security_key' => env('NMI_SECURITY_KEY'),

    // Direct Post endpoint
    'api_url'      => env('NMI_API_URL', 'https://secure.nmi.com/api/transact.php'),
];