<?php

namespace App\Http\Requests\Api\V1\Authentication;

use Illuminate\Foundation\Http\FormRequest;

class VerifyRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'mobile' => [
                'required',
                'string',
                'ir_mobile:zero'
            ],
            'hash_code' => [
                'required',
                'string',
                'size:8'
            ],
            'code' => [
                'required',
                'numeric',
                'digits:6'
            ],
        ];
    }


}
