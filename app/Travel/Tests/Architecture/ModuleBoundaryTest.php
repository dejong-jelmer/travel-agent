<?php

namespace App\Travel\Tests\Architecture;

use PhpToken;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Finder\Finder;

/**
 * Keeps the travel module apart from the rest of the app: within App\ it only uses its own code and the booking it
 * hangs off, so no trips, travelers, app traits or app enums. Its tests may also sign in as a user. Names are read
 * from the PHP tokens, so imports, group imports and fully qualified names all count.
 */
class ModuleBoundaryTest extends TestCase
{
    private const ALLOWED = ['App\Models\Booking'];

    private const ALLOWED_IN_TESTS = ['App\Models\User'];

    public function test_the_module_only_uses_its_own_code_and_the_booking(): void
    {
        $violations = [];

        foreach (Finder::create()->files()->name('*.php')->in(dirname(__DIR__, 2)) as $file) {
            $allowed = str_starts_with($file->getRelativePath(), 'Tests')
                ? [...self::ALLOWED, ...self::ALLOWED_IN_TESTS]
                : self::ALLOWED;

            foreach (PhpToken::tokenize($file->getContents()) as $token) {
                if (! $token->is([T_NAME_QUALIFIED, T_NAME_FULLY_QUALIFIED])) {
                    continue;
                }

                $name = ltrim($token->text, '\\');

                if ($this->isOutsideTheModule($name, $allowed)) {
                    $violations[] = "{$file->getRelativePathname()}:{$token->line} uses {$name}";
                }
            }
        }

        $this->assertSame([], $violations, 'The travel module uses app code outside App\Travel.');
    }

    /**
     * @param  list<string>  $allowed
     */
    private function isOutsideTheModule(string $name, array $allowed): bool
    {
        return str_starts_with($name, 'App\\')
            && $name !== 'App\Travel'
            && ! str_starts_with($name, 'App\Travel\\')
            && ! in_array($name, $allowed, true);
    }
}
