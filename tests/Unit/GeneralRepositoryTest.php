<?php

use Zotyo\uFiler\File;
use PHPUnit\Framework\TestCase;

require_once 'FileInstanceTrait.php';

abstract class GeneralRepositoryTest extends TestCase
{
    use FileInstanceTrait;
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
}
