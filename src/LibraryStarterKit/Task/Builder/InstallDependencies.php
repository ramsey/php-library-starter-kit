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
 * Installs project dependencies after building a new library
 */
class InstallDependencies extends Builder
{
    public function build(): void
    {
        $this->getConsole()->section('Installing dependencies');

        // Remove lock files and vendor directories to start fresh.
        $this->getEnvironment()->getFilesystem()->remove([
            $this->getEnvironment()->path('composer.lock'),
            $this->getEnvironment()->path('vendor'),
        ]);

        $process = $this->getEnvironment()->getProcess([
            'composer',
            'update',
            '--no-interaction',
            '--ansi',
            '--no-progress',
        ]);

        $process->mustRun($this->streamProcessOutput());
    }
}
