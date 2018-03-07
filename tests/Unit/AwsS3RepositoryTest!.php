<?php

use Zotyo\uFiler\Repositories\AwsS3Repository;
use Aws\S3\S3Client;

require_once __DIR__.'/GeneralRepositoryTest.php';

class AwsS3RepositoryTest extends GeneralRepositoryTest
{
    public function setUp()
    {
        $s3client = new S3Client([
            'version' => 'latest',
            'region'  => 'us-west-2'
        ]);
        $this->repo = new AwsS3Repository($s3client);
    }
}
