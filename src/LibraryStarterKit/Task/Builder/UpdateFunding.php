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
 * Replaces this project's FUNDING.yml file with an empty one for the new project
 */
class UpdateFunding extends Builder
{
    public function build(): void
    {
        $this->getConsole()->section('Updating .github/FUNDING.yml');

        $changelog = $this->getEnvironment()->getTwigEnvironment()->render(
            'FUNDING.yml.twig',
            $this->getAnswers()->getArrayCopy(),
        );

        $this->getEnvironment()->getFilesystem()->dumpFile(
            $this->getEnvironment()->path('.github/FUNDING.yml'),
            $changelog,
        );
    }
}
