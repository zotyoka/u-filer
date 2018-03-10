<?php

use Symfony\Component\HttpFoundation\File\UploadedFile;

trait FileInstanceTrait
{
    protected function fileInstance()
    {
        // Using UploadedFile in test mode will move the file.
        // We need duplicate the file.
        copy(__DIR__.'/../images.jpeg', $this->path());

        return new UploadedFile($this->path(), $this->name(), $this->mime(), $this->size(), null, true);
    }

    protected function path()
    {
        return __DIR__.'/../junk/test.jpeg';
    }

    protected function mime()
    {
        return 'image/jpeg';
    }

    protected function name()
    {
        return 'example.jpg';
    }

    protected function size()
    {
        return filesize($this->path());
    }
}
