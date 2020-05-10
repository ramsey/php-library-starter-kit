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

namespace Ramsey\Skeleton\Task;

use Composer\IO\IOInterface;
use ReflectionClass;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Process\Process;

use const DIRECTORY_SEPARATOR;

abstract class Task
{
    private string $appPath;
    private Filesystem $filesystem;
    private Finder $finder;
    private IOInterface $io;

    abstract public function run(): void;

    public function __construct(
        string $appPath,
        IOInterface $io,
        Filesystem $filesystem,
        Finder $finder
    ) {
        $this->appPath = $appPath;
        $this->filesystem = $filesystem;
        $this->finder = $finder;
        $this->io = $io;
    }

    public function getAppPath(): string
    {
        return $this->appPath;
    }

    public function getIO(): IOInterface
    {
        return $this->io;
    }

    public function getFilesystem(): Filesystem
    {
        return $this->filesystem;
    }

    public function getFinder(): Finder
    {
        return clone $this->finder;
    }

    /**
     * @param list<string> $command
     */
    public function getProcess(array $command): Process
    {
        // Support backward-compatibility with older versions of symfony/process.
        $processReflected = new ReflectionClass(Process::class);
        $processConstructor = $processReflected->getConstructor();

        if ($processConstructor !== null && !$processConstructor->getParameters()[0]->isArray()) {
            $command = implode(' ', array_map('escapeshellarg', $command)); // @codeCoverageIgnore
        }

        /**
         * @psalm-suppress PossiblyInvalidArgument
         * @phpstan-ignore-next-line
         */
        return new Process($command, $this->getAppPath());
    }

    public function path(string $fileName): string
    {
        return $this->appPath . DIRECTORY_SEPARATOR . $fileName;
    }

    public function streamProcessOutput(): callable
    {
        return function (string $type, string $buffer): void {
            if ($type === Process::ERR) {
                $this->getIO()->writeError($buffer, false);
            } else {
                $this->getIO()->write($buffer, false);
            }
        };
    }
}
