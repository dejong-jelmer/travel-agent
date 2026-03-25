<?php

namespace Tests\Feature;

use App\Enums\BlogPost\Status;
use App\Models\BlogPost;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Testing\AssertableInertia;
use Tests\TestCase;

class BlogPostTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create();
        $this->actingAs($this->admin);

        Storage::fake(config('images.disk'));
        Storage::makeDirectory(config('images.directory'));
    }

    // Admin Index

    public function test_admin_can_view_blog_post_index(): void
    {
        BlogPost::factory()->count(3)->create();

        $response = $this->get(route('admin.posts.index'));

        $response->assertInertia(
            fn (AssertableInertia $page) => $page
                ->component('Admin/BlogPosts/Index')
                ->has('posts.data', 3)
                ->has('posts.links')
        );

        $response->assertStatus(200);
    }

    // Admin Create

    public function test_admin_can_view_blog_post_create(): void
    {
        $response = $this->get(route('admin.posts.create'));

        $response->assertInertia(
            fn (AssertableInertia $page) => $page
                ->component('Admin/BlogPosts/Create')
                ->has('statusOptions')
        );

        $response->assertStatus(200);
    }

    // Admin Store

    public function test_admin_can_create_a_new_blog_post(): void
    {
        $postData = $this->validBlogPostData();

        $response = $this->post(route('admin.posts.store'), $postData);

        $post = BlogPost::firstOrFail();

        $response->assertRedirect(route('admin.posts.index'));
        $this->assertEquals($postData['title'], $post->title);
        $this->assertEquals(Str::slug($postData['title']), $post->slug);
        $this->assertEquals($postData['body'], $post->body);
        $this->assertEquals($postData['excerpt'], $post->excerpt);
        $this->assertEquals(Status::Draft, $post->status);
        $this->assertNull($post->published_at);
        $this->assertEquals($postData['meta_title'], $post->meta_title);
        $this->assertEquals($postData['meta_description'], $post->meta_description);
    }

    public function test_admin_can_create_a_published_blog_post(): void
    {
        $postData = $this->validBlogPostData([
            'status' => Status::Published->value,
        ]);

        $response = $this->post(route('admin.posts.store'), $postData);

        $post = BlogPost::firstOrFail();

        $response->assertRedirect(route('admin.posts.index'));
        $this->assertEquals(Status::Published, $post->status);
        $this->assertNotNull($post->published_at);
    }

    public function test_admin_can_create_a_blog_post_with_featured_image(): void
    {
        $postData = $this->validBlogPostData([
            'featured_image' => UploadedFile::fake()->image('hero.jpg'),
        ]);

        $response = $this->post(route('admin.posts.store'), $postData);

        $post = BlogPost::firstOrFail();

        $response->assertRedirect(route('admin.posts.index'));

        $heroImage = $post->heroImage;
        $this->assertNotNull($heroImage);
        $this->assertEquals('hero.jpg', $heroImage->original_name);
        $this->assertEquals('image/jpeg', $heroImage->mime_type);
        $this->assertTrue($heroImage->is_primary);
        Storage::disk(config('images.disk'))->assertExists(config('images.directory')."/{$heroImage->path}");
    }

    // Admin Show

    public function test_admin_can_view_blog_post_show(): void
    {
        $post = BlogPost::factory()->create();

        $response = $this->get(route('admin.posts.show', $post));

        $response->assertInertia(
            fn (AssertableInertia $page) => $page
                ->component('Admin/BlogPosts/Show')
                ->has('blogPost')
                ->where('blogPost.id', $post->id)
                ->etc()
        );

        $response->assertStatus(200);
    }

    // Admin Edit

    public function test_admin_can_view_blog_post_edit(): void
    {
        $post = BlogPost::factory()->create();

        $response = $this->get(route('admin.posts.edit', $post));

        $response->assertInertia(
            fn (AssertableInertia $page) => $page
                ->component('Admin/BlogPosts/Edit')
                ->has('blogPost')
                ->has('statusOptions')
                ->where('blogPost.id', $post->id)
                ->etc()
        );

        $response->assertStatus(200);
    }

    // Admin Update

    public function test_admin_can_update_a_blog_post(): void
    {
        $post = BlogPost::factory()->create();

        $updateData = $this->validBlogPostData([
            'title' => 'Updated Title',
            'body' => 'Updated body content.',
            'slug' => 'updated-title',
            'status' => Status::Published->value,
        ]);

        $response = $this->put(route('admin.posts.update', $post), $updateData);

        $post->refresh();
        $response->assertRedirect(route('admin.posts.show', $post));

        $this->assertEquals('Updated Title', $post->title);
        $this->assertEquals('Updated body content.', $post->body);
        $this->assertEquals('updated-title', $post->slug);
        $this->assertEquals(Status::Published, $post->status);
        $this->assertNotNull($post->published_at);
    }

    public function test_admin_can_update_blog_post_with_new_image(): void
    {
        $post = BlogPost::factory()->create();

        $updateData = $this->validBlogPostData([
            'slug' => $post->slug,
            'featured_image' => UploadedFile::fake()->image('new-hero.jpg'),
        ]);

        $response = $this->post(route('admin.posts.update', $post), array_merge($updateData, ['_method' => 'PUT']));

        $post->refresh();
        $response->assertRedirect(route('admin.posts.show', $post));

        $heroImage = $post->heroImage;
        $this->assertNotNull($heroImage);
        $this->assertEquals('new-hero.jpg', $heroImage->original_name);
        Storage::disk(config('images.disk'))->assertExists(config('images.directory')."/{$heroImage->path}");
    }

    // Admin Destroy

    public function test_admin_can_delete_a_blog_post(): void
    {
        $post = BlogPost::factory()->create();

        $response = $this->delete(route('admin.posts.destroy', $post));

        $response->assertRedirect(route('admin.posts.index'));
        $this->assertDatabaseMissing('blog_posts', ['id' => $post->id]);
    }

    // Validation

    public function test_store_requires_title_and_body(): void
    {
        $response = $this->post(route('admin.posts.store'), [
            'status' => Status::Draft->value,
        ]);

        $response->assertSessionHasErrors(['title', 'body']);
    }

    public function test_store_requires_valid_status(): void
    {
        $postData = $this->validBlogPostData([
            'status' => 'invalid',
        ]);

        $response = $this->post(route('admin.posts.store'), $postData);

        $response->assertSessionHasErrors(['status']);
    }

    public function test_store_generates_unique_slug_for_duplicate_titles(): void
    {
        BlogPost::factory()->create(['slug' => 'my-title']);

        $postData = $this->validBlogPostData(['title' => 'My Title']);

        $response = $this->post(route('admin.posts.store'), $postData);

        $response->assertRedirect(route('admin.posts.index'));

        $newPost = BlogPost::where('slug', '!=', 'my-title')->firstOrFail();
        $this->assertStringStartsWith('my-title-', $newPost->slug);
    }

    // Frontend Index

    public function test_guest_can_view_blog_index(): void
    {
        BlogPost::factory()->published()->count(2)->create();
        BlogPost::factory()->draft()->create();

        $response = $this->get(route('blog.index'));

        $response->assertInertia(
            fn (AssertableInertia $page) => $page
                ->component('Blog/Index')
                ->has('posts.data', 2)
        );

        $response->assertStatus(200);
    }

    public function test_blog_index_excludes_future_published_posts(): void
    {
        BlogPost::factory()->published()->create();
        BlogPost::factory()->scheduled()->create();

        $response = $this->get(route('blog.index'));

        $response->assertInertia(
            fn (AssertableInertia $page) => $page
                ->component('Blog/Index')
                ->has('posts.data', 1)
        );
    }

    // Frontend Show

    public function test_guest_can_view_a_blog_post(): void
    {
        $post = BlogPost::factory()->published()->create();

        $response = $this->get(route('blog.show', $post));

        $response->assertInertia(
            fn (AssertableInertia $page) => $page
                ->component('Blog/Show')
                ->has('post')
                ->where('post.slug', $post->slug)
                ->etc()
        );

        $response->assertStatus(200);
    }

    // Helper Methods

    private function validBlogPostData(array $overrides = []): array
    {
        return array_merge([
            'title' => fake()->sentence(),
            'body' => fake()->paragraphs(3, true),
            'excerpt' => fake()->text(200),
            'status' => Status::Draft->value,
            'meta_title' => fake()->text(55),
            'meta_description' => fake()->text(155),
        ], $overrides);
    }
}
