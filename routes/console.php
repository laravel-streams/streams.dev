<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Streams\Core\Support\Facades\Streams;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command(
    'streams:schema
    {streams? : The stream(s) to generate json schema for.}
    {--path= : The path to write schema files to.}',
    function () {

        $directory = base_path($relative = $this->option('path') ?: 'streams/schema');

        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $streams = Streams::collection();

        if ($target = $this->argument('streams')) {

            $target = explode('|', $target);
            
            $streams = $streams->filter(fn ($stream) => in_array($stream->id, $target));
        }

        foreach ($streams as $stream) {

            $file = $stream->id . '.schema.json';
            $path = $directory . '/' . $file;

            $schema = $stream->schema();

            $json = array_merge(
                $schema->tag()->toArray(),
                $schema->object()->toArray(),
            );

            file_put_contents($path, json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

            $this->info('Wrote: ' . $relative  . '/' . $file);
        }
    }
);
