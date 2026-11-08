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
            ->getProcess(['composer', 'lint'])
            ->mustRun($this->streamProcessOutput());

        $this
            ->getEnvironment()
            ->getProcess(['composer', 'test'])
            ->mustRun($this->streamProcessOutput());
    }
}
