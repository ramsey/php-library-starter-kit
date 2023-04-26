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

use function array_filter;
use function explode;
use function implode;
use function preg_replace;

/**
 * Updates the source file headers to include license information, based on
 * responses received during project setup
 */
class UpdateSourceFileHeaders extends Builder
{
    private const HEADER_PATTERN = '%/\*\*.* \*/%sU';

    public function build(): void
    {
        $this->getConsole()->section('Updating source file headers');

        $newFileHeader = $this->getEnvironment()->getTwigEnvironment()->render(
            'header/source-file-header.twig',
            $this->getAnswers()->getArrayCopy(),
        );

        $headerLines = explode("\n", $newFileHeader);
        $headerLines = array_filter($headerLines);
        $newFileHeader = implode("\n", $headerLines);

        foreach ($this->getSourceFilesFinder() as $file) {
            $this->replaceSourceFileHeader($file, $newFileHeader);
        }
    }

    private function getSourceFilesFinder(): Finder
    {
        $finder = $this->getEnvironment()->getFinder();

        $finder
            ->exclude(['LibraryStarterKit'])
            ->in([
                $this->getEnvironment()->path('src'),
            ])
            ->files()
            ->name('*.php');

        return $finder;
    }

    private function replaceSourceFileHeader(SplFileInfo $file, string $newFileHeader): void
    {
        $path = (string) $file->getRealPath();
        $contents = $file->getContents();

        $updatedContents = (string) preg_replace(
            self::HEADER_PATTERN,
            $newFileHeader,
            $contents,
            1,
        );

        $this->getEnvironment()->getFilesystem()->dumpFile($path, $updatedContents);
    }
}
