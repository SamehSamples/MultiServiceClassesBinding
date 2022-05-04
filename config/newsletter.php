<?php

return [
    'default' => env('SELECTED_SERVICE_PROVIDER', 'mailchimp'),

    'mailchimp' => [
        'key' => env('MAILCHIMP_KEY'),
        'server' => env('MAILCHIMP_SERVER'),
        'lists' => [
            'subscribers' => env('MAILCHIMP_SUBSCRIBERS_LIST_ID')
        ],
    ],

    'convert_kit' => [
        'key' => env('CONVERT_KIT_KEY'),
        'api_secret' => env('CONVERT_KIT_SECRET'),
        'lists' => [
            'subscribers' => env('CONVERT_KIT_SUBSCRIBERS_LIST_ID')
        ],
    ],

    'hubspot' => [
        'key' => env('HubSpot_KEY'),
    ],
];
