<?php

namespace App\Exceptions;


class FileException extends CustomException
{
    protected $statusCode = 403;
    public function __construct($message)
    {
        parent::__construct($message, $this->statusCode);
    }
}
