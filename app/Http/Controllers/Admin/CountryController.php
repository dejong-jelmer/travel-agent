<?php

namespace App\Http\Controllers\Admin;

use App\Models\Country;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CountryController extends Controller
{
    private string $appName;

    public function __construct()
    {
        $this->appName = config('app.name');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        return Inertia::render('Admin/Country/Index', [
            'countries' => Country::paginate(),
            'title' => __('country.title_index').' - '.$this->appName,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        return Inertia::render('Admin/Country/Create', [
            'title' => __('country.title_create').' - '.$this->appName,
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
            'message' => trans_choice('country.delete_failed', $trips, ['trips' => $trips, 'trips' => $trips]),
        ];

        if (! $trips) {
            Country::destroy($country->id);
            $feedback['status'] = 'success';
            $feedback['message'] = __('country.deleted');
        }

        return redirect()->route('admin.countries.index')->with($feedback['status'], $feedback['message']);
    }
}
