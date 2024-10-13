<?php

namespace App\Jobs;

use App\Enums\VoiceStatus;
use App\Exceptions\Api\ApiException;
use App\Models\Voice;
use App\Services\FileService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class TextGetterJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    protected array $uploadedFile;
    protected Voice $voice;


    /**
     * Create a new job instance.
     */
    public function __construct(array $uploadedFile,Voice $voice)
    {
        $this->uploadedFile = $uploadedFile;
        $this->voice = $voice;
    }


    /**
     * Execute the job.
     * @throws \App\Exceptions\File\FileException
     * @throws \Exception
     */
    public function handle(): void
    {
        try {
            $text = getTextFromSpeechAvalAi($this->uploadedFile['name']);
            $this->voice
                ->update([
                    'text' => $text,
                    'status' => VoiceStatus::SUCCESS,
                ]);

        } catch (ApiException $exception) {
            (new FileService())->deleteFile($this->uploadedFile['id']);
        }

    }
}
