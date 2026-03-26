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

        return Inertia::render('Blog/Index', [
            'posts' => $posts,
            'title' => $this->pageTitle('blog.title_index'),
            'seo' => $this->pageSeo('blog.blog_seo'),
        ]);
    }

    public function show(string $slug): Response
    {
        $post = BlogPost::published()
            ->where('slug', $slug)
            ->with('heroImage')
            ->firstOrFail();

        return Inertia::render('Blog/Show', [
            'post' => $post,
            'title' => $this->pageTitle('blog.title_show'),
            'seo' => $this->pageSeo('blog.blog_seo', [
                'title' => ($post->meta_title ?: $post->title).' | '.config('app.name'),
                'description' => $post->meta_description ?: $post->excerpt,
                'og_image' => $post->heroImage?->public_url,
            ]),
        ]);
    }
}
