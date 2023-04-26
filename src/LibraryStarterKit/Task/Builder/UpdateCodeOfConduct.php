<?php

/**
 * This file is part of ramsey/php-library-starter-kit
 *
 * @copyright Copyright (c) Ben Ramsey <ben@benramsey.com>
 * @license https://opensource.org/licenses/MIT MIT License
 */

declare(strict_types=1);

namespace Ramsey\Dev\LibraryStarterKit\Task\Builder;

use Ramsey\Dev\LibraryStarterKit\Console\Question\CodeOfConduct;
use Ramsey\Dev\LibraryStarterKit\Task\Builder;

use const DIRECTORY_SEPARATOR;

/**
 * Updates the project's Code of Conduct using the one selected during setup
 */
class UpdateCodeOfConduct extends Builder
{
    public function build(): void
    {
        if ($this->getAnswers()->codeOfConduct === CodeOfConduct::DEFAULT) {
            $this->getConsole()->section('Removing CODE_OF_CONDUCT.md');
            $this->getEnvironment()->getFilesystem()->remove(
                $this->getEnvironment()->path('CODE_OF_CONDUCT.md'),
            );

            return;
        }

        $this->getConsole()->section('Updating CODE_OF_CONDUCT.md');

        $codeOfConductTemplate = 'code-of-conduct' . DIRECTORY_SEPARATOR;
        $codeOfConductTemplate .= $this->getAnswers()->codeOfConduct ?? '';
        $codeOfConductTemplate .= '.md.twig';

        $codeOfConduct = $this->getEnvironment()->getTwigEnvironment()->render(
            $codeOfConductTemplate,
            $this->getAnswers()->getArrayCopy(),
        );

        $this->getEnvironment()->getFilesystem()->dumpFile(
            $this->getEnvironment()->path('CODE_OF_CONDUCT.md'),
            $codeOfConduct,
        );
    }
}
