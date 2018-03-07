<?php

namespace Zotyo\uFiler;

use Symfony\Component\HttpFoundation\File\UploadedFile;

interface Repository
{
    public function store(UploadedFile $file) : File;

    public function findOrFail(string $id) : File;

    public function find(string $id, string $default = 'default') : File;
}
