<?php

/**
 * This file is part of ramsey/php-library-starter-kit
 *
 * ramsey/php-library-starter-kit is open source software: you can
 * distribute it and/or modify it under the terms of the MIT License
 * (the "License"). You may not use this file except in
 * compliance with the License.
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or
 * implied. See the License for the specific language governing
 * permissions and limitations under the License.
 *
 * @copyright Copyright (c) Ben Ramsey <ben@benramsey.com>
 * @license https://opensource.org/licenses/MIT MIT License
 */

declare(strict_types=1);

namespace Ramsey\Dev\LibraryStarterKit\Task\Builder;

use Ramsey\Dev\LibraryStarterKit\Task\Builder;

use const DIRECTORY_SEPARATOR;

/**
 * Updates the project's Code of Conduct using the one selected during setup
 */
class UpdateCodeOfConduct extends Builder
{
    public function build(): void
    {
        if ($this->getBuildTask()->getAnswers()->codeOfConduct === null) {
            $this->getBuildTask()->getIO()->write('<info>Removing CODE_OF_CONDUCT.md</info>');
            $this->getBuildTask()->getFilesystem()->remove(
                $this->getBuildTask()->path('CODE_OF_CONDUCT.md'),
            );

            return;
        }

        $this->getBuildTask()->getIO()->write('<info>Updating CODE_OF_CONDUCT.md</info>');

        $codeOfConductTemplate = 'code-of-conduct' . DIRECTORY_SEPARATOR;
        $codeOfConductTemplate .= $this->getBuildTask()->getAnswers()->codeOfConduct ?? '';
        $codeOfConductTemplate .= '.md.twig';

        $changelog = $this->getBuildTask()->getTwigEnvironment()->render(
            $codeOfConductTemplate,
            $this->getBuildTask()->getAnswers()->getArrayCopy(),
        );

        $this->getBuildTask()->getFilesystem()->dumpFile(
            $this->getBuildTask()->path('CODE_OF_CONDUCT.md'),
            $changelog,
        );
    }
}
