<?php

namespace App\Http\Controllers\Admin\Newsletter;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\HasPageMetadata;
use App\Models\NewsletterSubscriber;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class SubscriberController extends Controller
{
    use HasPageMetadata;

    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        return Inertia::render('Admin/Newsletter/Subscriber/Index', [
            'subscribers' => NewsletterSubscriber::paginate(),
            'title' => $this->pageTitle('newsletter.subscriber.title_index'),
        ]);
    }

    /**
     * resendConfirmation to $subscriber on expired confirmation token
     */
    public function resendConfirmation(NewsletterSubscriber $subscriber)
    {
        // resendConfirmation to $subscriber on expired confirmation token
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
