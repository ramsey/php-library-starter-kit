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

use function trim;

/**
 * Initializes a local Git repository for the newly-created project
 */
class SetupRepository extends Builder
{
    private const COMMIT_MSG = 'chore: initialize project using ramsey/php-library-starter-kit';
    private const DEFAULT_BRANCH = 'main';

    public function build(): void
    {
        $this->getConsole()->section('Setting up Git repository');

        $this
            ->initializeRepository()
            ->installHooks()
            ->cleanBuildDir()
            ->gitAddAllFiles()
            ->gitInitialCommit();
    }

    private function getDefaultBranch(): string
    {
        $process = $this->getEnvironment()->getProcess(
            ['git', 'config', 'init.defaultBranch'],
        );

        $process->run();

        $defaultBranch = trim($process->getOutput());

        return $defaultBranch ?: self::DEFAULT_BRANCH;
    }

    private function initializeRepository(): self
    {
        $this
            ->getEnvironment()
            ->getProcess(['git', 'init', '-b', $this->getDefaultBranch()])
            ->mustRun($this->streamProcessOutput());

        return $this;
    }

    private function installHooks(): self
    {
        $captainHookPath = $this->getEnvironment()->path('vendor') . '/bin/captainhook';

        $this
            ->getEnvironment()
            ->getProcess([$captainHookPath, 'install', '--force', '--skip-existing'])
            ->mustRun($this->streamProcessOutput());

        return $this;
    }

    private function cleanBuildDir(): self
    {
        $this
            ->getEnvironment()
            ->getProcess(['composer', 'dev:build:clean'])
            ->mustRun();

        return $this;
    }

    private function gitAddAllFiles(): self
    {
        $this
            ->getEnvironment()
            ->getProcess(['git', 'add', '--all'])
            ->mustRun($this->streamProcessOutput());

        return $this;
    }

    private function gitInitialCommit(): self
    {
        $this
            ->getEnvironment()
            ->getProcess(['git', 'commit', '-n', '-m', self::COMMIT_MSG])
            ->mustRun($this->streamProcessOutput());

        return $this;
    }
}
