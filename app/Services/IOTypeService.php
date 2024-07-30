<?php

namespace App\Services;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;

class IOTypeService
{
    /**
     * @var string
     */
    private string $file;


    const DEFAULT_HEADERS = [
        'Accept' => 'application/json',
        'X-Requested-With' => 'XMLHttpRequest',
    ];


    public function __construct(string $file)
    {
        $this->file = $file;
    }


    /**
     * @return string
     */
    public static function getApiUrl(): string
    {
        return Config::get('iotype.API_URL');
    }


    /**
     * @return string
     */
    public static function getDefaultType(): string
    {
        return Config::get('iotype.DEFAULT_TYPE');
    }


    /**
     * @return \Illuminate\Http\Client\Response
     * @throws \Illuminate\Http\Client\ConnectionException
     */
    public function sendRequest(): Response
    {
        return Http::withHeaders(self::getHeaders())
            ->attach('file', $this->getFile())
            ->post(self::getApiUrl(), [
                'type' => self::getDefaultType(),
            ]);
    }


    /**
     * @return string
     */
    protected function getFile(): string
    {
        return $this->file;
    }


    /**
     * @return array
     */
    public static function getHeaders(): array
    {
        return array_merge(self::DEFAULT_HEADERS, [
            'Authorization' => Config::get('iotype.TOKEN')
        ]);
    }


}