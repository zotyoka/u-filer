<?php

namespace Zotyo\uFiler;

use Illuminate\Contracts\Support\Arrayable;

final class File implements Arrayable
{
    private $id;
    private $url;
    private $token;
    private $name;
    private $mime;
    private $ext;
    private $size;

    public function __construct(string $id, string $url, string $token, string $name, string $mime, string $ext, string $size)
    {
        $this->id = $id;
        $this->url = $url;
        $this->token = $token;
        $this->name = $name;
        $this->mime = $mime;
        $this->ext = $ext;
        $this->size = $size;
    }

    public function id()
    {
        return $this->id;
    }

    public function url()
    {
        return $this->url;
    }

    /**
     * Returns all details of the file as an array.
     * @return array
     */
    public function toArray()
    {
        return [
            'id' => $this->id(),
            'url' => $this->url(),
            'token' => $this->token,
            'client' => [
                'name' => $this->name,
                'mime' => $this->mime,
                'ext' => $this->ext,
                'size' => $this->size,
            ]
        ];
    }

    public function isValidToken($token) : bool
    {
        return $this->token === $token;
    }

    public function __toString() : string
    {
        return $this->id();
    }
}
