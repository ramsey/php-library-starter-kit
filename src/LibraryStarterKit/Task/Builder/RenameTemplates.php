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
        $this->getConsole()->section('Renaming template files');

        foreach ($this->getTemplatesFinder() as $template) {
            $this->removeTemplateExtension($template);
        }
    }

    private function getTemplatesFinder(): Finder
    {
        $finder = $this->getEnvironment()->getFinder();

        $finder
            ->ignoreDotFiles(false)
            ->exclude([
                'build',
                'vendor',
            ])
            ->in($this->getEnvironment()->getAppPath())
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

        $this->getConsole()->text(sprintf(
            '<comment>  - Renaming \'%s\' to \'%s\'.</comment>',
            $path,
            $newPath,
        ));

        $this->getEnvironment()->getFilesystem()->rename($path, $newPath);
    }
}
