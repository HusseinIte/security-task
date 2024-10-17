<?php

namespace App\Exceptions;


class AuthenticationException extends CustomException
{
    protected $statusCode = 401;
    public function __construct($message)
    {
        parent::__construct($message, $this->statusCode);
    }
}
