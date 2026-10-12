<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ImageRelation;
use App\Enums\TestimonialRelationship;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\HasPageMetadata;
use App\Http\Requests\StoreTestimonialRequest;
use App\Http\Requests\UpdateTestimonialOrderRequest;
use App\Models\Testimonial;
use App\Models\Trip;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class TestimonialController extends Controller
{
    use HasPageMetadata;

    /**
     * Display all testimonials in their display order. Only the trip name goes along: the appended attributes of a
     * whole trip would each query its prices, images and destinations.
     */
    public function index(): Response
    {
        $testimonials = Testimonial::with(['trip:id,name', 'image'])->orderBy('sort_order')->orderBy('id')->get()
            ->map(fn (Testimonial $testimonial) => [
                'id' => $testimonial->id,
                'quote' => $testimonial->quote,
                'author_name' => $testimonial->author_name,
                'relationship' => $testimonial->relationship->label(),
                'trip_name' => $testimonial->trip?->name,
                'photo_url' => $testimonial->image?->public_url,
                'is_published' => $testimonial->is_published,
                'sort_order' => $testimonial->sort_order,
            ]);

        return Inertia::render('Admin/Testimonials/Index', [
            'testimonials' => $testimonials,
            'title' => $this->pageTitle('testimonial.title_index'),
        ]);
    }

    /**
     * Save the order of the testimonials after dragging them in the list.
     */
    public function updateOrder(UpdateTestimonialOrderRequest $request): JsonResponse
    {
        foreach ($request->validated('testimonials') as $testimonial) {
            Testimonial::whereKey($testimonial['id'])->update(['sort_order' => $testimonial['sort_order']]);
        }

        return response()->json(['success' => true]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Testimonials/Create', [
            ...$this->formOptions(),
            'title' => $this->pageTitle('testimonial.title_create'),
        ]);
    }

    /**
     * Store a new testimonial at the end of the order.
     */
    public function store(StoreTestimonialRequest $request): RedirectResponse
    {
        $testimonial = Testimonial::create([
            ...$request->safe()->except('photo'),
            'sort_order' => (Testimonial::max('sort_order') ?? 0) + 1,
        ]);

        $testimonial->syncImageFromRequest($request, 'photo', ImageRelation::Image);

        return redirect()->route('admin.testimonials.index')
            ->with('success', __('testimonial.created'));
    }

    public function edit(Testimonial $testimonial): Response
    {
        return Inertia::render('Admin/Testimonials/Edit', [
            'testimonial' => $testimonial->load('image'),
            ...$this->formOptions(),
            'title' => $this->pageTitle('testimonial.title_edit'),
        ]);
    }

    public function update(StoreTestimonialRequest $request, Testimonial $testimonial): RedirectResponse
    {
        $testimonial->update($request->safe()->except('photo'));

        $testimonial->syncImageFromRequest($request, 'photo', ImageRelation::Image);

        return redirect()->route('admin.testimonials.index')
            ->with('success', __('testimonial.updated'));
    }

    /**
     * Publish an unpublished testimonial or take a published one offline, from the list.
     */
    public function togglePublished(Testimonial $testimonial): RedirectResponse
    {
        $testimonial->update(['is_published' => ! $testimonial->is_published]);

        return back()->with('success', __($testimonial->is_published ? 'testimonial.published' : 'testimonial.unpublished'));
    }

    /**
     * Remove a testimonial along with its photo.
     */
    public function destroy(Testimonial $testimonial): RedirectResponse
    {
        $testimonial->delete();

        return redirect()->route('admin.testimonials.index')
            ->with('success', __('testimonial.deleted'));
    }

    /**
     * The options and limits the create and edit forms share.
     *
     * @return array{tripOptions: list<array{id: string, name: string}>, relationshipOptions: array<int, array<string, mixed>>, limits: array{quote: int, body: int, author_context: int}}
     */
    private function formOptions(): array
    {
        return [
            // Ids as strings, the value type of the select
            'tripOptions' => Trip::orderBy('name')->pluck('name', 'id')
                ->map(fn (string $name, int $id) => ['id' => (string) $id, 'name' => $name])
                ->values()
                ->all(),
            'relationshipOptions' => TestimonialRelationship::options(),
            'limits' => [
                'quote' => Testimonial::MAX_QUOTE_LENGTH,
                'body' => Testimonial::MAX_BODY_LENGTH,
                'author_context' => Testimonial::MAX_AUTHOR_CONTEXT_LENGTH,
            ],
        ];
    }
}
