<?php

use Zotyo\uFiler\UFileRule;
use Zotyo\uFiler\Repositories\LocalFileSystemRepository;
use Zotyo\uFiler\File;
use PHPUnit\Framework\TestCase;

require_once 'FileInstanceTrait.php';
require_once 'TranslatorMock.php';

class ValidatorTest extends TestCase
{
    use FileInstanceTrait;

    private $repo;
    private $v;

    public function setUp()
    {
        $this->repo = new LocalFileSystemRepository(__DIR__.'/../junk', 'whatever-', '');
        $this->v = new UFileRule($this->repo, new TranslatorMock);
    }

    public function testNotFoundMissingId()
    {
        $this->assertFalse($this->v->passes([], []));
        $this->assertEquals($this->v->message(), 'validation.file_not_found');
    }

    public function testNotFoundMissingToken()
    {
        $this->assertFalse($this->v->passes([], ['id' => 'none']));
        $this->assertEquals($this->v->message(), 'validation.file_not_found');
    }

    public function testNotFound()
    {
        $this->assertFalse($this->v->passes([], ['id' => 'none', 'token' => 'none']));
        $this->assertEquals($this->v->message(), 'validation.file_not_found');
    }

    public function testInvalidToken()
    {
        $file = $this->repo->store($this->fileInstance());
        $this->assertInstanceOf(File::class, $file);

        $this->assertFalse($this->v->passes([], ['id' => $file->id(), 'token' => 'none']));
        $this->assertEquals($this->v->message(), 'validation.invalid_token');
    }

    public function testOK()
    {
        $file = $this->repo->store($this->fileInstance());
        $this->assertInstanceOf(File::class, $file);
        $data = $file->toArray();

        $this->assertTrue($this->v->passes([], ['id' => $data['id'], 'token' => $data['token']]));
    }
}
