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

namespace Ramsey\Dev\LibraryStarterKit\Task\Builder;

use Ramsey\Dev\LibraryStarterKit\Task\Builder;

use function sprintf;

use const DIRECTORY_SEPARATOR;

/**
 * Removes files that we don't want to include in the newly-created project
 */
class Cleanup extends Builder
{
    /**
     * The files and directories listed here are relative to the project root.
     */
    private const array CLEANUP_FILES = [
        'resources' . DIRECTORY_SEPARATOR . 'templates',
        'src' . DIRECTORY_SEPARATOR . 'LibraryStarterKit',
        'tests' . DIRECTORY_SEPARATOR . 'LibraryStarterKit',
        '.git',
        '.starter-kit-answers',
    ];

    public function build(): void
    {
        $this->getConsole()->section('Cleaning up...');

        foreach (self::CLEANUP_FILES as $file) {
            $this->getEnvironment()->getFilesystem()->remove(
                $this->getEnvironment()->path($file),
            );

            $this->getConsole()->text(sprintf(
                '<comment>  - Deleted \'%s\'.</comment>',
                $this->getEnvironment()->path($file),
            ));
        }
    }
}
