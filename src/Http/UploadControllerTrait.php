<?php

namespace Zotyo\uFiler\Http;

use Zotyo\uFiler\Repository;

trait UploadControllerTrait
{

    /**
     * Validates file and stores in destination directory(specified in the config)
     */
    public function upload(FormRequest $request, Repository $repo)
    {
        $fileName = config('file-uploader.file-name');
        $file     = $repo->store($request->file($fileName));
        return $file->toArray();
    }
}