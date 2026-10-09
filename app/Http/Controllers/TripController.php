<?php

namespace App\Http\Controllers;

use App\DTO\BreadcrumbTrail;
use App\Enums\Destination\TravelInfo;
use App\Enums\Trip\PracticalInfo;
use App\Http\Controllers\Traits\HasPageMetadata;
use App\Models\Trip;
use App\Services\CountryService;
use App\Services\ItineraryService;
use App\Services\OgImageService;
use App\Services\TripContentParser;
use App\Services\TripItemService;
use Inertia\Inertia;
use Inertia\Response;

class TripController extends Controller
{
    use HasPageMetadata;

    public function __construct(
        private readonly CountryService $countryService,
        private readonly OgImageService $ogImages,
        private readonly TripContentParser $contentParser,
        private readonly ItineraryService $itineraries,
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        $trips = Trip::with(['heroImage', 'prices'])->published()->get();
        $seo = $this->shareSeo('seo.trips');

        return Inertia::render('Trip/Index', [
            'title' => $seo['title'],
            'trips' => $trips,
            'countries' => $this->countryService->getCountriesForTrips($trips),
            'seo' => $seo,
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Trip $trip): Response
    {
        $trip->load(['heroImage', 'images', 'destinations', 'itineraries', 'itineraries.image', 'items']);

        $breadcrumbs = BreadcrumbTrail::home()
            ->add(__('trip.title_index'), route('trips.index'))
            ->add($trip->name);

        $seo = $this->shareSeo(overrides: [
            'title' => $trip->meta_title.' | '.config('app.name'),
            'description' => $trip->meta_description,
            ...$this->ogImageSeo($this->ogImages->forImage($trip->heroImage, $trip->name)),
        ], jsonLd: array_merge($trip->toTouristTripSchema(), [
            'provider' => $this->travelAgencySchema(),
        ]), breadcrumbs: $breadcrumbs);

        return Inertia::render('Trip/Show', [
            'title' => $seo['title'],
            // The description is only sent split into sections, the highlights and the itinerary only ready for
            // display, so none of them goes along twice
            'trip' => $trip->makeHidden(['description', 'highlights', 'itineraries']),
            'descriptionSections' => $this->descriptionSections($trip),
            'highlights' => $this->highlights($trip),
            'itinerary' => $this->itineraries->forDisplay($trip),
            'tripItems' => TripItemService::aggregate($trip),
            'practicalSections' => PracticalInfo::labels(),
            'travelInfoSections' => TravelInfo::labels(),
            'breadcrumbs' => $breadcrumbs->toArray(),
            'seo' => $seo,
        ]);
    }

    /**
     * Split the description into sections and give each a variant: the journey section is shown dark, all others
     * light. The first section opens the page in the top card and always stays light, as does every section when
     * the stored key no longer matches a title.
     *
     * Every section after the first also gets the id of the gallery image shown next to it, or null without one.
     *
     * @return list<array{key: string, title: string|null, html: string, variant: 'light'|'dark', image_id: int|null}>
     */
    private function descriptionSections(Trip $trip): array
    {
        $sections = $this->contentParser->sections($trip->description);
        $sectionImages = $trip->section_images;
        $galleryIds = $trip->images->modelKeys();

        return array_map(function (array $section, int $index) use ($trip, $sectionImages, $galleryIds) {
            $imageId = $index > 0 ? ($sectionImages[$section['key']] ?? null) : null;

            return [
                ...$section,
                'variant' => $index > 0 && $section['key'] === $trip->journey_section ? 'dark' : 'light',
                'image_id' => in_array($imageId, $galleryIds, true) ? $imageId : null,
            ];
        }, $sections, array_keys($sections));
    }

    /**
     * The highlights ready for display: a highlight with a category gets the name of its icon and the label shown
     * above the title, its own label or else the default label of the category. Without a category both are null.
     *
     * @return list<array{title: string, description: string|null, category: string|null, icon: string|null, label: string|null}>
     */
    private function highlights(Trip $trip): array
    {
        return array_map(fn (array $highlight) => [
            'title' => $highlight['title'],
            'description' => $highlight['description'],
            'category' => $highlight['category']?->value,
            'icon' => $highlight['category']?->icon(),
            'label' => $highlight['category'] ? ($highlight['label'] ?? $highlight['category']->label()) : null,
        ], $trip->highlights ?? []);
    }
}
