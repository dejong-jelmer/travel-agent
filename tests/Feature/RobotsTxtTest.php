<?php

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * public/robots.txt is a static file served by the web server, so these tests read it from disk.
 */
class RobotsTxtTest extends TestCase
{
    /**
     * The groups of the file, by user agent, each with its rule lines.
     *
     * @return array<string, list<string>>
     */
    private function robotsGroups(): array
    {
        $groups = [];
        $agents = [];
        $inRules = false;

        foreach (file(public_path('robots.txt'), FILE_IGNORE_NEW_LINES) as $line) {
            $line = trim(preg_replace('/#.*/', '', $line));

            if ($line === '' || str_starts_with(strtolower($line), 'sitemap:')) {
                continue;
            }

            if (str_starts_with(strtolower($line), 'user-agent:')) {
                // A user agent after rules starts a new group
                if ($inRules) {
                    $agents = [];
                    $inRules = false;
                }
                $agent = trim(substr($line, strlen('user-agent:')));
                $agents[] = $agent;
                $groups[$agent] = [];

                continue;
            }

            $inRules = true;
            foreach ($agents as $agent) {
                $groups[$agent][] = $line;
            }
        }

        return $groups;
    }

    public function test_every_crawler_may_read_the_whole_site(): void
    {
        $this->assertSame(['Disallow:'], $this->robotsGroups()['*']);
    }

    #[DataProvider('aiCrawlers')]
    public function test_ai_crawler_is_allowed_explicitly(string $agent): void
    {
        $this->assertSame(['Allow: /'], $this->robotsGroups()[$agent] ?? null, "{$agent} has no group that allows everything");
    }

    public static function aiCrawlers(): array
    {
        return [
            'GPTBot' => ['GPTBot'],
            'ClaudeBot' => ['ClaudeBot'],
            'PerplexityBot' => ['PerplexityBot'],
            'Google-Extended' => ['Google-Extended'],
        ];
    }

    public function test_no_group_blocks_a_path(): void
    {
        foreach ($this->robotsGroups() as $agent => $rules) {
            foreach ($rules as $rule) {
                $this->assertDoesNotMatchRegularExpression('/^disallow:\s*\S/i', $rule, "{$agent} blocks a path: {$rule}");
            }
        }
    }

    public function test_it_points_to_the_sitemap_route(): void
    {
        $this->assertMatchesRegularExpression('/^Sitemap: (https:\/\/\S+)$/m', file_get_contents(public_path('robots.txt')));

        preg_match('/^Sitemap: (\S+)$/m', file_get_contents(public_path('robots.txt')), $matches);

        // The file names the production host; the path must be the one the sitemap route serves
        $this->assertSame(parse_url(route('sitemap'), PHP_URL_PATH), parse_url($matches[1], PHP_URL_PATH));
    }
}
