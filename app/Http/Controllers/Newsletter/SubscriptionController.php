<?php

namespace App\Http\Controllers\Newsletter;

use App\Events\NewsletterSubscriptionRequested;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\HasPageMetadata;
use App\Http\Requests\Newsletter\SubscribeRequest;
use App\Models\NewsletterSubscriber;
use Inertia\Inertia;
use Inertia\Response;

class SubscriptionController extends Controller
{
    use HasPageMetadata;

    public function subscribe(SubscribeRequest $request): void
    {
        $validated = $request->validated();
        $hours = config('newsletter.subscription.confirmation_expires_after');

        $subscriber = NewsletterSubscriber::updateOrCreate(
            ['email' => $validated['email']],
            [
                'name' => $validated['name'] ?? null,
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

        return Inertia::render('Newsletter/Subscription/Confirmed', [
            'title' => $this->pageTitle('newsletter.subscription.title_confirmed'),
        ]);
    }

    public function unsubscribe(string $token): Response
    {
        $subscriber = NewsletterSubscriber::where('unsubscribe_token', $token)
            ->whereNull('unsubscribed_at')
            ->firstOrFail();
        $subscriber->update([
            'unsubscribed_at' => now(),
        ]);

        return Inertia::render('Newsletter/Subscription/Unsubscribed', [
            'title' => $this->pageTitle('newsletter.subscription.title_unsubscribed'),
        ]);
    }
}
