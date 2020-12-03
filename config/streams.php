<?php

return [
    'whitelist' => [
        '::1',
        '127.0.0.1',
        '193.148.18.54',
    ],
    'cp' => [
        'prefix' => env('STREAMS_CP_PREFIX', 'cp'),
    ],
];
