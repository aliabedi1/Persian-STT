<?php

namespace App\Http\Controllers\Api\V1\File;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\File\UploadFileRequest;
use App\Http\Resources\File\FileResource;
use App\Models\Voice;
use App\Services\FileService;
use App\Services\IOTypeService;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;


class VoiceController extends Controller
{

    /**
     * @throws \App\Exceptions\File\EntryFieldsMissMatchTableFillablesException
     * @throws \App\Exceptions\File\FileException
     */
    public function upload(UploadFileRequest $request)
    {
        $uploader = new FileService();
        $disk = $uploader->getTypeFromConfig()['disk'];
        $uploadedFile = $uploader->upload();
        $apiResponse = (new IOTypeService(Storage::disk($disk)->get($uploadedFile['name'])))->sendRequest();
        dd($apiResponse->body());
        if ($apiResponse->successful()){
            dd($result);
            $result = $apiResponse->json()['result'];



        }   else{
            dd($apiResponse->json());
        }



        $uploader->getTypeFromConfig();

        return Response::success(
            message: __('File uploaded successfully.'),
            data: new FileResource($uploadedFile)
        );
    }


}
