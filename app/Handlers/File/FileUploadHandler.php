<?php

namespace App\Handlers\File;

abstract class FileUploadHandler
{
    protected $nextHandler;
    public function setNext(FileUploadHandler $handler)
    {
        $this->nextHandler = $handler;
        return $handler;
    }

    public function handle($request)
    {
        if ($this->nextHandler) {
            return $this->nextHandler->handle($request);
        }
        return $request;
    }
}
