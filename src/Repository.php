<?php

namespace Zotyo\uFiler;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Zotyo\uFiler\Exceptions\FileGeneratorException;
use Zotyo\uFiler\Exceptions\FileNotFoundException;

class Repository
{

    /**
     * Saves an uploaded file
     * @param UploadedFile $file
     * @return File
     * @throws FileGeneratorException
     */
    public function store(UploadedFile $file)
    {
        $instance = $this->preserve();
        $path     = $instance->path();
        $file->move(dirname($path), basename($path));

        $desc = new FileDescriptor($instance);
        $desc->create($file);

        return $instance;
    }

    /**
     * Finds an already uploaded file
     * @param string $id
     * @return File
     * @throws FileNotFoundException
     */
    public function findOrFail($id)
    {
        $file = new File($id);
        if (!$file->exists()) {
            throw (new FileNotFoundException)->setID($id);
        }

        return $file;
    }

    /**
     * Finds an already uploaded file. If no match, will try to use $default as fallback
     * In case the fallback does not exists, throws exception
     * @param string $id
     * @return File
     * @throws FileNotFoundException
     */
    public function find($id, $default = 'default')
    {
        $file = new File($id);
        return $file->exists() ? $file : $this->findOrFail($default);
    }

    /**
     * 
     * @return File
     * @throws FileGeneratorException
     */
    private function preserve()
    {
        $filePath = tempnam((new File(null))->path(), config('u-filer.prefix'));
        if ($filePath === false) {
            throw new FileGeneratorException();
        }
        return new File(basename($filePath));
    }
}