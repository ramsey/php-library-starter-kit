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
 * Runs all tests for the newly-created project
 */
class RunTests extends Builder
{
    public function build(): void
    {
        $this->getConsole()->section('Running project tests...');

        $this
            ->getEnvironment()
            ->getProcess(['composer', 'dev:test:all'])
            ->mustRun($this->streamProcessOutput());
    }
}
