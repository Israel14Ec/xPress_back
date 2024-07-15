<?php

return [

    'default' => env('BROADCAST_DRIVER', 'pusher'),

    'connections' => [

        'pusher' => [
            'driver' => 'pusher',
            'key' => "df03ae27a8fe092e5efa",
            'secret' => "3038071db340c273c723",
            'app_id' => "1834186",
            'options' => [
                'cluster' => 'mt1',
                'useTLS' => false, // O false si no estás usando SSL
                'host' => env('PUSHER_HOST', '127.0.0.1'),
                'port' => env('PUSHER_PORT', 6001),
                'scheme' => 'http',
            ],
        ],
    ],
];
