<?php

namespace App\Exceptions;

use Exception;

class SocException extends Exception
{
    protected string $friendlyMessage;

    public function __construct($logMessage = null, string $friendlyMessage) {
        parent::__construct($logMessage ?? $friendlyMessage);

        $this->friendlyMessage = $friendlyMessage;
    }

    public function friendlyMessage(): string{
        return $this->friendlyMessage;
    }

}
