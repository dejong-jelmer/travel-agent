<?php

namespace App\Http\Controllers;

use App\DTO\ContactFormData;
use App\Http\Controllers\Traits\HasPageMetadata;
use App\Http\Requests\SubmitContactRequest;
use App\Mail\AdminContactFormNotificationMail;
use App\Models\Trip;
use App\Services\SustainabilityPdfService;
use App\Services\TermsPdfService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

class HomeController extends Controller
{
    use HasPageMetadata;

    public function home(): Response
    {
        $seo = $this->shareSeo('home.home_seo');

        return Inertia::render('Home', [
            'title' => $seo['title'],
            'trips' => Trip::with(['destinations', 'heroImage'])->published()->featured()->orderBy('published_at', 'desc')->get(),
            'seo' => $seo,
        ]);
    }

    public function about(): Response
    {
        $seo = $this->shareSeo('home.about_seo');

        return Inertia::render('About', [
            'title' => $seo['title'],
            'seo' => $seo,
        ]);
    }

    public function contact(): Response
    {
        $seo = $this->shareSeo('home.contact_seo');

        return Inertia::render('Contact', [
            'title' => $seo['title'],
            'seo' => $seo,
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
        $seo = $this->shareSeo('home.privacy_seo', ['robots' => 'noindex, follow']);

        return Inertia::render('Privacy', [
            'title' => $seo['title'],
            'seo' => $seo,
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
        $seo = $this->shareSeo('home.terms_seo', ['robots' => 'noindex, follow']);

        return Inertia::render('Terms', [
            'title' => $seo['title'],
            'seo' => $seo,
            'terms' => [
                'version' => config('terms.version'),
                'updated' => config('terms.updated'),
            ],
        ]);
    }

    public function guarantee(): Response
    {
        $seo = $this->shareSeo('home.guarantee_seo');

        return Inertia::render('Guarantee', [
            'title' => $seo['title'],
            'seo' => $seo,
        ]);
    }

    public function vvkr(): Response
    {
        $seo = $this->shareSeo('home.vvkr_seo');

        return Inertia::render('VvKR', [
            'title' => $seo['title'],
            'seo' => $seo,
        ]);
    }

    public function downloadSustainabilityDocument(SustainabilityPdfService $sustainabilityPdf): BinaryFileResponse
    {
        return response()->download($sustainabilityPdf->path(), SustainabilityPdfService::FILENAME);
    }

    public function downloadTerms(TermsPdfService $termsPdf): BinaryFileResponse
    {
        return response()->download($termsPdf->path(), TermsPdfService::FILENAME);
    }
}
