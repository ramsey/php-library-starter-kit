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

/**
 * Fixes any style issues we might encounter (e.g., use statements sorting, etc.)
 */
class FixStyle extends Builder
{
    public function build(): void
    {
        $this->getConsole()->section('Fixing style issues...');

        $this
            ->getEnvironment()
            ->getProcess(['composer', 'dev:lint:fix'])
            ->mustRun($this->streamProcessOutput());
    }
}
