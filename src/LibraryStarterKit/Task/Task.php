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

use Composer\IO\IOInterface;
use ReflectionClass;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Process\Process;

use function array_map;
use function implode;

use const DIRECTORY_SEPARATOR;

/**
 * Represents a library starter kit task
 */
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

    /**
     * Returns the absolute path to the directory for the application
     */
    public function getAppPath(): string
    {
        return $this->appPath;
    }

    /**
     * Returns the IO object for this task
     */
    public function getIO(): IOInterface
    {
        return $this->io;
    }

    /**
     * Returns a filesystem object to use when executing filesystem commands
     */
    public function getFilesystem(): Filesystem
    {
        return $this->filesystem;
    }

    /**
     * Returns an object used to search for files or directories
     */
    public function getFinder(): Finder
    {
        return clone $this->finder;
    }

    /**
     * Returns an instance used for executing a system command
     *
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

    /**
     * Given a project-relative directory or filename, constructs an absolute path
     */
    public function path(string $fileName): string
    {
        return $this->appPath . DIRECTORY_SEPARATOR . $fileName;
    }

    /**
     * Returns a callback that may be used by the IO instance to stream process
     * output to stdout or stderr
     */
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
