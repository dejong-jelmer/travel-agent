<?php

namespace App\Http\Controllers\Admin\Newsletter;

use App\Enums\Newsletter\SubscriberStatus;
use App\Events\NewsletterSubscriptionRequested;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\HasPageMetadata;
use App\Http\Requests\DataTableRequest;
use App\Models\NewsletterSubscriber;
use App\Services\DataTableService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class SubscriberController extends Controller
{
    use HasPageMetadata;

    public function __construct(private DataTableService $dataTableService) {}

    /**
     * Display a listing of the resource.
     */
    public function index(DataTableRequest $request): Response
    {
        $query = NewsletterSubscriber::query();

        // Apply status filter using model scopes
        NewsletterSubscriber::applyScopeFilters($query);

        // Merge validated data with validated filters
        $validatedData = array_merge(
            $request->validated(),
            $request->getValidatedFilters(['status'])
        );

        // Apply DataTable filters (excluding status as it's handled above)
        $this->dataTableService
            ->withValidatedData($validatedData)
            ->applySortFilters($query, NewsletterSubscriber::dataTableConfig());

        return Inertia::render('Admin/Newsletter/Subscriber/Index', [
            'subscribers' => $query->paginate()->withQueryString(),
            'totalSubscribers' => NewsletterSubscriber::count(),
            'filters' => $this->dataTableService->getCurrentSortFilters(['status']),
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
