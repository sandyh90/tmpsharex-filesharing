<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\File;

class SystemFile extends Controller
{
    //
    public function __construct()
    {
        $this->mainroot = 'app/public/system';
    }

    public function user_image($name)
    {
        $uri_file = storage_path($this->mainroot . DIRECTORY_SEPARATOR . 'profile/' . $name);
        if (!File::exists($uri_file)) {
            return abort(404);
        }
        $type = File::mimeType($uri_file);

        return Response::file($uri_file, ['Content-Type' => $type]);
    }
}
