<?php

/**
 * This file is part of ramsey/php-library-starter-kit
 *
 * @copyright Copyright (c) Ben Ramsey <ben@benramsey.com>
 * @license https://opensource.org/licenses/MIT MIT License
 */

declare(strict_types=1);

namespace Ramsey\Dev\LibraryStarterKit;

use Symfony\Component\Filesystem\Filesystem as SymfonyFilesystem;
use Symfony\Component\Finder\SplFileInfo;

/**
 * Wraps the Symfony Filesystem class to provide additional file operations
 */
class Filesystem extends SymfonyFilesystem
{
    /**
     * Returns an instance of a file
     */
    public function getFile(string $path): SplFileInfo
    {
        return new SplFileInfo($path, '', '');
    }
}
