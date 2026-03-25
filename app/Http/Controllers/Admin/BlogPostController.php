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
use App\Services\DataTableService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
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
            'title' => $this->pageTitle('blog.posts.title_create'),
        ]);
    }

    public function store(CreateBlogPostRequest $request): RedirectResponse
    {
        $validated = $request->safe()->except(['featured_image']);
        $status = Status::from($validated['status']);

        $slug = $this->generateUniqueSlug($request->title);

        $post = BlogPost::create(array_merge($validated, [
            'slug' => $slug,
            'published_at' => $status === Status::Published ? now() : null,
        ]));

        if ($request->hasFile('featured_image')) {
            $post->syncImages($request->file('featured_image'), ImageRelation::HeroImage, true);
        }

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
            'statusOptions' => Status::options(),
            'title' => $this->pageTitle('blog.posts.title_edit'),
        ]);
    }

    public function update(UpdateBlogPostRequest $request, BlogPost $post): RedirectResponse
    {
        $validated = $request->safe()->except(['featured_image', 'slug']);
        $status = Status::from($request->input('status'));
        $slug = $this->generateUniqueSlug($request->input('title'), $post->id);

        $post->fill(array_merge($validated, [
            'slug' => $slug,
            'published_at' => match (true) {
                $status === Status::Published && ! $post->published_at => now(),
                $status !== Status::Published => null,
                default => $post->published_at,
            },
        ]));
        $post->save();

        if ($request->has('featured_image')) {
            $post->syncImages(
                $request->hasFile('featured_image') ? $request->file('featured_image') : $request->input('featured_image'),
                ImageRelation::HeroImage,
                true
            );
        }

        return redirect()->route('admin.posts.show', $post)
            ->with('success', __('blog.posts.updated'));
    }

    public function destroy(BlogPost $post): RedirectResponse
    {
        $post->delete();

        return redirect()->route('admin.posts.index')
            ->with('success', __('blog.posts.deleted'));
    }

    private function generateUniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $slug = Str::slug($title);
        $originalSlug = $slug;
        $counter = 1;

        while (BlogPost::where('slug', $slug)->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))->exists()) {
            $slug = "{$originalSlug}-{$counter}";
            $counter++;
        }

        return $slug;
    }
}
