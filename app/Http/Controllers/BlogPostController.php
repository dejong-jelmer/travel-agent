<?php

namespace App\Http\Controllers;

use App\DTO\BreadcrumbTrail;
use App\Http\Controllers\Traits\HasPageMetadata;
use App\Models\BlogPost;
use App\Services\OgImageService;
use Inertia\Inertia;
use Inertia\Response;

class BlogPostController extends Controller
{
    use HasPageMetadata;

    public function __construct(private readonly OgImageService $ogImages) {}

    public function index(): Response
    {
        $posts = BlogPost::published()
            ->with('heroImage')
            ->latest('published_at')
            ->paginate(12);

        $seo = $this->shareSeo('seo.blog');

        return Inertia::render('Blog/Index', [
            'posts' => $posts,
            'title' => $seo['title'],
            'seo' => $seo,
        ]);
    }

    public function show(string $slug): Response
    {
        $post = BlogPost::published()
            ->where('slug', $slug)
            ->with('heroImage')
            ->firstOrFail();

        $overrides = [
            'title' => ($post->meta_title ?: $post->title).' | '.config('app.name'),
            'description' => $post->meta_description ?: $post->excerpt,
            'og_type' => 'article',
            'article_published_time' => $post->published_at?->toIso8601String(),
            'article_modified_time' => $post->updated_at?->toIso8601String(),
            ...$this->ogImageSeo($this->ogImages->forImage($post->heroImage, $post->title)),
        ];

        $jsonLd = array_merge(
            $post->toBlogPostingSchema(),
            [
                'author' => [
                    '@type' => 'Person',
                    'name' => config('blog.author_name'),
                    'url' => route('about'),
                ],
                'publisher' => [
                    '@type' => 'Organization',
                    'name' => config('app.name'),
                    'url' => config('app.url'),
                    'logo' => [
                        '@type' => 'ImageObject',
                        'url' => asset(config('seo.logo')),
                    ],
                ],
            ],
        );

        $breadcrumbs = BreadcrumbTrail::home()
            ->add(__('nav.blog'), route('blog.index'))
            ->add($post->title);

        $seo = $this->shareSeo(overrides: $overrides, jsonLd: $jsonLd, breadcrumbs: $breadcrumbs);

        return Inertia::render('Blog/Show', [
            'post' => $post,
            'author' => [
                'name' => config('blog.author_name'),
                'bio' => config('blog.author_bio'),
            ],
            // As on the trips page: the trip cards have no use for the gallery paths, which would fetch every gallery
            'trips' => $post->trips()->with(['destinations', 'heroImage'])->withStartingPrice()->withTravelMode()->published()
                ->orderBy('name')->get()->append(['travel_mode', 'type_display', 'format_display'])
                ->makeHidden(['image_paths', 'visibleDepartures', 'pivot']),
            'title' => $seo['title'],
            'seo' => $seo,
        ]);
    }
}
