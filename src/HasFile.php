<?php

namespace Zotyo\uFiler;

trait HasFile
{

    /**
     * Returns a File instance for the specified $key column
     * @param string $key Attribute name
     * @return File
     */
    protected function getFile($key)
    {
        return empty($this->attributes[$key]) ? null : (new Repository)->findOrFail($this->attributes[$key]);
    }

    /**
     * Sets an existing File instance to the specified $key attribute
     * @param string $key Attribute name
     * @param mixed $value Value with an existing file identifier
     * @throws Exceptions\FileNotFoundException
     */
    protected function setFile($key, $value)
    {
        if (empty($value)) {
            return $this->attributes[$key] = null;
        }
        if (is_array($value) && array_key_exists('id', $value)) {
            $fileID = $value['id'];
        }
        if (is_object($value) && property_exists($value, 'id')) {
            $fileID = $value['id'];
        }
        if (is_string($value)) {
            $fileID = $value;
        }
        $file                   = (new Repository)->findOrFail($fileID);
        $this->attributes[$key] = (string) $file;
    }
}