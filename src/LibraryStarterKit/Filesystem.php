<?php

/**
 * This file is part of ramsey/php-library-starter-kit
 *
 * Copyright © Ben Ramsey <ben@ramsey.dev>
 *
 * Licensed under the MIT License (the "License"); you may not use this file
 * except in compliance with the License. You may obtain a copy of the License
 * at <https://opensource.org/license/MIT>.
 *
 * SPDX-License-Identifier: MIT
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
