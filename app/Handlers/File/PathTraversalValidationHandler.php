<?php

namespace App\Handlers\File;

use App\Exceptions\FileException;

class PathTraversalValidationHandler extends FileUploadHandler
{
    public function handle($request)
    {

        $originalName = $request->file->getClientOriginalName();
        // Check for path traversal attack (e.g., using ../ or ..\ or / to go up directories)
        if (strpos($originalName, '..') !== false || strpos($originalName, '/') !== false || strpos($originalName, '\\') !== false) {
            throw new FileException('pathTraversalDetected');
        }

        return parent::handle($request);
    }
}
