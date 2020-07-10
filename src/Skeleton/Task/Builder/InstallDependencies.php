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
 * Installs project dependencies after building a new library
 */
class InstallDependencies extends Builder
{
    public const COMPOSER_REMOVE_PACKAGES = [
        'composer/composer',
    ];

    public function build(): void
    {
        $this->getBuildTask()->getIO()->write('<info>Installing dependencies</info>');

        // Remove lockfiles and vendor directories to start fresh.
        $this->getBuildTask()->getFilesystem()->remove(
            [
                $this->getBuildTask()->path('composer.lock'),
                $this->getBuildTask()->path('vendor'),
            ]
        );

        $this->composerRemoveDevelopmentPackages();
        $this->composerInstall();
    }

    protected function composerRemoveDevelopmentPackages(): void
    {
        $process = $this->getBuildTask()->getProcess(
            [
                'composer',
                'remove',
                '--no-interaction',
                '--ansi',
                '--dev',
                '--no-update',
                ...self::COMPOSER_REMOVE_PACKAGES,
            ]
        );

        $process->mustRun();
    }

    protected function composerInstall(): void
    {
        $process = $this->getBuildTask()->getProcess(
            [
                'composer',
                'install',
                '--no-interaction',
                '--ansi',
                '--no-progress',
                '--no-suggest',
            ]
        );

        $process->mustRun($this->getBuildTask()->streamProcessOutput());
    }
}
