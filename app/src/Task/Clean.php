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

/**
 * The `Clean` task removes any files or directories that should not be around
 * after setting up the project.
 */
class Clean extends AbstractTask
{
    /**
     * Files and directories to remove.
     */
    private const DELETE_PATHS = [
        'app',
        'composer.lock',
        'skeleton',
    ];

    /**
     * Runs the `Clean` task.
     */
    public function run(): void
    {
        $root = realpath('.');
        $pathsToDelete = array_map(function ($value) use ($root) {
            return $root . '/' . $value;
        }, self::DELETE_PATHS);

        $this->getFilesystem()->remove($pathsToDelete);
    }
}
