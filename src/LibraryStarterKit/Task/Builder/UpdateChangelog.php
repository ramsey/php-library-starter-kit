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
 * Updates the project's CHANGELOG using the CHANGELOG template
 */
class UpdateChangelog extends Builder
{
    public function build(): void
    {
        $this->getConsole()->section('Updating CHANGELOG.md');

        $changelog = $this->getEnvironment()->getTwigEnvironment()->render(
            'CHANGELOG.md.twig',
            $this->getAnswers()->getArrayCopy(),
        );

        $this->getEnvironment()->getFilesystem()->dumpFile(
            $this->getEnvironment()->path('CHANGELOG.md'),
            $changelog,
        );
    }
}
