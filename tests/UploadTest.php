<?php
namespace Zotyo\uFiler\Tests;

use Illuminate\Http\UploadedFile;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Zotyo\uFiler\Repository;
use Tests\TestCase;

class UploadTest extends TestCase
{

    use WithoutMiddleware;
    /**
     * LazyLoaded
     * @var stdClass
     */
    private $fileInstance;

    public function testUploadSuccess()
    {
        $jsonResponse = $this->call('POST', '/upload', [], [], ['file' => $this->fileInstance()->file]);

        $this->assertEquals(200, $jsonResponse->getStatusCode());
        $response = json_decode($jsonResponse->getContent());
        $this->assertObjectHasAttribute('id', $response);
        $this->assertObjectHasAttribute('token', $response);
        $this->assertObjectHasAttribute('url', $response);
        $this->assertObjectHasAttribute('client', $response);
        $this->assertEquals($response->client->mime, $this->fileInstance()->mime);
        $this->assertEquals($response->client->name, $this->fileInstance()->name);
        $this->assertEquals($response->client->size, $this->fileInstance()->size);
        return $response;
    }

    public function testUploadSuccess2()
    {
        // just to have two uploaded files
        return $this->testUploadSuccess();
    }

    /**
     * @depends testUploadSuccess
     */
    public function testInstantiationOfUploadedFile($response)
    {

        $file = (new Repository)->findOrFail($response->id);
        $this->assertEquals($response->client, $file->description()->client);
    }

    /**
     * @depends testUploadSuccess
     */
    public function testValidatorPasses($response)
    {
        $v = $this->app['validator']->make([
            'file' => [
                'id'    => $response->id,
                'token' => $response->token,
            ]
            ], [
            'file' => 'verify-file-by-token'
        ]);
        $this->assertTrue($v->passes());
    }

    /**
     * @depends testUploadSuccess
     */
    public function testValidatorFailsByInvalidToken($response)
    {
        $v = $this->app['validator']->make([
            'file' => [
                'id'    => $response->id,
                'token' => 'x',
            ]
            ], [
            'file' => 'verify-file-by-token'
        ]);
        $this->assertFalse($v->passes());
    }

    /**
     * @depends testUploadSuccess
     * @depends testUploadSuccess2
     */
    public function testValidatorFailsByMixedTokens($responseA, $responseB)
    {
        $v = $this->app['validator']->make([
            'file' => [
                'id'    => $responseA->id,
                'token' => $responseB->token
            ]
            ], [
            'file' => 'verify-file-by-token'
        ]);
        $this->assertFalse($v->passes());
    }

    private function fileInstance()
    {
        if ($this->fileInstance === null) {
            $fullPath = __DIR__.'/test.jpeg';
// Using UploadedFile in test mode will move the file. 
// We need the file for further tests.
            copy(__DIR__.'/images.jpeg', $fullPath);
            $name     = 'example.jpg';
            $mime     = 'image/jpeg';
            $size     = filesize($fullPath);

            $this->fileInstance = (object) [
                    'name' => $name,
                    'mime' => $mime,
                    'size' => $size,
                    'file' => new UploadedFile($fullPath, $name, $mime, $size, null, true)
            ];
        }
        return $this->fileInstance;
    }
}
