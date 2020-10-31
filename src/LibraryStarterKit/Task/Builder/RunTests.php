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

/**
 * Runs all tests for the newly-created project
 */
class RunTests extends Builder
{
    public function build(): void
    {
        $this->getBuildTask()->getIO()->write(
            '<info>Running project tests...</info>',
        );

        $commandPrefix = $this->getBuildTask()->getAnswers()->commandPrefix
            ?? UpdateCommandPrefix::DEFAULT;

        $this
            ->getBuildTask()
            ->getProcess(['composer', 'run-script', "{$commandPrefix}:test:all"])
            ->mustRun($this->getBuildTask()->streamProcessOutput());
    }
}
