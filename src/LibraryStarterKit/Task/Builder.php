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

namespace Ramsey\Dev\LibraryStarterKit\Task;

use Ramsey\Dev\LibraryStarterKit\Answers;
use Ramsey\Dev\LibraryStarterKit\Setup;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Represents a builder that uses user responses to build some part of a library
 */
abstract class Builder
{
    private Build $buildTask;

    public function __construct(Build $buildTask)
    {
        $this->buildTask = $buildTask;
    }

    /**
     * Executes the build action for this particular builder
     */
    abstract public function build(): void;

    public function getAnswers(): Answers
    {
        return $this->buildTask->getAnswers();
    }

    public function getEnvironment(): Setup
    {
        return $this->buildTask->getSetup();
    }

    public function getConsole(): SymfonyStyle
    {
        return $this->buildTask->getConsole();
    }

    /**
     * Returns a callback that may be used to stream process output to stdout
     *
     * @return callable(string, string): void
     */
    public function streamProcessOutput(): callable
    {
        return function (string $type, string $buffer): void {
            $this->getConsole()->write($buffer);
        };
    }
}
