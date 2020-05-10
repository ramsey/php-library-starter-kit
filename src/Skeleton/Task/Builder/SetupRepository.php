<?php

/**
 * This file is part of ramsey/php-library-skeleton
 *
 * ramsey/php-library-skeleton is open source software: you can
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

namespace Ramsey\Skeleton\Task\Builder;

use Ramsey\Skeleton\Task\Builder;

/**
 * Initializes a local Git repository for the newly-created project
 */
class SetupRepository extends Builder
{
    private const COMMIT_MSG = 'chore: initialize project using ramsey/php-library-skeleton';

    public function build(): void
    {
        $this->getBuildTask()->getIO()->write('<info>Setting up Git repository</info>');

        $this
            ->initializeRepository()
            ->cleanBuildDir()
            ->configureAuthor()
            ->yarnAddHusky()
            ->gitAddAllFiles()
            ->gitInitialCommit();
    }

    private function initializeRepository(): self
    {
        $this
            ->getBuildTask()
            ->getProcess(['git', 'init'])
            ->mustRun($this->getBuildTask()->streamProcessOutput());

        return $this;
    }

    private function cleanBuildDir(): self
    {
        $commandPrefix = $this->getBuildTask()->getAnswers()->commandPrefix
            ?? UpdateCommandPrefix::DEFAULT;

        $this
            ->getBuildTask()
            ->getProcess(['composer', 'run-script', "{$commandPrefix}:build:clean"])
            ->mustRun();

        return $this;
    }

    private function configureAuthor(): self
    {
        $authorName = trim((string) $this->getBuildTask()->getAnswers()->authorName);
        $authorEmail = trim((string) $this->getBuildTask()->getAnswers()->authorEmail);

        if ($authorName !== '') {
            $this
                ->getBuildTask()
                ->getProcess(['git', 'config', 'user.name', $authorName])
                ->mustRun();
        }

        if ($authorEmail !== '') {
            $this
                ->getBuildTask()
                ->getProcess(['git', 'config', 'user.email', $authorEmail])
                ->mustRun();
        }

        return $this;
    }

    /**
     * Uses Yarn to add Husky
     *
     * Husky is already included in package.json as a dependency, but Husky's
     * Git hooks are only installed properly when Husky is added *after*
     * initializing a repository, so we must add Husky at this point.
     */
    private function yarnAddHusky(): self
    {
        $this
            ->getBuildTask()
            ->getProcess(['yarn', 'add', 'husky'])
            ->mustRun($this->getBuildTask()->streamProcessOutput());

        return $this;
    }

    private function gitAddAllFiles(): self
    {
        $this
            ->getBuildTask()
            ->getProcess(['git', 'add', '--all'])
            ->mustRun($this->getBuildTask()->streamProcessOutput());

        return $this;
    }

    private function gitInitialCommit(): self
    {
        $this
            ->getBuildTask()
            ->getProcess(['git', 'commit', '-m', self::COMMIT_MSG])
            ->mustRun($this->getBuildTask()->streamProcessOutput());

        return $this;
    }
}
