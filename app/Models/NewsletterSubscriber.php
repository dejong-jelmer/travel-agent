<?php

namespace App\Models;

use App\Enums\Newsletter\SubscriberStatus;
use App\Models\Traits\Sortable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NewsletterSubscriber extends Model
{
    use HasFactory,
        Sortable;

    protected $perPage = 15;

    protected $fillable = [
        'email',
        'name',
        'token',
        'confirmation_token',
        'unsubscribe_token',
        'confirmed_at',
        'confirmation_expires_at',
        'subscribed_at',
        'unsubscribed_at',
    ];

    protected $casts = [
        'confirmed_at' => 'datetime',
        'confirmation_expires_at' => 'datetime',
        'subscribed_at' => 'datetime',
        'unsubscribed_at' => 'datetime',
    ];

    protected $appends = [
        'status',
        'status_label',
    ];

    // Sortable properties
    protected $searchable = ['email', 'name'];

    protected $sortable = ['id', 'email', 'name'];

    protected $defaultSort = [
        'column' => 'id',
        'direction' => 'desc',
    ];

    protected array $scopeFilters = [
        'status' => SubscriberStatus::class,
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($subscriber) {
            $subscriber->token ??= bin2hex(random_bytes(32));
            $subscriber->confirmation_token ??= bin2hex(random_bytes(32));
            $subscriber->unsubscribe_token ??= bin2hex(random_bytes(32));
        });
    }

    /**
     * Check if the subscriber is active (confirmed and not unsubscribed).
     */
    public function isActive(): bool
    {
        return $this->confirmed_at !== null && $this->unsubscribed_at === null;
    }

    /**
     * Check if the subscriber is pending (not yet confirmed).
     */
    public function isPending(): bool
    {
        return $this->confirmed_at === null
            && $this->unsubscribed_at === null
            && ($this->confirmation_expires_at === null || $this->confirmation_expires_at >= now());
    }

    /**
     * Check if the subscriber's confirmation has expired.
     */
    public function isExpired(): bool
    {
        return $this->confirmed_at === null
            && $this->unsubscribed_at === null
            && $this->confirmation_expires_at !== null
            && $this->confirmation_expires_at < now();
    }

    /**
     * Check if the subscriber has unsubscribed.
     */
    public function isUnsubscribed(): bool
    {
        return $this->unsubscribed_at !== null;
    }

    /**
     * Get the subscriber's status.
     *
     * @return Attribute<SubscriberStatus, never>
     */
    protected function status(): Attribute
    {
        /** @phpstan-ignore return.type */
        return Attribute::get(function (): SubscriberStatus {
            if ($this->isUnsubscribed()) {
                return SubscriberStatus::Unsubscribed;
            }
            if ($this->isActive()) {
                return SubscriberStatus::Active;
            }
            if ($this->isExpired()) {
                return SubscriberStatus::Expired;
            }

            return SubscriberStatus::Pending;
        });
    }

    /**
     * Get the status label.
     *
     * @return Attribute<string, never>
     */
    protected function statusLabel(): Attribute
    {
        return Attribute::get(fn () => $this->status->label());
    }

    /**
     * Scope a query to only include active subscribers (confirmed and not unsubscribed).
     */
    #[Scope]
    protected function active(Builder $query): void
    {
        $query->whereNotNull('confirmed_at')
            ->whereNull('unsubscribed_at');
    }

    /**
     * Scope a query to only include pending subscribers (not yet confirmed).
     */
    #[Scope]
    protected function pending(Builder $query): void
    {
        $query->whereNull('confirmed_at')
            ->whereNull('unsubscribed_at')
            ->where(function ($q) {
                $q->whereNull('confirmation_expires_at')
                    ->orWhere('confirmation_expires_at', '>=', now());
            });
    }

    /**
     * Scope a query to only include expired subscribers (confirmation expired).
     */
    #[Scope]
    protected function expired(Builder $query): void
    {
        $query->whereNull('confirmed_at')
            ->whereNull('unsubscribed_at')
            ->whereNotNull('confirmation_expires_at')
            ->where('confirmation_expires_at', '<', now());
    }

    /**
     * Scope a query to only include unsubscribed subscribers.
     */
    #[Scope]
    protected function unsubscribed(Builder $query): void
    {
        $query->whereNotNull('unsubscribed_at');
    }
}
