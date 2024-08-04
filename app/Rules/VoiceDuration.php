<?php

namespace App\Rules;

use Closure;
use FFMpeg\FFMpeg;
use Illuminate\Contracts\Validation\ValidationRule;

class VoiceDuration implements ValidationRule
{
    protected int $maxDuration;


    public function __construct(int $maxDuration = 120)
    {
        $this->maxDuration = $maxDuration;
    }


    /**
     * Run the validation rule.
     *
     * @param \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $filePath = $value->getRealPath();

        $ffmpeg = FFMpeg::create();
        $audio = $ffmpeg->open($filePath);

        $duration = $audio->getFormat()->get('duration');

        if ($duration > $this->maxDuration) {
            $fail(
                __('Uploaded voice duration must be less than or equal to :maxDuration seconds.', [$this->maxDuration])
            );
        }
    }


}
