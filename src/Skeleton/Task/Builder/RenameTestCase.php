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
use RuntimeException;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

use function str_replace;

use const DIRECTORY_SEPARATOR;

class RenameTestCase extends Builder
{
    public function build(): void
    {
        $this->getBuildTask()->getIO()->write('<info>Renaming VendorTestCase</info>');

        $namespaceParts = explode(
            '\\',
            (string) $this->getBuildTask()->getAnswers()->packageNamespace
        );
        $vendor = array_shift($namespaceParts);
        $className = "{$vendor}TestCase";

        $this->renameTestCaseFile($className);

        /** @var SplFileInfo $file */
        foreach ($this->getTestFiles() as $file) {
            $this->renameParentClass($file, $className);
        }
    }

    private function getVendorTestCaseFile(): SplFileInfo
    {
        $finder = $this->getBuildTask()->getFinder();

        $finder
            ->in([$this->getBuildTask()->path('tests')])
            ->name('VendorTestCase.php');

        /** @var SplFileInfo $file */
        foreach ($finder as $file) {
            return $file;
        }

        throw new RuntimeException(
            'Unable to get contents of tests/VendorTestCase.php'
        );
    }

    private function getTestFiles(): Finder
    {
        $finder = $this->getBuildTask()->getFinder();

        $finder
            ->exclude(['Skeleton'])
            ->in([$this->getBuildTask()->path('tests')])
            ->files()
            ->name('*Test.php');

        return $finder;
    }

    private function renameTestCaseFile(string $className): void
    {
        $testCase = $this->getVendorTestCaseFile();

        $testCaseContents = $testCase->getContents();
        $testCaseContents = str_replace(
            'VendorTestCase',
            $className,
            $testCaseContents
        );

        $this->getBuildTask()->getFilesystem()->remove((string) $testCase->getRealPath());

        $this->getBuildTask()->getFilesystem()->dumpFile(
            $this->getBuildTask()->path(
                'tests' . DIRECTORY_SEPARATOR . "{$className}.php"
            ),
            $testCaseContents
        );
    }

    private function renameParentClass(SplFileInfo $file, string $parentClass): void
    {
        $contents = $file->getContents();
        $updatedContents = str_replace('VendorTestCase', $parentClass, $contents);

        $this->getBuildTask()->getFilesystem()->dumpFile(
            (string) $file->getRealPath(),
            $updatedContents
        );
    }
}
