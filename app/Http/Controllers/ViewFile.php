<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use Streams\Core\Support\Facades\Streams;

class ViewFile extends Controller
{
    public function __invoke($id)
    {
        if (!$file = Streams::repository('files')->find($id)) {
            abort(404);
        }

        return Response::make(Storage::disk($file->disk)->get("$file->path"), 200, [
            'Content-Type' => $file->mime_type,
        ]);
    }
}
