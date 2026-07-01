<?php

namespace App\Exceptions;

use Exception;

class SicoobException extends Exception
{
    public function __construct(string $message, protected int $httpStatus, protected string|array|null $response = null) {
        parent::__construct($message);
    }

    public function getHttpStatus(): int{
        return $this->httpStatus;
    }

    public function getResponse(): string|array|null{
        return $this->response;
    }
}
