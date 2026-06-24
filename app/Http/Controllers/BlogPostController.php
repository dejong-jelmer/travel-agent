<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\HasPageMetadata;
use App\Models\BlogPost;
use Inertia\Inertia;
use Inertia\Response;

class BlogPostController extends Controller
{
    use HasPageMetadata;

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
            'og_image' => $post->heroImage?->public_url,
        ];

        $jsonLd = array_merge(
            ['@context' => 'https://schema.org'],
            $post->toBlogPostingSchema(),
            [
                'author' => [
                    '@type' => 'Person',
                    'name' => config('blog.author_name'),
                    'url' => route('about'),
                ],
                'publisher' => $this->travelAgencySchema(),
            ],
        );
        $seo = $this->shareSeo(overrides: $overrides, jsonLd: $jsonLd);

        return Inertia::render('Blog/Show', [
            'post' => $post,
            'title' => $seo['title'],
            'seo' => $seo,
        ]);
    }
}
