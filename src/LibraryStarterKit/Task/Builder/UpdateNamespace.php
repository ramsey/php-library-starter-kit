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
use Symfony\Component\Finder\SplFileInfo;

use function array_filter;
use function array_keys;
use function array_shift;
use function array_values;
use function explode;
use function implode;
use function iterator_to_array;
use function str_replace;

/**
 * Updates the namespace according to the one provided during project setup
 */
class UpdateNamespace extends Builder
{
    public function build(): void
    {
        $this->getConsole()->section('Updating namespace');

        $packageName = (string) $this->getAnswers()->packageName;
        $namespaceParts = explode(
            '\\',
            (string) $this->getAnswers()->packageNamespace,
        );
        $vendor = array_shift($namespaceParts);
        $subNamespace = implode('\\', $namespaceParts);

        $namespace = implode('\\', array_filter([$vendor, $subNamespace]));
        $testNamespace = implode('\\', array_filter([$vendor, 'Test', $subNamespace]));

        $replacements = [
            'Vendor\\SubNamespace' => $namespace,
            'Vendor\\Test\\SubNamespace' => $testNamespace,
            'Vendor\\\\SubNamespace' => str_replace('\\', '\\\\', $namespace),
            'Vendor\\\\Test\\\\SubNamespace' => str_replace('\\', '\\\\', $testNamespace),
            'ramsey/php-library-starter-kit' => $packageName,
        ];

        foreach ($this->getSourceFiles() as $file) {
            $this->replaceNamespace($file, $replacements);
        }
    }

    /**
     * @return SplFileInfo[]
     */
    private function getSourceFiles(): array
    {
        $finder = $this->getEnvironment()->getFinder();

        $finder
            ->exclude(['LibraryStarterKit'])
            ->in([
                $this->getEnvironment()->path('bin'),
                $this->getEnvironment()->path('src'),
                $this->getEnvironment()->path('tests'),
            ])
            ->files()
            ->name('*.php');

        /** @var SplFileInfo[] $files */
        $files = iterator_to_array($finder, false);

        // Find composer.json and add it to the array of files to return.
        $composerFinder = $this->getEnvironment()->getFinder();
        $composerFinder
            ->in([$this->getEnvironment()->getAppPath()])
            ->files()
            ->depth('== 0')
            ->name('composer.json');

        /** @var SplFileInfo $file */
        foreach ($composerFinder as $file) {
            $files[] = $file;

            break;
        }

        return $files;
    }

    /**
     * @param array<string, string> $replacements
     */
    private function replaceNamespace(SplFileInfo $file, array $replacements): void
    {
        $path = (string) $file->getRealPath();
        $contents = $file->getContents();

        $updatedContents = str_replace(
            array_keys($replacements),
            array_values($replacements),
            $contents,
        );

        $this->getEnvironment()->getFilesystem()->dumpFile($path, $updatedContents);
    }
}
