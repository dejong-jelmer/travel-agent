<?php

namespace App\Http\Controllers\Admin\Newsletter;

use App\Enums\Newsletter\SubscriberStatus;
use App\Events\NewsletterSubscriptionRequested;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\HasDataTableFilters;
use App\Http\Controllers\Traits\HasPageMetadata;
use App\Models\NewsletterSubscriber;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class SubscriberController extends Controller
{
    use HasDataTableFilters;
    use HasPageMetadata;

    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        $query = NewsletterSubscriber::query();

        // Apply status filter using model scopes
        if ($statusFilter = request('status')) {
            $status = SubscriberStatus::tryFrom($statusFilter);
            if ($status) {
                $query->{$status->value}();
            }
        }

        // Apply DataTable filters (excluding status as it's handled above)
        $this->applyDataTableFilters($query, [
            'searchable' => ['email', 'name'],
            'searchableRelations' => [],
            'filterable' => [],
            'sortable' => ['id', 'email', 'name'],
            'belongsToSorts' => [],
            'defaultSort' => ['id', 'desc'],
        ]);

        return Inertia::render('Admin/Newsletter/Subscriber/Index', [
            'subscribers' => $query->paginate()->withQueryString(),
            'totalSubscribers' => NewsletterSubscriber::count(),
            'filters' => $this->getCurrentFilters(['status']),
            'statusOptions' => SubscriberStatus::options(),
            'title' => $this->pageTitle('newsletter.subscriber.title_index'),
        ]);
    }

    /**
     * resendConfirmation to $subscriber on expired confirmation token
     */
    public function resendConfirmation(NewsletterSubscriber $subscriber): RedirectResponse
    {
        $hours = config('newsletter.subscription.confirmation_expires_after');
        $subscriber->update([
            'confirmation_expires_at' => now()->addHours($hours),
        ]);

        event(new NewsletterSubscriptionRequested($subscriber));

        return redirect()->route('admin.newsletter.subscribers.index')->with('success', __('newsletter.subscriber.confirmation_resend'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(NewsletterSubscriber $subscriber): RedirectResponse
    {
        NewsletterSubscriber::destroy($subscriber->id);

        return redirect()->route('admin.newsletter.subscribers.index')->with('success', __('newsletter.subscriber.deleted'));
    }
}
