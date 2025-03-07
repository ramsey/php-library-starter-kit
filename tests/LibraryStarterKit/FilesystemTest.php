<?php

declare(strict_types=1);

namespace Ramsey\Test\Dev\LibraryStarterKit;

use Ramsey\Dev\LibraryStarterKit\Filesystem;
use Symfony\Component\Finder\SplFileInfo;

class FilesystemTest extends TestCase
{
    public function testGetFile(): void
    {
        $filesystem = new Filesystem();
        $file = $filesystem->getFile(__FILE__);

        /** @phpstan-ignore-next-line */
        $this->assertInstanceOf(SplFileInfo::class, $file);
    }
}
