<?php

declare(strict_types=1);

namespace Ramsey\Test\Dev\LibraryStarterKit;

use Ramsey\Dev\LibraryStarterKit\Answers;
use Ramsey\Dev\LibraryStarterKit\Filesystem;
use Vendor\Test\SubNamespace\VendorTestCase;

class TestCase extends VendorTestCase
{
    protected Answers $answers;

    protected function setUp(): void
    {
        parent::setUp();

        $filesystem = $this->mockery(Filesystem::class);
        $filesystem->expects()->exists('/path/to/answers.json')->andReturnFalse();

        $this->answers = new Answers('/path/to/answers.json', $filesystem);
    }
}
