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
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

use function basename;
use function dirname;
use function sprintf;

use const DIRECTORY_SEPARATOR;

/**
 * Removes the .template suffix from any files in the project
 */
class RenameTemplates extends Builder
{
    public function build(): void
    {
        $this->getBuildTask()->getIO()->write('<info>Renaming template files</info>');

        /** @var SplFileInfo $template */
        foreach ($this->getTemplatesFinder() as $template) {
            $this->removeTemplateExtension($template);
        }
    }

    private function getTemplatesFinder(): Finder
    {
        $finder = $this->getBuildTask()->getFinder();

        $finder
            ->ignoreDotFiles(false)
            ->exclude(
                [
                    'build',
                    'vendor',
                ],
            )
            ->in($this->getBuildTask()->getAppPath())
            ->name('*.template')
            ->name('.*.template');

        return $finder;
    }

    private function removeTemplateExtension(SplFileInfo $file): void
    {
        $path = (string) $file->getRealPath();
        $dirName = dirname($path);
        $baseName = basename($path, '.template');
        $newPath = $dirName . DIRECTORY_SEPARATOR . $baseName;

        $this->getBuildTask()->getIO()->write(
            sprintf(
                '<comment>Renaming \'%s\' to \'%s\'.</comment>',
                $path,
                $newPath,
            ),
        );

        $this->getBuildTask()->getFilesystem()->rename($path, $newPath);
    }
}
