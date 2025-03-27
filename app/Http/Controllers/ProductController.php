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

    private string $featuredImagePath;

    private string $imagesPath;

    public function __construct()
    {
        $this->appName = env('APP_NAME');
        $this->featuredImagePath = config('product.featured-image-path');
        $this->imagesPath = config('product.images-path');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        return Inertia::render('Admin/Products/Index', [
            'products' => Product::with(['countries', 'itineraries'])->get(),
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

        $validatedFiles = $request->safe()->only(['image', 'images']);
        $validatedFields = $request->safe()->except(['image', 'images']);
        $countries = $request->safe()->countries ?? [];

        if (isset($validatedFiles['image'])) {
            $image = $validatedFiles['image'];
            $imagePath = $image->storeAs($this->featuredImagePath, $image->getClientOriginalName(), 'public');
            $product->image = $imagePath;
        }

        $product->fill($validatedFields);
        $product->save();
        if (count($countries)) {
            $product->countries()->sync($countries);
        }

        if (isset($validatedFiles['images']) && is_array($validatedFiles['images'])) {
            $imagePaths = [];
            foreach ($validatedFiles['images'] as $image) {
                $path = $image->storeAs($this->imagesPath, $image->getClientOriginalName(), 'public');
                $imagePaths[] = ['path' => $path];
            }
            $product->images()->createMany($imagePaths);
        }

        return redirect()->route('products.show', $product)->with('success', __('Product aangemaakt'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product): Response
    {
        return Inertia::render('Admin/Products/Show', [
            'product' => $product->load(['images', 'countries', 'itineraries']),
            'title' => "Admin product - {$this->appName}",
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        return Inertia::render('Admin/Products/Edit', [
            'product' => $product->load(['images', 'countries']),
            'countries' => Country::all(),
            'title' => "Admin product bewerken - {$this->appName}",
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreProductRequest $request, Product $product): RedirectResponse
    {
        $validatedFiles = $request->safe()->only(['image', 'images']);
        $validatedFields = $request->safe()->except(['image', 'images', 'countries']);
        $countries = $request->safe()->countries ?? [];

        if (isset($validatedFiles['image'])) {
            if ($product->hasImage()) {
                $product->deleteImage();
            }
            $image = $validatedFiles['image'];
            $imagePath = $image->storeAs($this->featuredImagePath, $image->getClientOriginalName(), 'public');
            $product->image = $imagePath;
        }
        if (isset($validatedFiles['images']) && is_array($validatedFiles['images'])) {
            $imagePaths = [];
            $product->deleteImages();

            foreach ($validatedFiles['images'] as $image) {
                $path = $image->storeAs($this->imagesPath, $image->getClientOriginalName(), 'public');
                $imagePaths[] = ['path' => $path];
            }

            $product->images()->createMany($imagePaths);
        }

        $product->fill($validatedFields);
        $product->save();
        if (count($countries)) {
            $product->countries()->sync($countries);
        }

        return redirect()->route('products.show', $product)->with('success', __('Product aangepast'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        //
    }
}
