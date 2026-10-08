<?php

namespace App\Services;

use Dom\Element;
use Dom\HTMLDocument;
use Illuminate\Support\Str;

class TripContentParser
{
    /**
     * Key of a section without a title that gives a usable slug, e.g. a text without any H2.
     */
    private const FALLBACK_KEY = 'section';

    /**
     * Split rich text into sections: every top-level H2 starts a section with all content up to the next H2.
     *
     * Content before the first H2 is merged into the first section. A text without any H2 becomes a single section
     * without a title. The HTML is passed through as it is, without extra sanitizing, just like the description
     * was rendered before it got split.
     *
     * @return list<array{key: string, title: string|null, html: string}>
     */
    public function sections(?string $html): array
    {
        if (trim((string) $html) === '') {
            return [];
        }

        // The native HTML5 parser of PHP 8.4 reads UTF-8 as it is, unlike DOMDocument which needs an encoding hack
        $document = HTMLDocument::createFromString('<!DOCTYPE html><body>'.$html, LIBXML_NOERROR);

        $leadingHtml = '';
        $sections = [];

        foreach ($document->body->childNodes as $node) {
            if ($node instanceof Element && $node->localName === 'h2') {
                $title = Str::squish($node->textContent);
                $sections[] = ['title' => $title !== '' ? $title : null, 'html' => ''];

                continue;
            }

            if ($sections === []) {
                $leadingHtml .= $document->saveHtml($node);
            } else {
                $sections[array_key_last($sections)]['html'] .= $document->saveHtml($node);
            }
        }

        if ($sections === []) {
            $sections[] = ['title' => null, 'html' => ''];
        }

        $sections[0]['html'] = $leadingHtml.$sections[0]['html'];

        return $this->withUniqueKeys($sections);
    }

    /**
     * Give every section a key based on the slug of its title, numbered from -2 when the slug is already taken.
     *
     * @param  list<array{title: string|null, html: string}>  $sections
     * @return list<array{key: string, title: string|null, html: string}>
     */
    private function withUniqueKeys(array $sections): array
    {
        $usedKeys = [];

        return array_map(function (array $section) use (&$usedKeys) {
            $baseKey = Str::slug((string) $section['title']) ?: self::FALLBACK_KEY;
            $key = $baseKey;

            for ($number = 2; in_array($key, $usedKeys, true); $number++) {
                $key = "{$baseKey}-{$number}";
            }

            $usedKeys[] = $key;

            return [
                'key' => $key,
                'title' => $section['title'],
                'html' => trim($section['html']),
            ];
        }, $sections);
    }
}
