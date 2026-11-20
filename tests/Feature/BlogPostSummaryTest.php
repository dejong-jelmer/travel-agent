<?php

namespace Tests\Feature;

use App\Enums\BlogPost\Status;
use App\Models\BlogPost;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia;
use Tests\TestCase;

class BlogPostSummaryTest extends TestCase
{
    use RefreshDatabase;

    private const BODY = '<p>Eerste alinea.</p><p>Tweede alinea.</p><h2>Dag 1</h2><p>Verder.</p><h2>Dag 2</h2>';

    // Points

    public function test_summary_has_a_point_per_line_without_empty_lines_or_typed_bullets(): void
    {
        $post = new BlogPost(['summary' => "- Met de nachttrein naar Bordeaux\r\n\n  • Drie dagen in de Vézère-vallei  \n* Lascaux IV"]);

        $this->assertSame([
            'Met de nachttrein naar Bordeaux',
            'Drie dagen in de Vézère-vallei',
            'Lascaux IV',
        ], $post->summaryPoints());
    }

    public function test_empty_summary_has_no_points(): void
    {
        $this->assertSame([], (new BlogPost(['summary' => null]))->summaryPoints());
        $this->assertSame([], (new BlogPost(['summary' => " \n \n"]))->summaryPoints());
    }

    // Where the summary goes

    public function test_body_is_split_before_its_first_h2(): void
    {
        $this->assertSame([
            'intro' => '<p>Eerste alinea.</p><p>Tweede alinea.</p>',
            'rest' => '<h2>Dag 1</h2><p>Verder.</p><h2>Dag 2</h2>',
        ], (new BlogPost(['body' => self::BODY]))->bodyAroundSummary());
    }

    public function test_body_without_h2_is_split_after_its_first_paragraph(): void
    {
        $this->assertSame([
            'intro' => '<p>Eerste alinea.</p>',
            'rest' => '<p>Tweede alinea.</p><h3>Een kop</h3>',
        ], (new BlogPost(['body' => '<p>Eerste alinea.</p><p>Tweede alinea.</p><h3>Een kop</h3>']))->bodyAroundSummary());
    }

    public function test_body_without_paragraphs_is_all_introduction(): void
    {
        $this->assertSame(
            ['intro' => '<ul><li>Een punt</li></ul>', 'rest' => ''],
            (new BlogPost(['body' => '<ul><li>Een punt</li></ul>']))->bodyAroundSummary(),
        );
    }

    // Story page

    public function test_story_page_shares_the_summary_and_the_body_around_it(): void
    {
        $post = BlogPost::factory()->published()->create([
            'body' => self::BODY,
            'summary' => "Met de nachttrein\nDrie dagen wandelen",
        ]);

        $this->get(route('blog.show', $post))
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->component('Blog/Show')
                ->where('summary', ['Met de nachttrein', 'Drie dagen wandelen'])
                ->where('body.intro', '<p>Eerste alinea.</p><p>Tweede alinea.</p>')
                ->where('body.rest', '<h2>Dag 1</h2><p>Verder.</p><h2>Dag 2</h2>')
                // Neither goes along twice
                ->missing('post.body')
                ->missing('post.summary')
                ->etc()
            );
    }

    public function test_story_page_without_summary_keeps_the_body_whole(): void
    {
        $post = BlogPost::factory()->published()->create(['body' => self::BODY, 'summary' => null]);

        $this->get(route('blog.show', $post))
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->where('summary', [])
                ->where('body.intro', self::BODY)
                ->where('body.rest', '')
                ->etc()
            );
    }

    // Admin

    public function test_admin_can_save_a_summary(): void
    {
        $this->actingAs(User::factory()->create())
            ->post(route('admin.posts.store'), $this->storyData(['summary' => "Punt een\nPunt twee"]))
            ->assertRedirect(route('admin.posts.index'));

        $this->assertSame("Punt een\nPunt twee", BlogPost::firstOrFail()->summary);
    }

    public function test_a_summary_longer_than_1000_characters_is_refused(): void
    {
        $this->actingAs(User::factory()->create())
            ->post(route('admin.posts.store'), $this->storyData(['summary' => str_repeat('a', 1001)]))
            ->assertSessionHasErrors('summary');
    }

    private function storyData(array $overrides = []): array
    {
        return array_merge([
            'title' => 'Langs de Vézère',
            'body' => self::BODY,
            'status' => Status::Draft->value,
        ], $overrides);
    }
}
