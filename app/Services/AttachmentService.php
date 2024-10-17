<?php

namespace App\Services;

use App\DTO\FileDTO;
use App\Handlers\File\FileExtensionValidationHandler;
use App\Handlers\File\FileStorageHandler;
use App\Handlers\File\FileTypeValidationHandler;
use App\Handlers\File\PathTraversalValidationHandler;

/**
 * Class AttachmentService
 *
 * This service handles file uploads for tasks, ensuring the file passes through
 * a chain of validation handlers before being stored. The validation chain includes
 * file extension validation, file type validation, path traversal protection,
 * and file storage.
 */
class AttachmentService
{
    /**
     * Uploads a file for a specific task, validating it through a chain of handlers
     * including file extension, file type, path traversal protection, and storage.
     *
     * @param array $data The file data to be uploaded. It should include the file and its metadata.
     * @param int $taskId The ID of the task to which the file is attached.
     *
     * @return FileDTO The validated and stored file data.
     *
     * @throws InvalidFileExtensionException If the file has an invalid extension.
     * @throws InvalidFileTypeException If the file type is not allowed.
     * @throws PathTraversalException If path traversal is detected in the file name.
     * @throws FileStorageException If the file cannot be stored.
     */
    public function uploadFile(array $data, $taskId)
    {
        $fileRequest = new FileDTO($data);
        // Create the chain
        $fileExtentionHandler = new FileExtensionValidationHandler();
        $fileTypeHandler = new FileTypeValidationHandler();
        $pathTraversalHandler = new PathTraversalValidationHandler();
        $storageHandler = new FileStorageHandler();

        // Build the chain: file extenion -> type -> path traversal -> storage
        $fileExtentionHandler
            ->setNext($fileTypeHandler)
            ->setNext($pathTraversalHandler)
            ->setNext($storageHandler);

        // Start the chain
        $fileExtentionHandler->handle($fileRequest);

        return $fileRequest;
    }
}
