<?php

namespace App\Http\Controllers;

use App\Events\NewsletterSubscriptionRequested;
use App\Http\Requests\SubscribeNewsLetterRequest;
use App\Models\NewsletterSubscriber;
use Inertia\Inertia;
use Inertia\Response;


class NewsletterController extends Controller
{

    public function subscribe(SubscribeNewsLetterRequest $request): void
    {
        $validated = $request->validated();
        $hours = config('newsletter.confirmation_expires_after');

        $subscriber = NewsletterSubscriber::updateOrCreate(
            ['email' => $validated['email']],
            [
                'name' => $validated['name'] ?? null,
                'unsubscribed_at' => null,
                'subscribed_at' => now(),
                'confirmation_expires_at' => now()->addHours($hours)
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

        if (!$subscriber->update([
            'confirmation_expires_at' => null,
            'confirmed_at' => now()
        ])) {
            abort(404);
        }

        return Inertia::render('Newsletter/Confirmed', [
            'title' => config('app.name') . " - Nieuwsbrief inschrijving bevestigd",
        ]);
    }

    public function unsubscribe(string $token): Response
    {
        $subscriber = NewsletterSubscriber::where('unsubscribe_token', $token)
            ->whereNull('unsubscribed_at')
            ->firstOrFail();

        if (!$subscriber->update([
            'confirmed_at' => null,
            'unsubscribed_at' => now()
        ])) {
            abort(404);
        }

        return Inertia::render('Newsletter/Unsubscribed', [
            'title' => config('app.name') . " - Nieuwsbrief uitschrijving bevestigd",
        ]);
    }
}
