<?php

return [

    /*
    |--------------------------------------------------------------------------
    | System Configuration
    |--------------------------------------------------------------------------
    |
    | Configure core behavior.
    |
    */
    
    'system' => [

        /**
         * Force the application
         * to run over HTTPS.
         */
        'force_ssl' => false,
    ],

    /*
    |--------------------------------------------------------------------------
    | Applications Configuration
    |--------------------------------------------------------------------------
    |
    | Configure applications like: "slug" => array $config
    |
    | The system will run as one "default" application if not configured.
    |
    */
    
    'applications' => [
        // 'default' => [
        //     'name' => config('app.name'),
        //     'streams_path' => 'streams',
        //     'locale' => 'en_US',
        //     'url' => '/',
        // ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Source Configuration
    |--------------------------------------------------------------------------
    |
    | Configure Stream sources.
    |
    */
    
    'sources' => [

        /**
         * Customize Filebase
         */
        'filebase' => [

            'path' => 'streams/data',
            
            'formats' => [
                'json' => \Filebase\Format\Json::class,
                'yaml' => \Filebase\Format\Yaml::class,
                'md' => \Anomaly\Streams\Platform\Criteria\Format\Markdown::class,
            ],
        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | Control Panel Customization
    |--------------------------------------------------------------------------
    |
    | Support for control panel configuration is
    | currently limited to the Streams module.
    |
    */

    'cp' => [

        /**
         * This is the URI  prefix
         * for the control panel.
         */
        'prefix' => env('STREAMS_CP_PREFIX', 'admin'),

        /**
         * Define additional CP middleware.
         */
        'middleware' => [
            //'auth',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Addon Customization
    |--------------------------------------------------------------------------
    |
    | Here you can customize and
    | extend the addon loader.
    |
    */

    'addons' => [

        /**
         * An array of disabled
         * addons by handle.
         */
        'disabled' => [
            //'anomaly.module.users',
        ],
    ],

];
