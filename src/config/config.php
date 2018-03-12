<?php
return [
    /*
     * Registers endpoint for uploading files.
     * If the predefined ControllerAction is not suitable for you,
     * feel free to disable it and see Zotyo\uFiler\Controllers\UploadTrait
     */
    'route'         => [
        'enabled' => true,
        'url'     => 'upload',
        'name'    => 'upload',
    ],

    /**
     * Prefix for the uploaded file.
     * It can be useful if you are about to migrate your files between environments
     */
    'prefix'        => function () {
        return env('APP_ENV').'-';
    },

    /**
     * The repository implementation. Should refer a key of repos.
     */
    'repo' => 'local',

    'repos' => [
        'local' => [
            'dir' => function () {
                return public_path('uploads');
            },
            'baseUrl' => function () {
                return asset('uploads');
            },
        ],
        'aws' => [
            /**
             * You'll need to install AWS-SDK on your own!
             * composer require aws/aws-sdk-php
             */
            'bucket' => 'uploads-bucket',
            's3client' => [
                'version' => 'latest',
                'region'  => 'eu-west-1'
            ]
        ],
    ]
];
