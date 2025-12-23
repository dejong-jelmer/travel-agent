<?php

namespace App\Http\Controllers\Admin;

use App\DTO\DataTableConfigData;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\HasDataTableFilters;
use App\Http\Controllers\Traits\HasPageMetadata;
use App\Models\Country;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CountryController extends Controller
{
    use HasDataTableFilters,
        HasPageMetadata;

    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        $query = Country::query();

        // Apply DataTable filters
        $this->applyDataTableFilters($query, new DataTableConfigData(
            searchable: ['name'],
            sortable: ['id', 'name'],
            defaultSort: ['id', 'asc']
        ));

        return Inertia::render('Admin/Country/Index', [
            'countries' => $query->paginate()->withQueryString(),
            'totalCountries' => Country::count(),
            'filters' => $this->getCurrentFilters(['name', 'id']),
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
    public function destroy(Country $country): RedirectResponse
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
