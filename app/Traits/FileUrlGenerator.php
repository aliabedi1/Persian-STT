<?php

namespace App\Traits;

use App\Exceptions\File\FileException;
use App\Services\FileService;

trait FileUrlGenerator
{
    /**
     * Get file url
     *
     * @param string $field_name
     * @return string|null
     * @throws FileException
     */
    public function getFileUrl(string $field_name): null|string
    {
        if (!isset($this->$field_name) || $this->$field_name == '') return null;

        $service = new FileService();
        return $service
            ->getUrl($this->$field_name);
    }

    /**
     * Get file details
     *
     * @param string $field_name
     * @return array|null
     * @throws FileException
     */
    public function getFileDetails(string $field_name): null|array
    {
        if (!isset($this->$field_name) || $this->$field_name == '') return null;

        $service = new FileService();
        return $service
            ->getDetails($this->$field_name);
    }
}
