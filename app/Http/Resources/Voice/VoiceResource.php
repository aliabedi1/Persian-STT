<?php

namespace App\Http\Resources\Voice;

use App\Http\Resources\Profile\UserResource;
use Illuminate\Http\Resources\Json\JsonResource;

class VoiceResource extends JsonResource
{


    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'text' => $this->text,
            'file' => $this->getFileDetails('voice_file_id'),
            'user' => new UserResource($this->whenLoaded('user')),
        ];
    }
}
