<?php

namespace App\Http\Requests\Api\V1\Voice;

use App\Exceptions\File\FileException;
use App\Services\FileService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DeleteVoiceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }


    /**
     * Get the validation rules that apply to the request.
     *
     * @throws FileException
     */
    public function rules()
    {
        return [
            'voices' => [
                'nullable',
                'array',
            ],
            'voices.*' => [
                'nullable',
                'int',
                Rule::exists('voice_files', 'id')
                    ->withoutTrashed()
            ]
        ];
    }


}
