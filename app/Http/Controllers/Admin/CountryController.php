<?php

namespace App\Http\Controllers\Admin;

use App\Models\Country;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;


class CountryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        return Inertia::render('Admin/Countries/Index', [
            'countries' => Country::paginate(15),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Admin/Countries/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|unique:countries|max:255',
        ]);
        Country::create(['name' => $validated['name']]);

        return redirect()->route('admin.countries.index')->with('success', __('Land aangemaakt'));

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Country $country)
    {
        $products = $country->products->count();
        $feedback = [
            'status' => 'error',
            'message' => "Er zijn nog {$products} producten gekoppeld aan dit land!",
        ];

        if (! $products) {
            Country::destroy($country->id);
            $feedback['status'] = 'success';
            $feedback['message'] = 'Land verwijderd';
        }

        return redirect()->route('admin.countries.index')->with($feedback['status'], __($feedback['message']));
    }
}
