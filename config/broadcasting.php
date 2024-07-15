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
                'cluster' => "mt1",
                'useTLS' => true,
                'encrypted' => true,
                // No configurar host, port y scheme para Pusher en producción
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
