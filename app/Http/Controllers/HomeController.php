<?php

namespace App\Http\Controllers;

use App\DTO\ContactFromData;
use App\Http\Requests\ContactRequest;
use App\Mail\ContactMail;
use App\Models\Product;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\Response as HttpResponse;
use Illuminate\Support\Facades\Log;
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

        $contact = new ContactFromData(
            name: $validated['name'],
            email: $validated['email'],
            text: $validated['text'],
            telephone: $validated['telephone'],
        );

        try {
            Mail::to($address)->send(
                new ContactMail($contact, 'test')
            );

            if (Mail::failures()) {
                Log::error('Mail failures:', Mail::failures());
            }
        } catch (\Exception $e) {
            Log::error('Mail sending failed: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
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

     public function bookTrip(Product $trip): Response
    {
        return Inertia::render('Book', [
            'title' => "{$this->appName} - Boek {$trip->name}",
            'trip' => $trip->load(['featuredImage', 'images', 'countries', 'itineraries', 'itineraries.image']),
        ]);
    }
}
