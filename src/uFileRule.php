<?php

namespace Zotyo\uFiler;

use Illuminate\Contracts\Translation\Translator;
use Illuminate\Contracts\Validation\Rule;
use Throwable;

class UFileRule implements Rule
{
    protected $repo;
    protected $trans;
    protected $notFoundMessage = 'validation.file_not_found';
    protected $invalidTokenMessage = 'validation.invalid_token';
    protected $message = 'WAITINNG';

    public function __construct(Repository $repo, Translator $trans)
    {
        $this->repo = $repo;
        $this->trans = $trans;
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
        return $this->trans->trans($this->message);
    }
}
