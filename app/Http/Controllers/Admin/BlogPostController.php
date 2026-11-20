<?php

namespace App\Http\Controllers\Admin;

use App\Enums\BlogPost\Status;
use App\Enums\ImageRelation;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\HasPageMetadata;
use App\Http\Requests\CreateBlogPostRequest;
use App\Http\Requests\DataTableRequest;
use App\Http\Requests\UpdateBlogPostRequest;
use App\Models\BlogPost;
use App\Models\Trip;
use App\Services\DataTableService;
use App\Services\SlugService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class BlogPostController extends Controller
{
    use HasPageMetadata;

    public function __construct(private DataTableService $dataTableService) {}

    public function index(DataTableRequest $request): Response
    {
        $posts = $this->dataTableService
            ->applyFilters(BlogPost::with('heroImage'), $request)
            ->paginate()
            ->withQueryString();

        return Inertia::render('Admin/BlogPosts/Index', [
            'posts' => $posts,
            'totalPosts' => BlogPost::count(),
            'filters' => $this->dataTableService->getSortFilters([]),
            'title' => $this->pageTitle('blog.posts.title_index'),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/BlogPosts/Create', [
            'statusOptions' => Status::options(),
            'tripOptions' => $this->tripOptions(),
            'title' => $this->pageTitle('blog.posts.title_create'),
        ]);
    }

    public function store(CreateBlogPostRequest $request): RedirectResponse
    {
        $validated = $request->safe()->except(['featured_image', 'featured_image_alt', 'trips']);
        $status = Status::from($validated['status']);

        $slug = SlugService::generateUniqueFor(BlogPost::class, $validated['title']);

        // The story and its trips are saved together; the images follow outside the transaction, as syncImages
        // uploads files and runs its own
        $post = DB::transaction(function () use ($validated, $slug, $status, $request) {
            $post = BlogPost::create(array_merge($validated, [
                'slug' => $slug,
                'published_at' => $status === Status::Published ? now() : null,
            ]));
            $post->trips()->sync($request->safe()->trips ?? []);

            return $post;
        });

        if ($request->hasFile('featured_image')) {
            $post->syncImages($request->file('featured_image'), ImageRelation::HeroImage, true);
        }
        $post->setImageAlt(ImageRelation::HeroImage, $request->safe()->input('featured_image_alt'));

        return redirect()->route('admin.posts.index')
            ->with('success', __('blog.posts.created'));
    }

    public function show(BlogPost $post): Response
    {
        return Inertia::render('Admin/BlogPosts/Show', [
            'blogPost' => $post->load('heroImage'),
            'title' => $this->pageTitle('blog.posts.title_show'),
        ]);
    }

    public function edit(BlogPost $post): Response
    {
        return Inertia::render('Admin/BlogPosts/Edit', [
            'blogPost' => $post->load('heroImage'),
            // The ids of the related trips, as strings like the option ids
            'tripIds' => $post->trips()->pluck('trips.id')->map(fn (int $id) => (string) $id)->all(),
            'statusOptions' => Status::options(),
            'tripOptions' => $this->tripOptions(),
            'title' => $this->pageTitle('blog.posts.title_edit'),
        ]);
    }

    public function update(UpdateBlogPostRequest $request, BlogPost $post): RedirectResponse
    {
        $validated = $request->safe()->except(['featured_image', 'featured_image_alt', 'slug', 'trips']);
        $status = Status::from($validated['status']);
        $slug = SlugService::generateUniqueFor(BlogPost::class, $request->input('title'), $post->id);

        $post->fill(array_merge($validated, [
            'slug' => $slug,
            'published_at' => match (true) {
                $status === Status::Published && ! $post->published_at => now(),
                default => $post->published_at,
            },
        ]));

        // The story and its trips are saved together; the images follow outside the transaction, as in store()
        DB::transaction(function () use ($post, $request) {
            $post->save();
            // The form always sends its trips, but an empty list goes missing in form data: no trips means none
            $post->trips()->sync($request->safe()->trips ?? []);
        });

        if ($request->hasFile('featured_image')) {
            $post->syncImages($request->file('featured_image'), ImageRelation::HeroImage, true);
        } elseif ($request->has('featured_image') && is_null($request->input('featured_image'))) {
            $post->heroImage?->delete();
        } elseif ($request->filled('featured_image')) {
            $post->syncImages($request->input('featured_image'), ImageRelation::HeroImage, true);
        }
        $post->setImageAlt(ImageRelation::HeroImage, $request->safe()->input('featured_image_alt'));

        return redirect()->route('admin.posts.show', $post)
            ->with('success', __('blog.posts.updated'));
    }

    public function destroy(BlogPost $post): RedirectResponse
    {
        $post->delete();

        return redirect()->route('admin.posts.index')
            ->with('success', __('blog.posts.deleted'));
    }

    /**
     * The trips a story can be related to, by name, ids as strings (a select returns its values as strings).
     *
     * @return list<array{id: string, name: string}>
     */
    private function tripOptions(): array
    {
        return Trip::orderBy('name')->pluck('name', 'id')
            ->map(fn (string $name, int $id) => ['id' => (string) $id, 'name' => $name])
            ->values()
            ->all();
    }
}
