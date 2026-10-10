<?php

namespace App\DTO;

/**
 * The breadcrumb trail of a public page, built once in the controller.
 *
 * The same trail feeds the `breadcrumbs` Inertia prop (toArray) and the
 * schema.org BreadcrumbList in the head (toSchema, via HasPageMetadata::shareSeo),
 * so the visible trail and the structured data cannot drift apart.
 */
class BreadcrumbTrail
{
    /**
     * @param  list<BreadcrumbItem>  $items
     */
    public function __construct(public readonly array $items = []) {}

    /**
     * Start a trail at the homepage.
     */
    public static function home(): self
    {
        return (new self)->add(__('home.title'), route('home'));
    }

    /**
     * Return a new trail with the item appended. Leave out the url for the current page.
     */
    public function add(string $label, ?string $url = null): self
    {
        return new self([...$this->items, new BreadcrumbItem($label, $url)]);
    }

    /**
     * @return list<array<string, string|null>>
     */
    public function toArray(): array
    {
        return array_map(fn (BreadcrumbItem $item) => $item->toArray(), $this->items);
    }

    /**
     * Map the trail onto a schema.org BreadcrumbList.
     *
     * An item without url (the current page) is listed without `item`;
     * Google then uses the URL of the page that contains the list.
     *
     * @return array<string, mixed>
     */
    public function toSchema(): array
    {
        $elements = [];

        foreach ($this->items as $index => $item) {
            $element = [
                '@type' => 'ListItem',
                'position' => $index + 1,
                'name' => $item->label,
            ];

            if ($item->url !== null) {
                $element['item'] = $item->url;
            }

            $elements[] = $element;
        }

        return [
            '@type' => 'BreadcrumbList',
            'itemListElement' => $elements,
        ];
    }
}
