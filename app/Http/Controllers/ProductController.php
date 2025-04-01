<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Models\Country;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class ProductController extends Controller
{
    private string $appName;

    public function __construct()
    {
        $this->appName = env('APP_NAME');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        return Inertia::render('Admin/Products/Index', [
            'products' => Product::with(['countries', 'itineraries', 'featuredImage'])->get(),
            'title' => "Admin producten - {$this->appName}",
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        return Inertia::render('Admin/Products/Create', [
            'countries' => Country::all(),
            'title' => "Admin product aanmaken - {$this->appName}",
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductRequest $request): RedirectResponse
    {
        $product = new Product;

        $validatedFiles = $request->safe()->only(['featuredImage', 'images']);
        $validatedFields = $request->safe()->except(['featuredImage', 'images']);
        $countries = $request->safe()->countries ?? [];

        $product->fill($validatedFields);
        $product->save();
        $product->storeImages($validatedFiles['featuredImage'], 'featuredImage', true);
        $product->storeImages($validatedFiles['images'], 'images');

        if (count($countries)) {
            $product->countries()->sync($countries);
        }

        return redirect()->route('products.show', $product)->with('success', __('Product aangemaakt'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product): Response
    {
        return Inertia::render('Admin/Products/Show', [
            'product' => $product->load(['featuredImage', 'images', 'countries', 'itineraries']),
            'title' => "Admin product - {$this->appName}",
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product): Response
    {
        return Inertia::render('Admin/Products/Edit', [
            'product' => $product->load(['featuredImage', 'images', 'countries']),
            'countries' => Country::all(),
            'title' => "Admin product bewerken - {$this->appName}",
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreProductRequest $request, Product $product): RedirectResponse
    {
        $validatedFiles = $request->safe()->only(['featuredImage', 'images']);
        $validatedFields = $request->safe()->except(['featuredImage', 'images', 'countries']);
        $countries = $request->safe()->countries ?? [];

        $product->fill($validatedFields);
        $product->save();
        $product->storeImages($validatedFiles['featuredImage'], 'featuredImage', true);
        $product->storeImages($validatedFiles['images'], 'images');

        if (count($countries)) {
            $product->countries()->sync($countries);
        }

        return redirect()->route('products.show', $product)
            ->with('success', __('Product aangepast'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product): RedirectResponse
    {
        Product::destroy($product->id);
        return redirect()->route('products.index')
            ->with('success', __('Product verwijderd'));
    }
}
