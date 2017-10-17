<?php

namespace Zotyo\uFiler;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileDescriptor
{
    private $path;

    public function create(UploadedFile $uploaded_file)
    {
        file_put_contents($this->path, json_encode([
            'token' => $this->generateToken(),
            'client' => [
                'mime' => $uploaded_file->getClientMimeType(),
                'ext' => $uploaded_file->getClientOriginalExtension(),
                'name' => $uploaded_file->getClientOriginalName(),
                'size' => $uploaded_file->getClientSize()
            ]
        ]));
    }

    public function read()
    {
        return @ json_decode(file_get_contents($this->path));
    }

    public function __construct(File $file)
    {
        $this->path = $file->path().'.json';
    }

    private function generateToken()
    {
        return md5(microtime().rand(0, 123456789));
    }
}