<?php

namespace App\Http\Controllers;

use App\DTO\BreadcrumbTrail;
use App\Enums\Destination\TravelInfo;
use App\Enums\Trip\PracticalInfo;
use App\Http\Controllers\Traits\HasPageMetadata;
use App\Models\Trip;
use App\Services\CountryService;
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
            // The description is only sent split into sections, so its HTML does not go along twice
            'trip' => $trip->makeHidden('description'),
            'descriptionSections' => $this->descriptionSections($trip),
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
     * @return list<array{key: string, title: string|null, html: string, variant: 'light'|'dark'}>
     */
    private function descriptionSections(Trip $trip): array
    {
        $sections = $this->contentParser->sections($trip->description);

        return array_map(fn (array $section, int $index) => [
            ...$section,
            'variant' => $index > 0 && $section['key'] === $trip->journey_section ? 'dark' : 'light',
        ], $sections, array_keys($sections));
    }
}
