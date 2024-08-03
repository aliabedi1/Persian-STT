<?php

namespace App\Exceptions\Api;


use App\Enums\SystemMessage;
use Exception;
use Throwable;

class ApiException extends Exception
{
    public function __construct(string $message = "", int $code = SystemMessage::FAIL->value, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
