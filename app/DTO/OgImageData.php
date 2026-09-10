<?php

namespace App\DTO;

readonly class OgImageData
{
    public function __construct(
        public string $url,
        public int $width,
        public int $height,
        public string $type,
        public string $alt,
    ) {}
}
