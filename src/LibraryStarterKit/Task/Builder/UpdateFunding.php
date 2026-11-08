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
 * Writes the new project's .github/FUNDING.yml when funding platforms were chosen
 */
class UpdateFunding extends Builder
{
    public function build(): void
    {
        if ($this->getAnswers()->funding === null || $this->getAnswers()->funding === []) {
            $this->getConsole()->section('Skipping .github/FUNDING.yml');

            return;
        }

        $this->getConsole()->section('Writing .github/FUNDING.yml');

        $funding = $this->getEnvironment()->getTwigEnvironment()->render(
            'FUNDING.yml.twig',
            $this->getAnswers()->getArrayCopy(),
        );

        $this->getEnvironment()->getFilesystem()->dumpFile(
            $this->getEnvironment()->path('.github/FUNDING.yml'),
            $funding,
        );
    }
}
