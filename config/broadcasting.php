<?php

return [

    'default' => 'pusher',

    'connections' => [

        'pusher' => [
            'driver' => 'pusher',
            'key' => 'df03ae27a8fe092e5efa',
            'secret' => '3038071db340c273c723',
            'app_id' => '1834186',
            'options' => [
                'cluster' => 'mt1',
                'useTLS' => false,
                'host' => '127.0.0.1',
                'port' => 6001,
                'scheme' => 'http',
            ],
        ],

        'ably' => [
            'driver' => 'ably',
            'key' => env('ABLY_KEY'),
        ],

        'redis' => [
            'driver' => 'redis',
            'connection' => 'default',
        ],

        'log' => [
            'driver' => 'log',
        ],

        'null' => [
            'driver' => 'null',
        ],

    ],

];
