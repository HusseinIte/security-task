<?php

namespace App\Exceptions;

class ValidationException extends CustomException
{
    protected $errors;
    public function __construct($errors, $message = "Validation Exception", $statusCode = 422)
    {
        parent::__construct($message, $statusCode);
        $this->errors = $errors;
    }

    public function getErrors()
    {
        return $this->errors;
    }
    // This method will define the response when this exception is thrown
    public function render()
    {
        return $this->sendError($this->getErrors(), $this->getMessage(), $this->getStatusCode());
    }
}
