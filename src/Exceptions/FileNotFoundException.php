<?php
namespace Zotyo\uFiler\Exceptions;

use Exception;

class FileNotFoundException extends Exception
{
    public $message = 'The referenced file is not uploaded.';

    public function setID($id)
    {
        $this->message = 'Could not find {'.$id.'}.'.$this->message;
        return $this;
    }
}
