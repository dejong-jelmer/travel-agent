<?php

namespace App\Models;

use App\Enums\BlogPost\Status;
use App\Models\Traits\ManagesImages;
use App\Models\Traits\Sortable;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property Image|null $heroImage
 */
class BlogPost extends Model
{
    use HasFactory,
        ManagesImages,
        SoftDeletes,
        Sortable;

    protected $perPage = 15;

    protected $fillable = [
        'title',
        'slug',
        'body',
        'excerpt',
        'summary',
        'meta_title',
        'meta_description',
        'status',
        'published_at',
    ];

    protected $casts = [
        'status' => Status::class,
        'published_at' => 'datetime',
    ];

    protected $appends = ['is_published'];

    protected array $searchable = ['title', 'excerpt'];

    protected array $sortable = ['id', 'title', 'published_at'];

    protected array $defaultSort = [
        'column' => 'created_at',
        'direction' => 'desc',
    ];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function heroImage(): MorphOne
    {
        return $this->morphOne(Image::class, 'imageable')->where('is_primary', true);
    }

    /**
     * The trips this story goes with.
     *
     * @return BelongsToMany<Trip, $this>
     */
    public function trips(): BelongsToMany
    {
        return $this->belongsToMany(Trip::class);
    }

    /**
     * Scope a query to only include published posts.
     */
    #[Scope]
    protected function published(Builder $query): void
    {
        $query->where('status', Status::Published)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    /**
     * Get the total is_published attribute for this BlogPost .
     *
     * @return Attribute<bool, never>
     */
    public function isPublished(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->status === Status::Published
        );
    }

    /**
     * When the story last changed for a reader: its last save, but never before it was published. A story scheduled
     * ahead goes live without a save, so its updated_at can lie before its publication.
     */
    public function lastModifiedAt(): ?CarbonInterface
    {
        if ($this->published_at === null || $this->updated_at === null) {
            return $this->updated_at ?? $this->published_at;
        }

        return $this->updated_at->max($this->published_at);
    }

    /**
     * The key points of the summary, one per line. Empty lines are dropped, as is a bullet typed in front of a point.
     *
     * @return list<string>
     */
    public function summaryPoints(): array
    {
        $points = array_map(
            fn (string $line) => trim(preg_replace('/^\s*[-*•]\s*/u', '', $line) ?? $line),
            preg_split('/\R/u', (string) $this->summary) ?: [],
        );

        return array_values(array_filter($points, fn (string $point) => $point !== ''));
    }

    /**
     * Split the body where the summary goes: after the introduction, which ends at the first H2. A body without one
     * is split after its first paragraph; a body without paragraphs is all introduction.
     *
     * @return array{intro: string, rest: string}
     */
    public function bodyAroundSummary(): array
    {
        $body = (string) $this->body;
        $heading = stripos($body, '<h2');

        if ($heading !== false) {
            $splitAt = $heading;
        } else {
            $paragraphEnd = stripos($body, '</p>');
            $splitAt = $paragraphEnd === false ? strlen($body) : $paragraphEnd + strlen('</p>');
        }

        return [
            'intro' => substr($body, 0, $splitAt),
            'rest' => substr($body, $splitAt),
        ];
    }

    /**
     * Build a schema.org BlogPosting JSON-LD object from the BlogPost's model fields.
     *
     * @return array<string, string|null>
     */
    public function toBlogPostingSchema(): array
    {
        return array_filter([
            '@type' => 'BlogPosting',
            'headline' => $this->title,
            'description' => $this->meta_description ?: $this->excerpt,
            'image' => $this->heroImage?->public_url,
            'datePublished' => $this->published_at?->toIso8601String(),
            'dateModified' => $this->lastModifiedAt()?->toIso8601String(),
            'mainEntityOfPage' => route('blog.show', $this->slug),
        ], fn ($v) => $v !== null);
    }
}
