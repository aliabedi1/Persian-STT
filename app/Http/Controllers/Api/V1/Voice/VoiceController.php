<?php

namespace App\Http\Controllers\Api\V1\Voice;

use App\Constants\Base;
use App\Enums\VoiceStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Voice\DeleteVoiceRequest;
use App\Http\Requests\Api\V1\Voice\UploadVoiceRequest;
use App\Http\Resources\PaginationResource;
use App\Http\Resources\Voice\VoiceResource;
use App\Jobs\TextGetterJob;
use App\Models\Voice;
use App\Services\FileService;
use Illuminate\Support\Facades\Response;


class VoiceController extends Controller
{

    /**
     * @throws \App\Exceptions\File\EntryFieldsMissMatchTableFillablesException
     * @throws \App\Exceptions\File\FileException
     * @throws \Exception
     */
    public function upload(UploadVoiceRequest $request)
    {
        $uploadedFile = (new FileService())->upload();

        $voice = Voice::create([
            'user_id' => auth()->id(),
            'voice_file_id' => $uploadedFile['id'],
            'text' => null,
            'status' => VoiceStatus::FAILURE,
        ]);

        TextGetterJob::dispatch($uploadedFile, $voice);

        return Response::success(
            message: __('File uploaded successfully.'),
            data: [
                'id' => $voice->id,
                'wait_time' => 15,
            ]
        );
    }


    /**
     * @throws \App\Exceptions\File\FileException
     */
    public function delete(DeleteVoiceRequest $request)
    {
        $voiceIDs = $request->input('files');
        $fileService = new FileService();

        $multiple = false;
        if (sizeof($voiceIDs) > 1) {
            $multiple = true;
        }

        if (empty($voiceIDs)) {
            $multiple = true;

            auth('api-user')
                ->user()
                ->voices()
                ->delete();

            $fileService
                ->deleteFile(
                    auth('api-user')
                        ->user()
                        ->voice_files
                        ->pluck('id')
                        ->toArray()
                );
        } else {
            $voiceFileIDs = auth('api-user')
                ->user()
                ->voices()
                ->whereIn('id', $voiceIDs)
                ->get()
                ->pluck('id')
                ->toArray();

            auth('api-user')
                ->user()
                ->voices()
                ->whereIn('id', $voiceIDs)
                ->delete();

            $fileService->deleteFile($voiceFileIDs);
        }

        return Response::destroy(
            message: $multiple ? __('Voices deleted successfully.') : __('Voice deleted successfully.'),
        );
    }


    public function history()
    {
        return Response::success(
            message: __('Sent voice history'),
            data: new PaginationResource(
                VoiceResource::collection(
                    auth()
                        ->user()
                        ->voices()
                        ->with([
                            'voice_file'
                        ])
                        ->latest()
                        ->paginate(Base::PAGINATION_PER_PAGE)
                )
            )
        );
    }

    public function show(Voice $voice)
    {
        if ($voice->status == VoiceStatus::FAILURE) {
            return Response::dataNotFound();
        }


        return Response::success(
            data: new VoiceResource($voice)
        );
    }

}
