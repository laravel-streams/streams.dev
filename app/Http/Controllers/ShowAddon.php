<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller;
use Streams\Core\Support\Facades\Streams;

class ShowAddon extends Controller
{
    public function __invoke($vendor, $addon)
    {
        $addon = Streams::entries('addons')->where('composer.name', "{$vendor}/{$addon}")->first();

        dd($addon->composer);
    }
}
