<?php

namespace App\Exceptions;

use Exception;

class AuthorizationException extends CustomException
{
    protected $statusCode = 403;
    public function __construct($message)
    {
        parent::__construct($message, $this->statusCode);
    }
}
