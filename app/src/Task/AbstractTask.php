<?php

/**
 * This file is part of the ramsey/php-library-skeleton project
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright Copyright (c) Ben Ramsey <ben@benramsey.com>
 * @license http://opensource.org/licenses/MIT MIT
 */

declare(strict_types=1);

namespace Ramsey\Skeleton\Task;

use Composer\IO\IOInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

/**
 * `AbstractTask` provides the foundation for ramsey/php-library-skeleton tasks.
 */
abstract class AbstractTask
{
    /**
     * @var IOInterface
     */
    private $io;

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var Finder
     */
    private $finder;

    /**
     * Runs the task.
     */
    abstract public function run(): void;

    /**
     * Constructs the task.
     *
     * @param IOInterface $io
     * @param Filesystem $filesystem
     * @param Finder $finder
     */
    public function __construct(IOInterface $io, Filesystem $filesystem, Finder $finder)
    {
        $this->io = $io;
        $this->filesystem = $filesystem;
        $this->finder = $finder;
    }

    /**
     * Returns the task's input/output object.
     *
     * @return IOInterface
     */
    public function getIO(): IOInterface
    {
        return $this->io;
    }

    /**
     * Returns the task's filesystem object.
     *
     * @return Filesystem
     */
    public function getFilesystem(): Filesystem
    {
        return $this->filesystem;
    }

    /**
     * Returns the task's file/directory `Finder`.
     *
     * @return Finder
     */
    public function getFinder(): Finder
    {
        return $this->finder;
    }
}
