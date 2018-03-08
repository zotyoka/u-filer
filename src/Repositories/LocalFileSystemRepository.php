<?php

namespace Zotyo\uFiler\Repositories;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Zotyo\uFiler\Exceptions\FileGeneratorException;
use Zotyo\uFiler\Exceptions\FileNotFoundException;
use Zotyo\uFiler\Repository;
use Zotyo\uFiler\File;

class LocalFileSystemRepository implements Repository
{
    protected $dir;
    protected $prefix;
    protected $baseUrl;

    public function __construct(string $dir, string $prefix, string $baseUrl)
    {
        $this->dir      = $dir;
        $this->prefix   = $prefix;
        $this->baseUrl  = $baseUrl;
    }
    /**
     * Saves an uploaded file
     * @param UploadedFile $file
     * @return File
     * @throws FileGeneratorException
     */
    public function store(UploadedFile $file) : File
    {
        $path               = $this->preserve();
        $id                 = basename($path);
        $file->move(dirname($path), basename($path));
        $this->storeDescription($id, $file);
        return $this->findOrFail($id);
    }

    /**
     * @param string $id
     * @return File
     * @throws FileNotFoundException
     */
    public function findOrFail(string $id) : File
    {
        if (!$this->exists($id)) {
            throw (new FileNotFoundException)->setID($id);
        }
        return new File($id, $this->url($id), ...$this->restoreDescriptor($id));
    }

    /**
     * Finds an already uploaded file. If no match, will try to use $default as fallback
     * In case the fallback does not exists, throws exception
     * @param string $id
     * @return File
     * @throws FileNotFoundException
     */
    public function find(string $id, string $default = 'default') : File
    {
        return $this->exists($id) ?
            new File($id, $this->url($id), ...$this->restoreDescriptor($id)) :
            $this->findOrFail($default);
    }

    /**
     * @return string
     * @throws FileGeneratorException
     */
    protected function preserve() : string
    {
        $filePath = tempnam($this->dir, $this->prefix);
        if ($filePath === false) {
            throw new FileGeneratorException();
        }
        return $filePath;
    }

    protected function storeDescription(string $id, UploadedFile $file)
    {
        $description = [
                'token' => $this->generateToken(),
                'name'  => $file->getClientOriginalName(),
                'mime'  => $file->getClientMimeType(),
                'ext'   => $file->getClientOriginalExtension(),
                'size'  => $file->getClientSize()
            ];
            
        file_put_contents($this->descriptorPath($id), json_encode($description));
    }

    protected function restoreDescriptor(string $id) : array
    {
        $content    = file_get_contents($this->descriptorPath($id));
        $data       = json_decode($content, true);

        return [
            $data['token'],
            $data['name'],
            $data['mime'],
            $data['ext'],
            $data['size']
        ];
    }

    protected function generateToken() : string
    {
        return sha1(microtime().rand(0, 123456789));
    }

    protected function path(string $id) : string
    {
        return $this->dir.DIRECTORY_SEPARATOR.$id;
    }

    protected function descriptorPath(string $id) : string
    {
        return $this->path($id) . '.json';
    }

    protected function exists(string $id) : bool
    {
        $path = $this->path($id);
        return file_exists($path) && is_file($path);
    }

    protected function url(string $id) : string
    {
        return $this->baseUrl.'/'.$id;
    }
}
