<?php

/*
    |--------------------------------------------------------------------------
    | Default Settings for Images
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default settings for images storing. The disk &
    | directory used for storage, the maximum file size and the allowed mime types
    |
    */
return [
    'disk' => 'public',
    'directory' => 'images',
    'max_size' => 10240,
    'allowed_mimes' => ['jpeg', 'jpg', 'png', 'gif', 'webp'],
];
