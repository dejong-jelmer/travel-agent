<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\HasPageMetadata;
use App\Http\Requests\DataTableRequest;
use App\Models\Country;
use App\Services\DataTableService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CountryController extends Controller
{
    use HasPageMetadata;

    public function __construct(private DataTableService $dataTableService) {}

    /**
     * Display a listing of the resource.
     */
    public function index(DataTableRequest $request): Response
    {
        $query = Country::query();

        // Apply DataTable filters
        $this->dataTableService
            ->withValidatedData($request->validated())
            ->applySortFilters($query, Country::dataTableConfig());

        return Inertia::render('Admin/Country/Index', [
            'countries' => $query->paginate()->withQueryString(),
            'totalCountries' => Country::count(),
            'filters' => $this->dataTableService->getCurrentSortFilters(['name', 'id']),
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
