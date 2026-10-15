<?php

namespace App\Travel\Documents;

/**
 * The icons of the documents, as image sources. dompdf draws no inline SVG, but it does draw an SVG image, and so does
 * every browser, so the PDF and the page share them. Train, ticket, transfer and external-link are Lucide's
 * `train-front`, `ticket`, `arrow-right-left` and `external-link` (https://lucide.dev, under the license in
 * Resources/licenses/Lucide.txt), the others come from the design.
 */
class Icon
{
    public const SUN = '#f2a516';

    public const INK = '#2f5560';

    public const MUTED = '#5f6b70';

    private const STROKE = 'fill="none" stroke="%s" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"';

    private const PATHS = [
        'train' => '<path d="M8 3.1V7a4 4 0 0 0 8 0V3.1"/><path d="m9 15-1-1"/><path d="m15 15 1-1"/><path d="M9 19c-2.8 0-5-2.2-5-5v-4a8 8 0 0 1 16 0v4c0 2.8-2.2 5-5 5Z"/><path d="m8 19-2 3"/><path d="m16 19 2 3"/>',
        'transfer' => '<path d="m16 3 4 4-4 4"/><path d="M20 7H4"/><path d="m8 21-4-4 4-4"/><path d="M4 17h16"/>',
        'ticket' => '<path d="M2 9a3 3 0 0 1 0 6v2a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-2a3 3 0 0 1 0-6V7a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2Z"/><path d="M13 5v2"/><path d="M13 17v2"/><path d="M13 11v2"/>',
        'phone' => '<path d="M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3.1 19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2.1 4.2 2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7c.1.9.4 1.8.7 2.7a2 2 0 0 1-.5 2.1L8 9.8a16 16 0 0 0 6 6l1.3-1.3a2 2 0 0 1 2.1-.4c.9.3 1.8.6 2.7.7a2 2 0 0 1 1.7 2z"/>',
        'document' => '<rect x="5" y="2" width="14" height="20" rx="2"/><circle cx="12" cy="10" r="3"/><path d="M9 17h6"/>',
        'shield' => '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="M9 12l2 2 4-4"/>',
        'drop' => '<path d="M12 2.7C9 7 6 10.3 6 14a6 6 0 0 0 12 0c0-3.7-3-7-6-11.3z"/>',
        'cross' => '<path d="M12 5v14M5 12h14"/>',
        'alert' => '<circle cx="12" cy="12" r="10"/><path d="M12 7v6M12 17h.01"/>',
        'sun' => '<circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.9 4.9l1.4 1.4M17.7 17.7l1.4 1.4M2 12h2M20 12h2M4.9 19.1l1.4-1.4M17.7 6.3l1.4-1.4"/>',
        'external-link' => '<path d="M15 3h6v6"/><path d="M10 14 21 3"/><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/>',
        'walk' => '<circle cx="13" cy="4" r="2"/><path d="M10 22l2-7 3 3v6M8 12l3-4 3 2 3 1M9 8l-2 5"/>',
    ];

    /**
     * An icon of the design, drawn in the color, as a data URI.
     */
    public static function src(string $name, string $color = self::INK): string
    {
        return self::dataUri(
            '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" '
            .sprintf(self::STROKE, $color).'>'.self::PATHS[$name].'</svg>'
        );
    }

    /**
     * The logo of Omdat We Reizen, with its name: the bridge, with the sun behind it.
     */
    public static function logo(): string
    {
        return self::dataUri((string) file_get_contents(dirname(__DIR__).'/Resources/logo/logo-text.svg'));
    }

    /**
     * A stop on a route, a dot of 12px: 'from' an open ring, 'via' filled, 'to' the sun with a light ring of 3px, for
     * the end. Each is drawn in the middle of 18px, so the stops of a route line up.
     */
    public static function stop(string $kind): string
    {
        $circle = match ($kind) {
            'from' => '<circle cx="9" cy="9" r="4.5" fill="#ffffff" stroke="'.self::INK.'" stroke-width="3"/>',
            'via' => '<circle cx="9" cy="9" r="6" fill="'.self::INK.'"/>',
            default => '<circle cx="9" cy="9" r="9" fill="#fdf0d8"/><circle cx="9" cy="9" r="6" fill="'.self::SUN.'"/>',
        };

        return self::dataUri('<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 18">'.$circle.'</svg>');
    }

    private static function dataUri(string $svg): string
    {
        return 'data:image/svg+xml;base64,'.base64_encode($svg);
    }
}
