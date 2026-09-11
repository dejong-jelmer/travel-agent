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
    'max_size' => 5120,
    'allowed_mimes' => ['jpeg', 'jpg', 'png', 'webp'],
    // Resized WebP versions of every uploaded image, served through srcset (see ImageVariantService).
    'variants' => [
        'directory' => 'variants',
        'widths' => [480, 768, 1200, 1600],
        // Width of the variant used as img src, for browsers that ignore srcset.
        'fallback_width' => 1200,
        'quality' => 78,
        // Variants larger than this (in kilobytes) are encoded again at a lower quality, down to min_quality.
        'max_size' => 250,
        'min_quality' => 50,
    ],
];
