<?php

namespace Zotyo\uFiler\Repositories;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Zotyo\uFiler\Exceptions\FileGeneratorException;
use Zotyo\uFiler\Exceptions\FileNotFoundException;
use Zotyo\uFiler\Repository;
use Zotyo\uFiler\File;
use Aws\S3\S3Client;
use Throwable;

class AwsS3Repository implements Repository
{
    protected $s3client;
    protected $bucket;
    protected $prefix;

    public function __construct(S3Client $s3client, string $bucket, string $prefix)
    {
        $this->s3client = $s3client;
        $this->bucket = $bucket;
        $this->prefix = $prefix;
    }

    public function store(UploadedFile $file) : File
    {
        $id = $this->uuid($file);
        $this->s3client->putObject([
            'Bucket' => $this->bucket,
            'Key'    => $id,
            'Body'   => fopen($file->getPathname(), 'r'),
            'ACL'    => 'public-read',
            'Metadata' => [
                'token' => $this->generateToken(),
                'client_name'  => $file->getClientOriginalName(),
                'client_mime'  => $file->getClientMimeType(),
                'client_ext'   => $file->getClientOriginalExtension(),
                'client_size'  => $file->getClientSize()
            ]
        ]);

        return $this->findOrFail($id);
    }

    public function findOrFail(string $id) : File
    {
        try {
            $result = $this->s3client->headObject([
                'Bucket' => $this->bucket,
                'Key'    => $id,
            ]);
            $meta = $result->toArray()['@metadata'];
            $headers = $meta['headers'];
            $url = $meta['effectiveUri'];
            $token = $headers['x-amz-meta-token'];
            $name = $headers['x-amz-meta-client_name'];
            $mime = $headers['x-amz-meta-client_mime'];
            $ext = $headers['x-amz-meta-client_ext'];
            $size = $headers['x-amz-meta-client_size'];

            return new File($id, $url, $token, $name, $mime, $ext, $size);
        } catch (Throwable $ex) {
            throw (new FileNotFoundException("", 0, $ex))->setID($id);
        }
    }
    
    public function find(string $id, string $default = 'default') : File
    {
        try {
            return $this->findOrFail($id);
        } catch (FileNotFoundException $ex) {
            return $this->findOrFail($default);
        }
    }

    protected function uuid(UploadedFile $file) : string
    {
        return $this->prefix.sha1(microtime().hash_file('sha256', $file->getPathname()));
    }

    protected function generateToken() : string
    {
        return sha1(microtime().rand(0, 123456789));
    }
}
