<?php

namespace App\Models;

use App\Enums\Transport;
use App\Enums\Trip\PracticalInfo;
use App\Enums\Trip\TripType;
use App\Models\Traits\HasFormattedDates;
use App\Models\Traits\ManagesImages;
use App\Models\Traits\Sortable;
use App\Support\MoneyHelper;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

/**
 * @property string $name
 * @property string $og_image_url
 * @property \Illuminate\Support\Collection $image_paths
 * @property string $destinations_formatted
 * @property Image|null $heroImage
 * @property array<int, array{value: string, label: string}> $transport_formatted
 */
class Trip extends Model
{
    use HasFactory,
        HasFormattedDates,
        ManagesImages,
        SoftDeletes,
        Sortable;

    protected $perPage = 10;

    protected array $formattedDates = [
        'published_at' => ['format' => 'dddd LL'],
    ];

    protected $fillable = [
        'name',
        'slug',
        'intro',
        'description',
        'transport',
        'featured',
        'published_at',
        'highlights',
        'practical_info',
        'blocked_dates',
        'min_advance_days',
        'meta_title',
        'meta_description',
    ];

    protected $appends = [
        'image_paths',
        'price_formatted',
        'is_expected',
        'destinations_formatted',
        'published_at_formatted',
        'og_image_url',
        'transport_formatted',
    ];

    protected $casts = [
        'transport' => 'array',
        'highlights' => 'array',
        'practical_info' => 'array',
        'blocked_dates' => 'array',
        'min_advance_days' => 'integer',
        'published_at' => 'date',
        'featured' => 'boolean',
        'type' => TripType::class,
    ];

    // Sortable properties
    protected $searchable = ['name'];

    protected $searchableRelations = ['destinations.name'];

    protected $sortable = [
        'id',
        'name',
        'duration',
        'published_at',
        'destinations',
    ];

    protected $sortableBelongsToMany = [
        'destinations' => [
            'relation' => 'destinations',
            'column' => 'name',
            'pivot_table' => 'destination_trip',
            'pivot_foreign_key' => 'trip_id',
            'pivot_related_key' => 'destination_id',
        ],
    ];

    protected static function booted(): void
    {
        parent::boot();

        $clearNavCache = fn () => \Illuminate\Support\Facades\Cache::forget(config('cache.keys.nav_countries'));
        static::saved($clearNavCache);
        static::deleted($clearNavCache);
        static::restored($clearNavCache);

        static::deleting(function ($trip) {
            $trip->images()->delete();
            $trip->heroImage()->delete();
            $trip->itineraries()->delete();
            $trip->items()->delete();
        });

        static::restoring(function ($trip) {
            $trip->images()->withTrashed()->restore();
            $trip->heroImage()->withTrashed()->restore();
            $trip->itineraries()->withTrashed()->restore();
        });
    }

    public function destinations(): BelongsToMany
    {
        return $this->belongsToMany(Destination::class)->withTimestamps();
    }

    public function items(): HasMany
    {
        return $this->hasMany(TripItem::class);
    }

    /**
     * Scope a query to only include featured trips.
     */
    #[Scope]
    protected function featured(Builder $query): void
    {
        $query->where('featured', true);
    }

    /**
     * Scope a query to only include published trips.
     */
    #[Scope]
    protected function published(Builder $query): void
    {
        $query->where('published_at', '<=', today());
    }

    protected function publishedAtFormatted(): Attribute
    {
        return Attribute::get(fn () => $this->getFormattedDate('published_at'));
    }

    /**
     * Get a formatted, comma-separated list of destination names.
     *
     * Multiple destinations are joined with commas and an ampersand before the last item.
     * Example: "Netherlands, Belgium & Germany"
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute<string, never>
     */
    public function destinationsFormatted(): Attribute
    {
        return Attribute::get(function () {
            /** @var \Illuminate\Database\Eloquent\Collection<int, Destination> $destinations */
            $destinations = $this->destinations;
            $names = $destinations->map(fn (Destination $d) => $d->region ? $d->region.', '.$d->name : $d->name);

            return match ($names->count()) {
                0 => '',
                1 => $names->first(),
                default => $names->slice(0, -1)->implode(', ').' & '.$names->last()
            };
        });
    }

    public function itineraries(): HasMany
    {
        return $this->hasMany(Itinerary::class)->orderBy('order');
    }

    public function recalculateDuration(): void
    {
        $max = $this->itineraries()
            ->selectRaw('MAX(COALESCE(day_to, day_from)) as max_day')
            ->value('max_day');

        $this->duration = $max ?? 0;
        $this->saveQuietly();
    }

    public function prices(): HasMany
    {
        return $this->hasMany(TripPrice::class);
    }

