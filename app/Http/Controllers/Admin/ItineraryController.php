<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Meals;
use App\Enums\Transport;
use App\Http\Requests\StoreItineraryRequest;
use App\Http\Requests\UpdateItineraryOrderRequest;
use App\Http\Requests\UpdateItineraryRequest;
use App\Models\Itinerary;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\UploadedFile;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

class ItineraryController extends Controller
{
    /**
     * Display a listing of a product's itinerary.
     */
    public function index(Product $product): Response
    {
        return Inertia::render('Admin/Products/Itineraries/Index', [
            'product' => $product->load('itineraries.image'),
        ]);
    }

    public function updateOrder(UpdateItineraryOrderRequest $request, Product $product): HttpResponse
    {
        $validated = $request->safe();
        foreach ($validated['itineraries'] as $itinerary) {
            $product->itineraries()->where('id', $itinerary['id'])->update(['order' => $itinerary['order']]);
        }

        return response()->json([
            'success' => true,
        ], 200);
    }

    /**
     * Show the form for creating a new itinerary block for the product.
     */
    public function create(Product $product): Response
    {
        return Inertia::render('Admin/Products/Itineraries/Create', [
            'product' => $product,
            'meals' => Meals::options(),
            'transport' => Transport::options(),
        ]);
    }

    /**
     * Store a newly created itinerary block for the product.
     */
    public function store(StoreItineraryRequest $request, Product $product): RedirectResponse
    {
        $itinerary = new Itinerary;
        $validatedFields = $request->safe()->except('image');
        $validatedImage = $request->safe()->only('image');

        $itinerary->fill($validatedFields);
        $itinerary->product()->associate($product);
        $itinerary->order = $product->itineraries->count() + 1;
        $itinerary->save();
        $itinerary->storeImages($validatedImage['image'], 'image');

        return redirect()
            ->route('admin.products.itineraries.index', $itinerary->product)
            ->with('success', __('Aanmaken van het reisplan is gelukt!'));
    }

    /**
     * Show the form for editing an itinerary block for the product.
     */
    public function edit(Itinerary $itinerary): Response
    {
        return Inertia::render('Admin/Products/Itineraries/Edit', [
            'itinerary' => $itinerary->load('image'),
            'meals' => Meals::options(),
            'transport' => Transport::options(),
        ]);
    }

    /**
     * Update an itinerary block in storage.
     */
    public function update(UpdateItineraryRequest $request, Itinerary $itinerary): RedirectResponse
    {
        $validatedFields = $request->safe()->except('image');
        $validatedImage = $request->safe()->only('image');

        $itinerary->update($validatedFields);

        // Sync image (handles both existing path and new upload)
        if (isset($validatedImage['image'])) {
            $itinerary->syncImages($validatedImage['image'], 'image');
        }

        return redirect()
            ->route('admin.products.itineraries.index', $itinerary->product)
            ->with('success', 'Aanpassen van het reisplan is gelukt!.');
    }

    /**
     * Remove an itinerary block from storage.
     */
    public function destroy(Itinerary $itinerary): RedirectResponse
    {
        $product = $itinerary->product;
        $itineraryTitle = $itinerary->title;
        Itinerary::destroy($itinerary->id);

        return redirect()
            ->route('admin.products.itineraries.index', $product)
            ->with('success', "Reisplan \"{$itineraryTitle}\" is verwijderd!.");
    }
}
