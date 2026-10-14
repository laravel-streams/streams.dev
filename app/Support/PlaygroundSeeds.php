<?php

namespace App\Support;

/** Read-only fixtures, deliberately outside registered file-backed streams. */
class PlaygroundSeeds
{
    public static function all(): array
    {
        $streams = [];

        foreach (['people', 'posts', 'orders'] as $handle) {
            $streams[$handle] = json_decode(
                file_get_contents(resource_path('playground/'.$handle.'.json')),
                true,
                512,
                JSON_THROW_ON_ERROR
            );
        }

        return $streams;
    }
}
