<?php

namespace Zotyo\uFiler;

use Throwable;

// use Illuminate\Contracts\Validation\Rule;

class uFileRule //implements Rule
{
    protected $repo;
    protected $notFoundMessage = 'validation.file_not_found';
    protected $invalidTokenMessage = 'validation.invalid_token';
    protected $message = 'WAITINNG';

    public function __construct(Repository $repo)
    {
        $this->repo = $repo;
    }

    public function passes($attribute, $value)
    {
        try {
            $fileID = $value['id'];
            $token  = $value['token'];
            $file = $this->repo->findOrFail($fileID);
        } catch (Throwable $ex) {
            $this->message = $this->notFoundMessage;
            return false;
        }

        if (!$file->isValidToken($token)) {
            $this->message = $this->invalidTokenMessage;
            return false;
        }
        return true;
    }

    public function message()
    {
        return trans($this->message);
    }
}
