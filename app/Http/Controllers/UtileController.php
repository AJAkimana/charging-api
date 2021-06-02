<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\File;

class UtileController extends Controller
{
    public function displayFile(Request $request)
    {
        $path = storage_public($request->route('dir') . '/' . $request->route('filename'));

        if (!File::exists($path)) {
            abort(404);
        }

        $file = File::get($path);

        $type = File::mimeType($path);

        $response = Response::make($file, 200);

        $response->header("Content-Type", $type);

        return $response;
    }
}
