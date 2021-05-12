<?php

return [
    'whitelist' => [
        '::1',
        '127.0.0.1',
        '216.175.28.99',
    ],
    'cp' => [
        'prefix' => env('STREAMS_CP_PREFIX', 'cp'),
    ],
];
