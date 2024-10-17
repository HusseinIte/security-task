<?php

namespace App\Exceptions;

use App\Models\ExceptionLog;
use App\Traits\ApiResponseTrait;
use Exception;
use Illuminate\Support\Facades\Log;

class CustomException extends Exception
{
    use ApiResponseTrait;
    protected $statusCode;

    public function __construct($message = "Custom Error", $statusCode = 400)
    {
        parent::__construct($message);
        $this->statusCode = $statusCode;
    }

    public function getStatusCode()
    {
        return $this->statusCode;
    }

    // This method will log exception in db when exception is thrown
    public function report()
    {

        try {

            // Insert exception details into the exceptions table
            ExceptionLog::create([
                'exception_type' => get_class($this),
                'message' => $this->getMessage(),
                'status_code' => $this->getStatusCode(),
                'file' => $this->getFile(),
                'line' => $this->getLine(),
                'stack_trace' => $this->getTraceAsString(),
            ]);
        } catch (Exception $e) {
            // Handle any errors that may occur when logging the exception, such as a DB error
            Log::error("Failed to log exception: " . $e->getMessage());
        }
    }


    // This method will define the response when this exception is thrown
    public function render()
    {
        return $this->sendError(null, $this->getMessage(), $this->getStatusCode());
    }
}
