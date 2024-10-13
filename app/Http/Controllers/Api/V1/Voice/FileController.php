<?php

namespace App\Http\Controllers\Api\V1\Voice;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Profile\UpdateProfileRequest;
use App\Http\Resources\Profile\UserResource;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;

class FileController extends Controller
{
    public function get($fileName)
    {
        $filePath = Storage::disk('public')->path($fileName);

        if (!file_exists($filePath)) {
            abort(404, 'File not found');
        }

        $fileContent = file_get_contents($filePath);
        $mimeType = mime_content_type($filePath);

        return response($fileContent, 200)
            ->header('Content-Type', $mimeType);
    }

}
