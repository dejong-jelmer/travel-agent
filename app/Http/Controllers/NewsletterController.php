<?php

namespace App\Http\Controllers;

use App\Events\NewsletterSubscriptionRequested;
use App\Http\Controllers\Traits\HasPageMetadata;
use App\Http\Requests\SubscribeNewsletterRequest;
use App\Models\NewsletterSubscriber;
use Inertia\Inertia;
use Inertia\Response;

class NewsletterController extends Controller
{
    use HasPageMetadata;

    public function subscribe(SubscribeNewsletterRequest $request): void
    {
        $validated = $request->validated();
        $hours = config('newsletter.confirmation_expires_after');

        $subscriber = NewsletterSubscriber::updateOrCreate(
            ['email' => $validated['email']],
            [
                'name' => $validated['name'] ?? null,
                'unsubscribed_at' => null,
                'subscribed_at' => now(),
                'confirmation_expires_at' => now()->addHours($hours),
            ]
        );

        event(new NewsletterSubscriptionRequested($subscriber));
    }

    public function confirm(string $token): Response
    {
        $subscriber = NewsletterSubscriber::where('confirmation_token', $token)
            ->whereNull('confirmed_at')
            ->where('confirmation_expires_at', '>=', now())
            ->firstOrFail();

        $subscriber->update([
            'confirmation_expires_at' => null,
            'confirmed_at' => now(),
        ]);

        return Inertia::render('Newsletter/Confirmed', [
            'title' => $this->pageTitle('newsletter.title_confirmed'),
        ]);
    }

    public function unsubscribe(string $token): Response
    {
        $subscriber = NewsletterSubscriber::where('unsubscribe_token', $token)
            ->whereNull('unsubscribed_at')
            ->firstOrFail();
        $subscriber->update([
            'confirmed_at' => null,
            'unsubscribed_at' => now(),
        ]);

        return Inertia::render('Newsletter/Unsubscribed', [
            'title' => $this->pageTitle('newsletter.title_unsubscribed'),
        ]);
    }
}
