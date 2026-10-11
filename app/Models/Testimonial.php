<?php

namespace App\Models;

use App\Enums\TestimonialRelationship;
use App\Models\Traits\ManagesImages;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;

/**
 * A short quote from someone who travelled, with an optional full account below it. A testimonial without a trip is
 * a general one. The relationship tells a customer from family or a friend; the author's name and context are always
 * shown, so a personal story never passes for a customer review.
 *
 * @property TestimonialRelationship $relationship
 * @property \Illuminate\Support\Carbon|null $travelled_on
 * @property Trip|null $trip
 * @property Image|null $image
 */
class Testimonial extends Model
{
    use HasFactory,
        ManagesImages;

    public const MAX_QUOTE_LENGTH = 300;

    public const MAX_BODY_LENGTH = 1500;

    public const MAX_AUTHOR_CONTEXT_LENGTH = 120;

    protected $fillable = [
        'trip_id',
        'quote',
        'body',
        'author_name',
        'relationship',
        'author_context',
        'travelled_on',
        'link_url',
        'link_label',
        'is_published',
        'sort_order',
    ];

    protected $casts = [
        'relationship' => TestimonialRelationship::class,
        // Serialised without a time, as the month picker in the admin form reads it
        'travelled_on' => 'date:Y-m-d',
        'is_published' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function trip(): BelongsTo
    {
        return $this->belongsTo(Trip::class);
    }

    /**
     * The photo of the author.
     */
    public function image(): MorphOne
    {
        return $this->morphOne(Image::class, 'imageable');
    }

    #[Scope]
    protected function published(Builder $query): void
    {
        $query->where('is_published', true);
    }

    /**
     * The full account split into paragraphs on its blank lines. A single line break within a paragraph becomes a
     * space, as text pasted from an email often breaks its lines at a fixed width.
     *
     * @return list<string>
     */
    public function bodyParagraphs(): array
    {
        if (blank($this->body)) {
            return [];
        }

        $paragraphs = preg_split('/\R\s*\R/u', trim($this->body)) ?: [];

        return array_map(fn (string $paragraph) => (string) preg_replace('/\s*\R\s*/u', ' ', trim($paragraph)), $paragraphs);
    }
}
