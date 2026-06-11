<?php

namespace App\Http\Controllers;

use App\DTO\ContactFormData;
use App\Http\Controllers\Traits\HasPageMetadata;
use App\Http\Requests\SubmitContactRequest;
use App\Mail\AdminContactFormNotificationMail;
use App\Models\Trip;
use App\Services\TermsPdfService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

class HomeController extends Controller
{
    use HasPageMetadata;

    public function home(): Response
    {
        return Inertia::render('Home', [
            'title' => $this->pageTitle('home.home_seo'),
            'trips' => Trip::with(['destinations', 'heroImage'])->published()->featured()->orderBy('published_at', 'desc')->get(),
            'seo' => $this->pageSeo('home.home_seo'),
        ]);
    }

    public function about(): Response
    {
        return Inertia::render('About', [
            'title' => $this->pageTitle('home.about_seo'),
            'seo' => $this->pageSeo('home.about_seo'),
        ]);
    }

    public function contact(): Response
    {
        return Inertia::render('Contact', [
            'title' => $this->pageTitle('home.contact_seo'),
            'seo' => $this->pageSeo('home.contact_seo'),
        ]);
    }

    public function submitContact(SubmitContactRequest $request): HttpResponse
    {
        $validated = $request->validated();
        $address = config('contact.mail');

        $contact = new ContactFormData(
            name: $validated['name'],
            email: $validated['email'],
            text: $validated['text'],
            phone: $validated['phone'] ?? null,
        );

        try {
            Mail::to($address)->queue(
                new AdminContactFormNotificationMail($contact)
            );
        } catch (\Throwable $e) {
            Log::error('Contact form notification mail failed: '.$e->getMessage(), [
                'contact_name' => $contact->name,
                'contact_email' => $contact->email,
                'admin_email' => $address,
            ]);
            Log::error('Stack trace: '.$e->getTraceAsString());
        }

        return response()->json([
            'success' => true,
        ], 200);
    }

    public function privacy(): Response
    {
        return Inertia::render('Privacy', [
            'title' => $this->pageTitle('home.privacy_seo'),
            'seo' => $this->pageSeo('home.privacy_seo'),
            'newsletterRetentionMonths' => (int) config('privacy.newsletter.subscription.retention_months', 3),
            'bookingRetentionYears' => (int) config('privacy.booking.retention_years', 7),
            'specialRequestsRetentionDays' => (int) config('privacy.booking.special_requests_retention_days', 7),
            'tripRequestsRetentionYears' => (int) config('privacy.trip_request.retention_years ', 1),
            'privacy' => [
                'version' => config('privacy.version'),
                'updated' => config('privacy.updated'),
            ],
        ]);
    }

    public function terms(): Response
    {
        return Inertia::render('Terms', [
            'title' => $this->pageTitle('home.terms_seo'),
            'seo' => $this->pageSeo('home.terms_seo'),
            'terms' => [
                'version' => config('terms.version'),
                'updated' => config('terms.updated'),
            ],
        ]);
    }

    public function guarantee(): Response
    {
        return Inertia::render('Guarantee', [
            'title' => $this->pageTitle('home.guarantee_seo'),
            'seo' => $this->pageSeo('home.guarantee_seo'),
        ]);
    }

    public function downloadSustainabilityDocument(): BinaryFileResponse
    {
        $path = 'sustainability/Reizen met aandacht.pdf';
        $disk = Storage::disk('local');

        abort_unless($disk->exists($path), 404);

        return response()->download($disk->path($path), 'Reizen met aandacht.pdf');
    }

    public function downloadTerms(TermsPdfService $termsPdf): BinaryFileResponse
    {
        return response()->download($termsPdf->path(), TermsPdfService::FILENAME);
    }
}
