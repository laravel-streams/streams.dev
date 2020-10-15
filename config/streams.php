<?php

return [

    'applications' => [
        'french' => [
            'match' => '*streams.*lang=fr*',
            'config' => [
                'app.name' => 'Ruisseaux',
                'app.locale' => 'fr',
            ],
        ],
        'default' => [], // Required and last.
    ]
];
