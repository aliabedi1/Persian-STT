<?php

namespace App\Http\Requests\Api\V1\File;

use App\Exceptions\File\FileException;
use App\Services\FileService;
use Illuminate\Foundation\Http\FormRequest;

class UploadFileRequest extends FormRequest
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
        return  (new FileService())->validationRules();
    }



}
