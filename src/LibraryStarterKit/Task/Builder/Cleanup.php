<?php

/**
 * This file is part of ramsey/php-library-starter-kit
 *
 * @copyright Copyright (c) Ben Ramsey <ben@benramsey.com>
 * @license https://opensource.org/licenses/MIT MIT License
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
    private const CLEANUP_FILES = [
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
