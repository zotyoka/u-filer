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
        $file     = $repo->store($request->file('file'));
        return $file->toArray();
    }
}