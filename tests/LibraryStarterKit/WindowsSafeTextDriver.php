<?php

declare(strict_types=1);

namespace Ramsey\Test\Dev\LibraryStarterKit;

use PHPUnit\Framework\Assert;
use Spatie\Snapshots\Driver;

use function preg_replace;

/**
 * A text driver for spatie/phpunit-snapshot-assertions that ensures snapshots
 * are serialized and matched with lf line endings
 */
class WindowsSafeTextDriver implements Driver
{
    /**
     * @param string $data
     *
     * @inheritDoc
     */
    public function serialize($data): string
    {
        // Save snapshot only with lf line endings.
        $data = (string) preg_replace('/\r\n/', "\n", $data);

        return $data;
    }

    public function extension(): string
    {
        return 'txt';
    }

    /**
     * @param string $expected
     * @param string $actual
     *
     * @inheritDoc
     */
    public function match($expected, $actual): void
    {
        // Make sure the expected string has lf line endings, so we can
        // compare accurately.
        $expected = (string) preg_replace('/\r\n/', "\n", $expected);

        Assert::assertEquals($expected, $this->serialize($actual));
    }
}
