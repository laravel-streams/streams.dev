<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Moved docs pages
    |--------------------------------------------------------------------------
    |
    | Old docs URL (no leading slash) => new URL. App\Http\Middleware\RedirectMovedDocs
    | answers these with a 301 before routing, so they win over the docs stream
    | routes (docs/{id}, docs/sdk/{id}). The raw markdown variant ({old}.md) is
    | redirected to the new page's .md URL automatically.
    |
    */

    'redirects' => [
        // The SDK hub page duplicated the SDK introduction.
        'docs/sdk' => '/docs/sdk/introduction',
        // Taught field types Core does not have; Core's field reference replaces it.
        'docs/sdk/fields' => '/docs/core/fields',
        // Documented streams:admin, which was removed from streams/sdk.
        'docs/sdk/admin-panels' => '/docs/sdk/commands#streamslivewire',
    ],

];
