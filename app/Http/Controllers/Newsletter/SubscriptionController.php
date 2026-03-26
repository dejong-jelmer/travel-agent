<?php

namespace App\Http\Controllers\Newsletter;

use App\Events\NewsletterSubscriptionRequested;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\HasPageMetadata;
use App\Http\Requests\Newsletter\SubscribeRequest;
use App\Models\NewsletterSubscriber;
<<<<<<< HEAD
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
=======
>>>>>>> b9e884b3fa401a1a668de43a24b0f92b9502b33e
use Inertia\Inertia;
use Inertia\Response;

class SubscriptionController extends Controller
{
    use HasPageMetadata;

<<<<<<< HEAD
    public function subscribe(SubscribeRequest $request): RedirectResponse
=======
    public function subscribe(SubscribeRequest $request): void
>>>>>>> b9e884b3fa401a1a668de43a24b0f92b9502b33e
    {
        $validated = $request->validated();
        $hours = config('newsletter.subscription.confirmation_expires_after');

<<<<<<< HEAD
        DB::transaction(function () use ($validated, $hours): void {
            // Lock the row for this email (if it exists) for the duration of the transaction.
            // This prevents a concurrent request from inserting or confirming a record between
            // our existence check and the updateOrCreate, eliminating the race condition.
            $existing = NewsletterSubscriber::where('email', $validated['email'])
                ->lockForUpdate()
                ->first();

            if ($existing !== null
                && $existing->confirmed_at !== null
                && $existing->unsubscribed_at === null
            ) {
                throw ValidationException::withMessages([
                    'already_subscribed' => true,
                ]);
            }

            $subscriber = NewsletterSubscriber::updateOrCreate(
                ['email' => $validated['email']],
                [
                    'name' => $validated['name'] ?? null,
                    'subscribed_at' => now(),
                    'confirmation_expires_at' => now()->addHours($hours),
                ]
            );

            event(new NewsletterSubscriptionRequested($subscriber));
        });

        return back();
=======
        $subscriber = NewsletterSubscriber::updateOrCreate(
            ['email' => $validated['email']],
            [
                'name' => $validated['name'] ?? null,
                'subscribed_at' => now(),
                'confirmation_expires_at' => now()->addHours($hours),
            ]
        );

        event(new NewsletterSubscriptionRequested($subscriber));
>>>>>>> b9e884b3fa401a1a668de43a24b0f92b9502b33e
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
