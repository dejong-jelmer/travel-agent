<?php

namespace App\Models;

use App\Enums\BlogPost\Status;
use App\Models\Traits\ManagesImages;
use App\Models\Traits\Sortable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
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
}
