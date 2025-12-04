<?php

namespace App\Http\Controllers\Admin\Newsletter;

use App\Enums\ImageRelation;
use App\Enums\Newsletter\CampaignStatus;
use App\Exceptions\CampaignAlreadySentException;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\HasPageMetadata;
use App\Http\Requests\Newsletter\CreateCampaignRequest;
use App\Http\Requests\Newsletter\UpdateCampaignRequest;
use App\Mail\NewsletterCampaignMail;
use App\Models\NewsletterCampaign;
use App\Models\Trip;
use App\Services\NewsletterCampaignService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

class CampaignController extends Controller
{
    use HasPageMetadata;

    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        return Inertia::render('Admin/Newsletter/Campaign/Index', [
            'campaigns' => NewsletterCampaign::paginate(),
            'title' => $this->pageTitle('newsletter.campaign.title_index'),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        return Inertia::render('Admin/Newsletter/Campaign/Create', [
            'title' => $this->pageTitle('newsletter.campaign.title_create'),
            'statusOptions' => CampaignStatus::options(),
            'trips' => Trip::with('heroImage')->orderBy('name')->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateCampaignRequest $request): RedirectResponse
    {
        $validatedFiles = $request->safe()->only('hero_image');
        $validatedFields = $request->validated();

        $campaign = NewsletterCampaign::create([
            'subject' => $validatedFields['subject'],
            'content' => $validatedFields['content'],
            'preview_text' => $validatedFields['preview_text'],
            'status' => $validatedFields['status'],
            'scheduled_at' => $validatedFields['scheduled_at'],
        ]);

        // Sync trips
        $campaign->trips()->sync($validatedFields['trips'] ?? []);

        // Sync heroImage
        if (isset($validatedFiles['hero_image'])) {
            $campaign->syncImages($validatedFiles['hero_image'], ImageRelation::HeroImage, true);
        }

        return redirect()->route('admin.newsletter.campaigns.index')->with('success', __('newsletter.campaign.created'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(NewsletterCampaign $campaign): Response
    {
        return Inertia::render('Admin/Newsletter/Campaign/Edit', [
            'campaign' => $campaign->load(['heroImage', 'trips']),
            'statusOptions' => CampaignStatus::options(),
            'title' => $this->pageTitle('newsletter.campaign.title_edit'),
            'trips' => Trip::with('heroImage')->orderBy('name')->get(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCampaignRequest $request, NewsletterCampaign $campaign): RedirectResponse
    {
        $validatedFiles = $request->safe()->only('hero_image');
        $validatedFields = $request->validated();

        $campaign->update([
            'subject' => $validatedFields['subject'],
            'content' => $validatedFields['content'],
            'preview_text' => $validatedFields['preview_text'],
            'status' => $validatedFields['status'],
            'scheduled_at' => $validatedFields['scheduled_at'],
        ]);
        // Sync trips
        $campaign->trips()->sync($validatedFields['trips'] ?? []);

        // Sync heroImage
        $campaign->syncImages($validatedFiles['hero_image'] ?? '', ImageRelation::HeroImage, true);

        return redirect()->route('admin.newsletter.campaigns.index')->with('success', __('newsletter.campaign.updated'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(NewsletterCampaign $campaign): RedirectResponse
    {
        NewsletterCampaign::destroy($campaign->id);

        return redirect()->route('admin.newsletter.campaigns.index')->with('success', __('newsletter.campaign.deleted'));
    }

    /**
     * Send a test email of the campaign to the authenticated user.
     */
    public function sendTest(NewsletterCampaign $campaign): RedirectResponse
    {
        $user = Auth::user();
        $email = $user->email ?? '';
        try {
            Mail::to($email)->send(new NewsletterCampaignMail($campaign));
        } catch (Throwable $e) {
            return back()->with('error', __('newsletter.campaign.test_email_failed', ['error_message' => $e->getMessage()]));
        }

        return back()->with('success', __('newsletter.campaign.test_email_sent', ['email' => $email]));
    }

    /**
     * Send the campaign to all active newsletter subscribers.
     */
    public function send(NewsletterCampaign $campaign): RedirectResponse
    {
        try {
            app(NewsletterCampaignService::class)->sendCampaign($campaign);

            return back()->with('success', __('newsletter.campaign.sent'));
        } catch (CampaignAlreadySentException $e) {
            return back()->with('error', __('newsletter.campaign.sent_failed', ['error_message' => $e->getMessage()]));
        }
    }
}
