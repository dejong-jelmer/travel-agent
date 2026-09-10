<?php

return [
    'currency' => 'EUR',
    'meta_title_max_length' => 60,
    'meta_description_max_length' => 160,
    // Served through Vite, so the URL carries a content hash (see OgImageService::default).
    'default_og_image' => 'resources/images/og_image.jpg',
    // JPEG derivative generated for every hero image, at the 1.91:1 size link previews expect.
    'og_image' => [
        'directory' => 'og',
        'width' => 1200,
        'height' => 630,
        'quality' => 82,
    ],
    // Maps the app locale to the Open Graph locale format.
    'og_locales' => [
        'nl' => 'nl_NL',
        'en' => 'en_GB',
    ],
    'logo' => 'images/logos/logo-text.png',

    // How long the rendered sitemap XML stays cached, in seconds.
    'sitemap_cache_ttl' => 3600,
];
