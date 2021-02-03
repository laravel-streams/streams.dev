<?php

namespace App\Http\Controllers;

use Streams\Core\Support\Facades\Streams;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    public function index($stream)
    {
        return Streams::make($stream)
            ->table([
                'options.cp_enabled' => true,
            ])
            ->response();
    }
}
