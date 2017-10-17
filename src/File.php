<?php

namespace Zotyo\uFiler;

use Illuminate\Contracts\Support\Arrayable;

class File implements Arrayable
{
    private $id;

    public function __construct($id)
    {
        $this->id = $id;
    }

    public function exists()
    {
        $path = $this->path();
        return file_exists($path) && is_file($path);
    }

    public function path()
    {
        return public_path($this->getRelativePath());
    }

    public function url()
    {
        return asset($this->getRelativePath());
    }

    public function id()
    {
        return $this->id;
    }

    public function relPath()
    {
        return $this->getRelativePath();
    }

    /**
     * Returns all details of the file as an array.
     * @return array
     */
    public function toArray()
    {
        $descriptor = new FileDescriptor($this);

        $ret = [
            'id' => $this->id(),
            'url' => $this->url(),
            ] + (array) $descriptor->read();

        return $ret;
    }

    public function isValidToken($token)
    {
        $descriptor = new FileDescriptor($this);
        return $descriptor->read()->token === $token;
    }

    public function description()
    {
        $desc = new FileDescriptor($this);
        return $desc->read();
    }

    private function getRelativePath()
    {
        return config('u-filer.relative_path').'/'.$this->id;
    }

    public function __toString()
    {
        return $this->id();
    }
}