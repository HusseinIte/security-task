<?php

namespace App\Exceptions;


class NotFoundException extends CustomException
{
    protected $statusCode = 404;
    public function __construct($message)
    {
        parent::__construct($message, $this->statusCode);
    }
}
