<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NewsletterSubscriber extends Model
{
    use HasFactory;

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

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($subscriber) {
            $subscriber->token ??= bin2hex(random_bytes(32));
            $subscriber->confirmation_token ??= bin2hex(random_bytes(32));
            $subscriber->unsubscribe_token ??= bin2hex(random_bytes(32));
        });
    }
}
