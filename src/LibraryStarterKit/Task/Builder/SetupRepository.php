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
use Symfony\Component\Process\Process;

use function sprintf;
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
            ->gitConfigUser()
            ->cleanBuildDir()
            ->gitAddAllFiles()
            ->gitInitialCommit()
            ->setGitBranchName()
            ->installHooks();
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

    private function getUserName(): string
    {
        $process = $this->getEnvironment()->getProcess(
            ['git', 'config', 'user.name'],
        );

        $process->run();

        return trim($process->getOutput());
    }

    private function getUserEmail(): string
    {
        $process = $this->getEnvironment()->getProcess(
            ['git', 'config', 'user.email'],
        );

        $process->run();

        return trim($process->getOutput());
    }

    private function initializeRepository(): self
    {
        $this
            ->getEnvironment()
            ->getProcess(['git', 'init'])
            ->mustRun(function (string $type, string $buffer): void {
                if ($type === Process::OUT) {
                    $this->getConsole()->write($buffer);
                }
            });

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
        $author = sprintf(
            '%s <%s>',
            (string) $this->getAnswers()->authorName,
            (string) $this->getAnswers()->authorEmail,
        );

        $this
            ->getEnvironment()
            ->getProcess(['git', 'commit', '-n', '-m', self::COMMIT_MSG, '--author', $author])
            ->mustRun($this->streamProcessOutput());

        return $this;
    }

    private function setGitBranchName(): self
    {
        $this
            ->getEnvironment()
            ->getProcess(['git', 'branch', '-M', $this->getDefaultBranch()])
            ->mustRun($this->streamProcessOutput());

        return $this;
    }

    private function gitConfigUser(): self
    {
        $userName = $this->getUserName();
        $userEmail = $this->getUserEmail();

        if ($userName !== $this->getAnswers()->authorName) {
            $this
                ->getEnvironment()
                ->getProcess(['git', 'config', 'user.name', (string) $this->getAnswers()->authorName])
                ->mustRun($this->streamProcessOutput());
        }

        if ($userEmail !== $this->getAnswers()->authorEmail) {
            $this
                ->getEnvironment()
                ->getProcess(['git', 'config', 'user.email', (string) $this->getAnswers()->authorEmail])
                ->mustRun($this->streamProcessOutput());
        }

        return $this;
    }
}
