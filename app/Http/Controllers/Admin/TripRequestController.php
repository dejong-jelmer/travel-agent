<?php

namespace App\Http\Controllers\Admin;

use App\Enums\TripRequest\Status;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\HasPageMetadata;
use App\Http\Requests\DataTableRequest;
use App\Models\TripRequest;
use App\Services\DataTableService;
use Inertia\Inertia;
use Inertia\Response;

class TripRequestController extends Controller
{
    use HasPageMetadata;

    public function __construct(private DataTableService $dataTableService) {}

    public function index(DataTableRequest $request): Response
    {
        $tripRequests = $this->dataTableService
            ->applyFilters(TripRequest::with(['trip']), $request, TripRequest::filters())
            ->paginate()
            ->withQueryString();

        return Inertia::render('Admin/Request/Index', [
            'tripRequests' => $tripRequests,
            'totalTripRequests' => TripRequest::count(),
            'filters' => $this->dataTableService->getSortFilters(TripRequest::filters()),
            'statusOptions' => Status::options(),
            'title' => $this->pageTitle('trip_request.title_index'),
        ]);
    }
}