    /**
     * Get the lowest base price per person across all price rows.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute<float|null, never>
     */
    protected function startingFromPrice(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->prices->min('base_price_pp')
        );
    }

    /**
     * Get the formatted starting price for display purposes.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute<string, never>
     */
    protected function priceFormatted(): Attribute
    {
        return Attribute::make(
            get: fn () => empty($this->starting_from_price)
                ? null
                : number_format((float) $this->starting_from_price / MoneyHelper::CENTS_PER_UNIT, 0, ',', '.')
        );
    }

    /**
     * Whether this trip has no prices set yet and should be shown as "expected".
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute<bool, never>
     */
    protected function isExpected(): Attribute
    {
        return Attribute::make(
            get: fn () => empty($this->starting_from_price)
        );
    }

    public function images(): MorphMany
    {
        return $this->morphMany(Image::class, 'imageable')->where('is_primary', false)->orderBy('order');
    }

    public function heroImage(): MorphOne
    {
        return $this->morphOne(Image::class, 'imageable')->where('is_primary', true);
    }

    /**
     * Get a collection of image paths associated with this trip.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute<\Illuminate\Support\Collection, never>
     */
    public function imagePaths(): Attribute
    {
        return Attribute::get(fn () => $this->images->pluck('path'));
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function tripRequests(): HasMany
    {
        return $this->hasMany(TripRequest::class);
    }

    /**
     * Get the hero image URL for Open Graph usage.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute<string, never>
     */
    public function ogImageUrl(): Attribute
    {
        return Attribute::get(
            fn () => $this->heroImage?->public_url ?? asset(config('seo.default_og_image')) // @phpstan-ignore nullsafe.neverNull
        );
    }

    /**
     * Get the description as plain text, with all rich text markup removed.
     *
     * A space is injected before every tag so block elements do not glue the
     * surrounding words together once the tags are stripped.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute<string, never>
     */
    protected function descriptionPlain(): Attribute
    {
        return Attribute::get(
            fn () => Str::squish(
                strip_tags(str_replace('<', ' <', html_entity_decode((string) $this->description)))
            )
        );
    }

    /**
     * Get the meta_description property or fallback to substring of $trip->description.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute<string, never>
     */
    protected function metaDescription(): Attribute
    {
        return Attribute::get(
            fn (?string $value) => $value ?? Str::substr(
                $this->description_plain,
                0,
                config(
                    'seo.meta_description_max_length'
                )
            )
        );
    }

    /**
     * Get the meta_title property or fallback to substring of $trip->name.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute<string, never>
     */
    protected function metaTitle(): Attribute
    {
        return Attribute::get(
            fn (?string $value) => $value ?? Str::substr(
                $this->name ?? '',
                0,
                config(
                    'seo.meta_title_max_length'
                )
            )
        );
    }

    /**
     * Get transport modes with translated labels.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute<array, never>
     */
    public function transportFormatted(): Attribute
    {
        return Attribute::get(
            fn () => collect($this->transport ?? [])
                ->map(fn (string $value) => [
                    'value' => $value,
                    'label' => Transport::from($value)->label(),
                ])
                ->toArray()
        );
    }

    /**
     * Get the trip highlights, stored as a list of ['title' => ..., 'description' => ...]
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute<array<int, array{title: string, description: string|null}>|null, mixed>
     */
    protected function highlights(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => json_encode(
                collect(is_array($value) ? $value : [])
                    ->map(fn ($highlight) => [
                        'title' => trim((string) ($highlight['title'] ?? '')),
                        'description' => trim((string) ($highlight['description'] ?? '')) ?: null,
                    ])
                    ->filter(fn (array $highlight) => $highlight['title'] !== '')
                    ->values()
                    ->all()
            )
        );
    }

    /**
     * Get the practical info with all keys from PracticalInfo enum
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute<array, never>
     */
    protected function practicalInfo(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                $decoded = is_null($value) ? [] : json_decode($value, true);

                // Get all keys from PracticalInfo enum
                $allKeys = collect(PracticalInfo::cases())
                    ->mapWithKeys(fn ($case) => [$case->value => ''])
                    ->all();

                // Merge with existing values
                return array_merge($allKeys, $decoded);
            },
            set: fn ($value) => json_encode($value ?? [])
        );
    }

    /**
     * Build a schema.org TouristTrip JSON-LD object from the trip's model fields.
     *
     * The canonical price lives in the `starting_from_price` accessor (lowest
     * `base_price_pp` across price rows, stored in cents) - the same source that
     * feeds `price_formatted`. Trips without a price are "expected" and get no Offer.
     *
     * @return array<string, mixed>
     */
    public function toTouristTripSchema(): array
    {
        $schema = [
            '@type' => 'TouristTrip',
            'name' => $this->name,
            'description' => $this->meta_description,
            'image' => $this->og_image_url,
            'url' => route('trips.show', $this->slug),
        ];

        if ($itinerary = $this->toItinerarySchema()) {
            $schema['itinerary'] = $itinerary;
        }

        if (! $this->is_expected && $this->starting_from_price !== null) {
            $schema['offers'] = [
                '@type' => 'AggregateOffer',
                'lowPrice' => round($this->starting_from_price / 100, 2),
                'priceCurrency' => config('seo.currency', 'EUR'),
                'availability' => 'https://schema.org/InStock',
                'url' => route('trips.show', $this->slug),
            ];
        }

        return $schema;
    }

    /**
     * Map the trip highlights onto a schema.org ItemList of tourist attractions.
     *
     * Highlights are stored as rows of `title` and `description`. The order is
     * explicit so crawlers keep the editorial sequence instead of re-sorting.
     *
     * @return array<string, mixed>|null
     */
    private function toItinerarySchema(): ?array
    {
        $elements = [];

        foreach ($this->highlights ?? [] as $highlight) {
            $title = trim(strip_tags($highlight['title']));

            if ($title === '') {
                continue;
            }

            $item = [
                '@type' => 'TouristAttraction',
                'name' => $title,
            ];

            $description = trim(strip_tags((string) ($highlight['description'] ?? '')));

            if ($description !== '') {
                $item['description'] = "{$title}: {$description}";
            }

            $elements[] = [
                '@type' => 'ListItem',
                'position' => count($elements) + 1,
                'item' => $item,
            ];
        }

        if ($elements === []) {
            return null;
        }

        return [
            '@type' => 'ItemList',
            'itemListOrder' => 'https://schema.org/ItemListOrderAscending',
            'numberOfItems' => count($elements),
            'itemListElement' => $elements,
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
