<?php

namespace Zotyo\uFiler\Repositories;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Zotyo\uFiler\Exceptions\FileGeneratorException;
use Zotyo\uFiler\Exceptions\FileNotFoundException;
use Zotyo\uFiler\Repository;
use Zotyo\uFiler\File;
use Aws\S3\S3Client;

class AwsS3Repository implements Repository
{
    protected $s3client;

    public function __construct(S3Client $s3client)
    {
        $this->s3client = $s3client;
    }

    public function store(UploadedFile $file) : File
    {
        $s3->putObject([
            'Bucket' => 'my-bucket',
            'Key'    => 'my-object',
            'Body'   => fopen('/path/to/file', 'r'),
            'ACL'    => 'public-read',
        ]);
    }

    public function findOrFail(string $id) : File
    {
    }
    
    public function find(string $id, string $default = 'default') : File
    {
    }
}
