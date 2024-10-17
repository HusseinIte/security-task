<?php

namespace App\Handlers\File;

use App\Exceptions\FileException;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileStorageHandler extends FileUploadHandler
{
    public function handle($request)
    {
        $file = $request->file;
        // Generate a safe, random file name
        $fileName = Str::random(32);

        $extension = $file->getClientOriginalExtension(); // Safe way to get file extension

        $folder = $this->determineFolderByMimeType($file->getClientMimeType());
        // Store the file securely
        $path = Storage::disk('encrypted')->putFileAs($folder, $request->file, $fileName . '.' . $extension);
        // Get the full URL path of the stored file
        $request->filePath = Storage::url($path);
        $request->mime_type = $file->getClientMimeType();
        return parent::handle($request);
    }

    private function determineFolderByMimeType($mimeType)
    {
        if (strpos($mimeType, 'image') !== false) {
            return 'Images';
        } elseif (strpos($mimeType, 'pdf') !== false || strpos($mimeType, 'msword') !== false) {
            return 'Documents';
        } elseif (strpos($mimeType, 'audio') !== false) {
            return 'Audio';
        } elseif (strpos($mimeType, 'video') !== false) {
            return 'Videos';
        } else {
            throw new FileException('unsupportedFileType');
        }
    }
}
