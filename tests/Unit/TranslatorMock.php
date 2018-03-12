<?php

use Illuminate\Contracts\Translation\Translator;

class TranslatorMock implements Translator
{
    public function trans($key, array $replace = [], $locale = null)
    {
        return $key;
    }

    public function transChoice($key, $number, array $replace = [], $locale = null)
    {
    }

    public function getLocale()
    {
    }

    public function setLocale($locale)
    {
    }
}
