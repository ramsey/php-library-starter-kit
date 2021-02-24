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
use RuntimeException;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

use function array_shift;
use function explode;
use function str_replace;

use const DIRECTORY_SEPARATOR;

/**
 * Renames the test case class and file, for the new project
 */
class RenameTestCase extends Builder
{
    private const TEST_CASE = 'VendorTestCase';

    public function build(): void
    {
        $this->getConsole()->note('Renaming ' . self::TEST_CASE);

        $namespaceParts = explode(
            '\\',
            (string) $this->getAnswers()->packageNamespace,
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
        $finder = $this->getEnvironment()->getFinder();

        $finder
            ->in([$this->getEnvironment()->path('tests')])
            ->name(self::TEST_CASE . '.php');

        /** @var SplFileInfo $file */
        foreach ($finder as $file) {
            return $file;
        }

        throw new RuntimeException(
            'Unable to get contents of tests/' . self::TEST_CASE . '.php',
        );
    }

    private function getTestFiles(): Finder
    {
        $finder = $this->getEnvironment()->getFinder();

        $finder
            ->exclude(['LibraryStarterKit'])
            ->in([$this->getEnvironment()->path('tests')])
            ->files()
            ->name('*Test.php');

        return $finder;
    }

    private function renameTestCaseFile(string $className): void
    {
        $testCase = $this->getVendorTestCaseFile();

        $testCaseContents = $testCase->getContents();
        $testCaseContents = str_replace(
            self::TEST_CASE,
            $className,
            $testCaseContents,
        );

        $this->getEnvironment()->getFilesystem()->remove((string) $testCase->getRealPath());

        $this->getEnvironment()->getFilesystem()->dumpFile(
            $this->getEnvironment()->path(
                'tests' . DIRECTORY_SEPARATOR . "{$className}.php",
            ),
            $testCaseContents,
        );
    }

    private function renameParentClass(SplFileInfo $file, string $parentClass): void
    {
        $contents = $file->getContents();
        $updatedContents = str_replace(self::TEST_CASE, $parentClass, $contents);

        $this->getEnvironment()->getFilesystem()->dumpFile(
            (string) $file->getRealPath(),
            $updatedContents,
        );
    }
}
