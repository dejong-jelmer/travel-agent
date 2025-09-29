<?php

namespace App\Http\Controllers;

use App\DTO\ContactFormData;
use App\Http\Requests\ContactRequest;
use App\Mail\ContactMail;
use App\Models\Product;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

class HomeController extends Controller
{
    private string $appName;

    public function __construct()
    {
        $this->appName = config('app.name');
    }

    public function home(): Response
    {
        return Inertia::render('Home', [
            'products' => Product::with(['countries', 'featuredImage'])->active()->featured()->get(),
        ]);
    }

    public function about(): Response
    {
        return Inertia::render('About', [
            'title' => "Over mij - {$this->appName}",
        ]);
    }

    public function contact(): Response
    {
        return Inertia::render('Contact', [
            'title' => "Contact - {$this->appName}",
        ]);
    }

    public function submitContact(ContactRequest $request): HttpResponse
    {
        $validated = $request->safe();
        $address = config('contact.mail');

        $contact = new ContactFormData(
            name: $validated['name'],
            email: $validated['email'],
            text: $validated['text'],
            phone: $validated['phone'],
        );

        try {
            Mail::to($address)->send(
                new ContactMail($contact)
            );
        } catch (\Throwable $e) {
            Log::error('Mail sending failed: '.$e->getMessage());
            Log::error('Stack trace: '.$e->getTraceAsString());
        }

        return response()->json([
            'success' => true,
        ], 200);
    }

    public function showTrip(Product $trip): Response
    {
        return Inertia::render('Trips/Show', [
            'title' => "{$this->appName} - {$trip->name}",
            'trip' => $trip->load(['featuredImage', 'images', 'countries', 'itineraries', 'itineraries.image']),
        ]);
    }

    public function showPrivacy(): Response
    {
        return Inertia::render('Privacy', [
            'title' => "{$this->appName} - Privacyverklaring",
        ]);
    }

    public function showTerms(): Response
    {
        return Inertia::render('Terms', [
            'title' => "{$this->appName} - Algemene Voorwaarden",
        ]);
    }
}
