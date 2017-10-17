<?php

namespace Zotyo\uFiler;

use Exception as AnyException;
use Lang;

class UploadedFileValidator
{

    public function verifyFileByToken($attribute, array $value, $parameters, $validator)
    {
        $fileID = $value['id'];
        $token  = $value['token'];
        try {
            $file = (new Repository)->findOrFail($fileID);
        } catch (AnyException $ex) {
            $validator->setFallbackMessages([
                'verify_file_by_token' => $this->validationMessage(),
            ]);
            return false;
        }

        if (!$file->isValidToken($token)) {
            $validator->setFallbackMessages([
                'verify_file_by_token' => $this->validationMessage(),
            ]);
            return false;
        }

        return true;
    }

    private function validationMessage()
    {
        return Lang::has('validation.verify_file_by_token') ? Lang::get('validation.verify_file_by_token') :
            'Can not use the uploaded file! Please try to upload it again.';
    }
}