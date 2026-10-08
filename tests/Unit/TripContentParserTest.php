<?php

namespace Tests\Unit;

use App\Services\TripContentParser;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class TripContentParserTest extends TestCase
{
    private TripContentParser $parser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->parser = new TripContentParser;
    }

    public function test_every_h2_starts_a_section_with_the_content_up_to_the_next_h2(): void
    {
        $sections = $this->parser->sections(
            '<h2>Met de trein naar de Vézère-vallei</h2><p>Je reist ’s ochtends.</p><ul><li>Lascaux</li></ul>'
            .'<h2>Waar de stad samenkomt</h2><p>De <strong>Piazza</strong>.</p>'
        );

        $this->assertSame([
            [
                'key' => 'met-de-trein-naar-de-vezere-vallei',
                'title' => 'Met de trein naar de Vézère-vallei',
                'html' => '<p>Je reist ’s ochtends.</p><ul><li>Lascaux</li></ul>',
            ],
            [
                'key' => 'waar-de-stad-samenkomt',
                'title' => 'Waar de stad samenkomt',
                'html' => '<p>De <strong>Piazza</strong>.</p>',
            ],
        ], $sections);
    }

    public function test_content_before_the_first_h2_is_merged_into_the_first_section(): void
    {
        $sections = $this->parser->sections('<p>Vooraf.</p><h2>De reis</h2><p>Onderweg.</p><h2>De stad</h2><p>Verona.</p>');

        $this->assertCount(2, $sections);
        $this->assertSame('De reis', $sections[0]['title']);
        $this->assertSame('<p>Vooraf.</p><p>Onderweg.</p>', $sections[0]['html']);
        $this->assertSame('<p>Verona.</p>', $sections[1]['html']);
    }

    public function test_duplicate_titles_get_unique_keys(): void
    {
        $sections = $this->parser->sections('<h2>Dag 2</h2><p>A</p><h2>Dag</h2><p>B</p><h2>Dag</h2><p>C</p><h2>Dag</h2><p>D</p>');

        $this->assertSame(['dag-2', 'dag', 'dag-3', 'dag-4'], array_column($sections, 'key'));
        $this->assertSame(['Dag 2', 'Dag', 'Dag', 'Dag'], array_column($sections, 'title'));
    }

    #[DataProvider('emptyDescriptions')]
    public function test_an_empty_description_has_no_sections(?string $description): void
    {
        $this->assertSame([], $this->parser->sections($description));
    }

    public static function emptyDescriptions(): array
    {
        return [
            'null' => [null],
            'empty string' => [''],
            'whitespace' => ["  \n "],
        ];
    }

    public function test_a_description_without_h2_is_a_single_section_without_title(): void
    {
        $this->assertSame(
            [['key' => 'section', 'title' => null, 'html' => '<p>Alleen tekst.</p><h3>Kopje</h3><p>Meer.</p>']],
            $this->parser->sections('<p>Alleen tekst.</p><h3>Kopje</h3><p>Meer.</p>')
        );
    }

    public function test_only_top_level_h2s_start_a_section(): void
    {
        $sections = $this->parser->sections('<h2>De reis</h2><blockquote><h2>Citaat</h2></blockquote>');

        $this->assertCount(1, $sections);
        $this->assertSame('<blockquote><h2>Citaat</h2></blockquote>', $sections[0]['html']);
    }
}
