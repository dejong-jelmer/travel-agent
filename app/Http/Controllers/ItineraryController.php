<?php

namespace App\Http\Controllers;

use App\Models\Itinerary;
use App\Models\Product;
use Illuminate\Http\Request;
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
        // Overview of a product itinerary
        return Inertia::render('Admin/Products/Itineraries/Index', [
            'product' => $product->load('itineraries'),
        ]);
    }

    public function updateOrder(Request $request, Product $product): HttpResponse
    {
        $validated = $request->validate([
            'itineraries' => 'required|array',
            'itineraries.*.id' => 'exists:itineraries,id',
            'itineraries.*.order' => "integer|between:0,{$product->itineraries()->count()}",
        ]);

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
    public function create(Product $product)
    {
        // Here we want to show the create a new itinerary block form for $product.
    }

    /**
     * Store a newly created itinerary block for the product.
     */
    public function store(Request $request, Product $product)
    {
        // Here we want to store the newly create itinerary block for the $product.
    }

    /**
     * Display a single the itinerary block.
     */
    public function show(Itinerary $itinerary)
    {
        // Here we want to show a single itinerary block.
        dd($itinerary);
    }

    /**
     * Show the form for editing an itinerary block for the product.
     */
    public function edit(Itinerary $itinerary): Response
    {
        // Here we want to show the form for editing an existing itinerary block.
        return Inertia::render('Admin/Itineraries/Edit', [
            'itinerary' => $itinerary,
        ]);
    }

    /**
     * Update an itinerary block in storage.
     */
    public function update(Request $request, Itinerary $itinerary)
    {
        // Here we want to update the an excisting itinerary block for the $product.
    }

    /**
     * Remove an itinerary block from storage.
     */
    public function destroy(Itinerary $itinerary)
    {
        // Here we want to delete the $itinerary.
    }
}
