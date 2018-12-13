<?php
return [
    'api_key' => env('VIBER_API_KEY'),
    'webhook_url' => env('APP_URL') . '/api/webhook',

    'bot' => [
        'name' => 'Chat bot'
    ],

    'keyboard' => [
        'bg_color' => '#f6f7f9'
    ],
    "color" => [
        "gray" => "#A0A0A0",
        "green" => "#00AA00"
    ]
];
