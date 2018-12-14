<?php
return [
    'api_key' => env('VIBER_API_KEY'),
    'webhook_url' => env('APP_URL') . '/api/webhook',

    'bot' => [
        'name' => 'Chat bot'
    ],

    'keyboard' => [
        'bg_color' => '#f6f7f9',
        'button_color' => '#fdfdfd'
    ],
    "color" => [
        "gray" => "#D7D7D7",
        "red" => "#B00000",
        "black" => "#1C1C1C",
        "white" => "#FFFFFF"
    ],
    "datetime_format" => "H-i d-m-Y"
];
