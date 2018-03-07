<?php

use Zotyo\uFiler\Repositories\LocalFileSystemRepository;

require_once __DIR__.'/GeneralRepositoryTest.php';

class LocalFileSystemRepositoryTest extends GeneralRepositoryTest
{
    public function setUp()
    {
        $this->repo = new LocalFileSystemRepository(__DIR__.'/../junk', 'whatever-', '');
    }
}
