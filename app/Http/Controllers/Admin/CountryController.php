<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Traits\HasPageTitle;
use App\Models\Country;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;



class CountryController extends Controller
{
    use HasPageTitle;

    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        return Inertia::render('Admin/Country/Index', [
            'countries' => Country::paginate(),
            'title' => $this->pageTitle('country.title_index'),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        return Inertia::render('Admin/Country/Create', [
            'title' => $this->pageTitle('country.title_create'),
        ]);
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

        return redirect()->route('admin.countries.index')->with('success', __('country.created'));

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Country $country)
    {
        $trips = $country->trips->count();
        $feedback = [
            'status' => 'error',
            'message' => trans_choice('country.delete_failed', $trips, ['trips' => $trips]),
        ];

        if (! $trips) {
            Country::destroy($country->id);
            $feedback['status'] = 'success';
            $feedback['message'] = __('country.deleted');
        }

        return redirect()->route('admin.countries.index')->with($feedback['status'], $feedback['message']);
    }
}
