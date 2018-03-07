<?php

use Zotyo\uFiler\Repository;
use Zotyo\uFiler\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use PHPUnit\Framework\TestCase;

abstract class GeneralRepositoryTest extends TestCase
{
    /**
     * @var Repository
     */
    protected $repo;

    public function testStore()
    {
        $file = $this->repo->store($this->fileInstance());
        $this->assertInstanceOf(File::class, $file);
        return $file;
    }
    /**
     * @depends testStore
     */
    public function testFindOrFail(File $file)
    {
        $file = $this->repo->findOrFail($file->id());
        $this->assertInstanceOf(File::class, $file);
        $array = $file->toArray();
        $this->assertEquals($this->name(), $array['client']['name']);
        $this->assertEquals($this->mime(), $array['client']['mime']);
    }

    /**
     * @depends testStore
     */
    public function testFindFallback(File $file)
    {
        $file2 = $this->repo->find('__DOES_NOT_EXIST__', $file->id());
        $this->assertInstanceOf(File::class, $file2);
        $array = $file->toArray();
        $this->assertEquals($file->id(), $file2->id());
    }

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
