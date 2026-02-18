<?php

namespace App\Http\Controllers;

use App\DTO\ContactFormData;
use App\Http\Controllers\Traits\HasPageMetadata;
use App\Http\Requests\SubmitContactRequest;
use App\Mail\AdminContactFormNotificationMail;
use App\Models\Trip;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

class HomeController extends Controller
{
    use HasPageMetadata;

    public function home(): Response
    {
        return Inertia::render('Home', [
            'title' => $this->pageTitle('home.home'),
            'trips' => Trip::with(['destinations', 'heroImage'])->published()->featured()->get(),
            'seo' => $this->pageSeo('home.home_seo'),
        ]);
    }

    public function about(): Response
    {
        return Inertia::render('About', [
            'title' => $this->pageTitle('home.about'),
            'seo' => $this->pageSeo('home.about_seo'),
        ]);
    }

    public function contact(): Response
    {
        return Inertia::render('Contact', [
            'title' => $this->pageTitle('home.contact'),
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
            phone: $validated['phone'],
        );

        try {
            Mail::to($address)->send(
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
            'title' => $this->pageTitle('home.privacy_statement'),
            'seo' => $this->pageSeo('home.privacy_seo'),
        ]);
    }

    public function terms(): Response
    {
        return Inertia::render('Terms', [
            'title' => $this->pageTitle('home.conditions'),
            'seo' => $this->pageSeo('home.terms_seo'),
        ]);
    }
}
