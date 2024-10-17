<?php

namespace App\DTO;

class FileDTO
{
    public $file;
    public $filePath;
    public $name;
    public $alt_text;
    public $mime_type;

    public function __construct(array $data)
    {
        $this->file = $data['file'];
        $this->name = isset($data['name']) ? $data['name'] : $this->file->getClientOriginalName();
        $this->alt_text = isset($data['alt_text']) ? $data['alt_text'] : null;
    }
}
