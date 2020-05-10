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
use Symfony\Component\Finder\SplFileInfo;

use const DIRECTORY_SEPARATOR;

class UpdateNamespace extends Builder
{
    public function build(): void
    {
        $this->getBuildTask()->getIO()->write('<info>Updating namespace</info>');

        $packageName = (string) $this->getBuildTask()->getAnswers()->packageName;
        $namespaceParts = explode(
            '\\',
            (string) $this->getBuildTask()->getAnswers()->packageNamespace
        );
        $vendor = array_shift($namespaceParts);
        $subNamespace = implode('\\', $namespaceParts);

        $namespace = implode('\\', array_filter([$vendor, $subNamespace]));
        $testNamespace = implode('\\', array_filter([$vendor, 'Test', $subNamespace]));
        $consoleNamespace = implode('\\', [$vendor, 'Console']);

        $replacements = [
            'Vendor\\SubNamespace' => $namespace,
            'Vendor\\Test\\SubNamespace' => $testNamespace,
            'Vendor\\Console' => $consoleNamespace,
            'Vendor\\\\SubNamespace' => str_replace('\\', '\\\\', $namespace),
            'Vendor\\\\Test\\\\SubNamespace' => str_replace('\\', '\\\\', $testNamespace),
            'Vendor\\\\Console' => str_replace('\\', '\\\\', $consoleNamespace),
            'ramsey/php-library-skeleton' => $packageName,
        ];

        /** @var SplFileInfo $file */
        foreach ($this->getSourceFiles() as $file) {
            $this->replaceNamespace($file, $replacements);
        }
    }

    /**
     * @return list<SplFileInfo>
     */
    private function getSourceFiles(): array
    {
        $finder = $this->getBuildTask()->getFinder();

        $finder
            ->exclude(['Skeleton'])
            ->in(
                [
                    $this->getBuildTask()->path('bin'),
                    $this->getBuildTask()->path('src'),
                    $this->getBuildTask()->path('tests'),
                    $this->getBuildTask()->path('resources' . DIRECTORY_SEPARATOR . 'console'),
                ]
            )
            ->files()
            ->name('*.php');

        /** @var list<SplFileInfo> $files */
        $files = iterator_to_array($finder, false);

        // Find composer.json and add it to the array of files to return.
        $composerFinder = $this->getBuildTask()->getFinder();
        $composerFinder
            ->in([$this->getBuildTask()->getAppPath()])
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
            $contents
        );

        $this->getBuildTask()->getFilesystem()->dumpFile($path, $updatedContents);
    }
}
