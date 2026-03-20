<?php

namespace App\Models;

use App\Enums\Newsletter\CampaignSubscriberStatus;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Model;

class NewsletterCampaignSubscriber extends Model
{
    protected $table = 'newsletter_campaign_subscriber';

    public $timestamps = false;

    protected $fillable = [
        'campaign_id',
        'subscriber_id',
        'status',
        'sent_at',
        'error_message',
        'failed_at',
    ];

    protected $casts = [
        'status' => CampaignSubscriberStatus::class,
        'sent_at' => 'datetime',
        'failed_at' => 'datetime',
    ];

    #[Scope]
    public function queued($query)
    {
        return $query->where('status', CampaignSubscriberStatus::Queued);
    }

    #[Scope]
    public function sent($query)
    {
        return $query->where('status', CampaignSubscriberStatus::Sent);
    }

    #[Scope]
    public function failed($query)
    {
        return $query->where('status', CampaignSubscriberStatus::Failed);
    }
}
