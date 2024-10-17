<?php

namespace App\Handlers\File;

use App\Exceptions\FileException;

class FileTypeValidationHandler extends FileUploadHandler
{
    protected $allowedMimeTypes = [
        'image/jpeg',
        'image/png',
        'image/gif',
        'image/webp',
        'application/pdf',
        'application/msword',
        'audio/mpeg',
        'video/mp4'
    ];

    public function handle($request)
    {
        $file = $request->file;
        $mimeType = $file->getClientMimeType();
        // Validate the MIME type to ensure it's an image
        if (!in_array($mimeType, $this->allowedMimeTypes)) {
            throw new FileException('invalidFileType');
        }

        return parent::handle($request);
    }
}
