<?php

namespace App\Models;

use App\Enums\Newsletter\CampaignStatus;
use App\Models\Traits\HasFormattedDates;
use App\Models\Traits\ManagesImages;
use App\Services\NewsletterCampaignService;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class NewsletterCampaign extends Model
{
    use HasFactory,
        ManagesImages,
        HasFormattedDates;

    protected array $formattedDates = [
        'scheduled_at' => ['format' => 'DD-MM-YYYY HH:mm'],
        'sent_at' => ['format' => 'DD-MM-YYYY HH:mm'],
    ];

    protected $perPage = 10;

    protected $fillable = [
        'subject',
        'content',
        'preview_text',
        'status',
        'scheduled_at',
        'sent_at',
        'sent_count',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'sent_at' => 'datetime',
        'status' => CampaignStatus::class
    ];

    protected $appends = [
        'scheduled_at_formatted',
        'sent_at_formatted'
    ];

    protected static function booted(): void
    {
        parent::boot();
        static::deleting(function ($campaign) {
            $campaign->heroImage()->delete();
        });
    }

    protected function scheduledAtFormatted(): Attribute
    {
        return Attribute::get(fn() => $this->getFormattedDate('scheduled_at'));
    }

    protected function sentAtFormatted(): Attribute
    {
        return Attribute::get(fn() => $this->getFormattedDate('sent_at'));
    }

    public function sentTo(): BelongsToMany
    {
        return $this->belongsToMany(NewsletterSubscriber::class)->withPivot('sent_at');
    }

    public function heroImage(): MorphOne
    {
        return $this->morphOne(Image::class, 'imageable')->where('is_primary', true);
    }

    public function trips(): BelongsToMany
    {
        return $this->BelongsToMany(Trip::class)
            ->withTimestamps()
            ->orderBy('order');
    }
}
