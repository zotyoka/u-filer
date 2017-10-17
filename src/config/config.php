<?php
return [
    /*
     * The relative path of the folder where the package stores the files
     * The folder should be publicly accessible
     */
    'relative_path' => 'uploads',
    /*
     * Name of the file input the package is waiting for
     */
    'file-name' => 'file',
    /**
     * Prefix for the uploaded file.
     * It can be useful if you are about to migrate your files between environments
     */
    'prefix' => env('APP_ENV'),
    /*
     * Registers endpoint for uploading files.
     * If the predefined ControllerAction is not suitable for you,
     * feel free to disable it and see Zotyo\uFiler\Controllers\UploadTrait
     */
    'route' => [
        'enabled' => true,
        'url' => 'upload',
    ],
];
