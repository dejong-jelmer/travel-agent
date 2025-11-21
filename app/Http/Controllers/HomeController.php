<?php

namespace App\Http\Controllers;

use App\DTO\ContactFormData;
use App\Http\Controllers\Traits\HasPageTitle;
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
    use HasPageTitle;

    public function home(): Response
    {
        return Inertia::render('Home', [
            'trips' => Trip::with(['countries', 'featuredImage'])->active()->featured()->get(),
        ]);
    }

    public function about(): Response
    {
        return Inertia::render('About', [
            'title' => $this->pageTitle('home.about'),
        ]);
    }

    public function contact(): Response
    {
        return Inertia::render('Contact', [
            'title' => $this->pageTitle('home.contact'),
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

    public function showTrip(Trip $trip): Response
    {
        return Inertia::render('Trip/Show', [
            'title' => $this->pageTitle($trip->name),
            'trip' => $trip->load(['featuredImage', 'images', 'countries', 'itineraries', 'itineraries.image']),
        ]);
    }

    public function showPrivacy(): Response
    {
        return Inertia::render('Privacy', [
            'title' => $this->pageTitle('home.privacy_statement'),
        ]);
    }

    public function showTerms(): Response
    {
        return Inertia::render('Terms', [
            'title' =>  $this->pageTitle('home.conditions'),
        ]);
    }
}
