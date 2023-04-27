<?php

declare(strict_types=1);

namespace Ramsey\Test\Dev\LibraryStarterKit;

use PHPUnit\Framework\Assert;
use Spatie\Snapshots\Driver;

use function assert;
use function is_string;
use function preg_replace;

/**
 * A text driver for spatie/phpunit-snapshot-assertions that ensures snapshots
 * are serialized and matched with lf line endings
 */
class WindowsSafeTextDriver implements Driver
{
    public function serialize(mixed $data): string
    {
        assert(is_string($data));

        // Save snapshot only with lf line endings.
        return (string) preg_replace('/\R/', "\n", $data);
    }

    public function extension(): string
    {
        return 'txt';
    }

    public function match(mixed $expected, mixed $actual): void
    {
        assert(is_string($expected));

        // Make sure the expected string has lf line endings, so we can
        // compare accurately.
        $expected = (string) preg_replace('/\R/', "\n", $expected);

        Assert::assertEquals($expected, $this->serialize($actual));
    }
}
