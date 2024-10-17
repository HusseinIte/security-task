<?php

namespace App\Handlers\File;

use App\Exceptions\FileException;

class FileExtensionValidationHandler extends FileUploadHandler
{
    public function handle($request)
    {
        $originalName = $request->file->getClientOriginalName();
        // Ensure the file extension is valid and there is no path traversal in the file name
        if (preg_match('/\.[^.]+\./', $originalName)) {
            throw new FileException('notAllowedAction');
        }
        return parent::handle($request);
    }
}
