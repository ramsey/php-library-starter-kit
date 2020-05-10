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
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class UpdateCommandPrefix extends Builder
{
    public const DEFAULT = 'vnd';

    public function build(): void
    {
        $this->getBuildTask()->getIO()->write('<info>Updating command prefix</info>');

        $commandPrefix = $this->getBuildTask()->getAnswers()->commandPrefix ?? self::DEFAULT;

        if ($commandPrefix === self::DEFAULT) {
            return;
        }

        /** @var SplFileInfo $file */
        foreach ($this->getFiles() as $file) {
            $this->replaceCommandPrefix($file, $commandPrefix);
        }
    }

    private function getFiles(): Finder
    {
        $finder = $this->getBuildTask()->getFinder();

        $finder
            ->in($this->getBuildTask()->getAppPath())
            ->ignoreDotFiles(false)
            ->exclude(
                [
                    'build',
                    'node_modules',
                    'resources/templates',
                    'src/Skeleton',
                    'tests/Skeleton',
                    'vendor',
                ]
            )
            ->files();

        return $finder;
    }

    private function replaceCommandPrefix(SplFileInfo $file, string $commandPrefix): void
    {
        $searches = [
            self::DEFAULT . ':',
            '`' . self::DEFAULT . '`',
            'composer list ' . self::DEFAULT,
        ];

        $replaces = [
            $commandPrefix . ':',
            '`' . $commandPrefix . '`',
            'composer list ' . $commandPrefix,
        ];

        $path = (string) $file->getRealPath();
        $contents = $file->getContents();

        $matchFound = false;
        foreach ($searches as $search) {
            if (strpos($contents, $search) !== false) {
                $matchFound = true;
            }
        }

        if (!$matchFound) {
            return;
        }

        $updatedContents = str_replace(
            $searches,
            $replaces,
            $contents
        );

        $this->getBuildTask()->getFilesystem()->dumpFile($path, $updatedContents);
    }
}
