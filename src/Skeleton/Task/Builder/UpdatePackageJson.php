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
use Symfony\Component\Finder\SplFileInfo;
use stdClass;

use const JSON_PRETTY_PRINT;
use const JSON_UNESCAPED_SLASHES;
use const JSON_UNESCAPED_UNICODE;

/**
 * Updates the package.json file based on responses from the project setup
 */
class UpdatePackageJson extends Builder
{
    private const JSON_OPTIONS = JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE;

    public function build(): void
    {
        $this->getBuildTask()->getIO()->write('<info>Updating package.json</info>');

        /**
         * @psalm-var object{
         *     name: string,
         *     description: string
         * } | null $package
         */
        $package = json_decode($this->getPackageContents());
        if (!$package instanceof stdClass) {
            throw new RuntimeException('Unable to decode contents of package.json');
        }

        $license = (string) $this->getBuildTask()->getAnswers()->license;
        if ($license === 'Proprietary') {
            $license = 'UNLICENSED';
        }

        $package->name = '@' . (string) $this->getBuildTask()->getAnswers()->packageName;
        $package->description = (string) $this->getBuildTask()->getAnswers()->packageDescription;
        $package->license = $license;

        $this->buildAuthor($package);

        $this->getBuildTask()->getFilesystem()->dumpFile(
            $this->getBuildTask()->path('package.json'),
            (string) json_encode($package, self::JSON_OPTIONS)
        );
    }

    private function getPackageContents(): string
    {
        $finder = $this->getBuildTask()->getFinder();
        $finder
            ->in($this->getBuildTask()->getAppPath())
            ->files()
            ->depth('== 0')
            ->name('package.json');

        $packageContents = null;

        /** @var SplFileInfo $file */
        foreach ($finder as $file) {
            $packageContents = $file->getContents();

            break;
        }

        if ($packageContents === null) {
            throw new RuntimeException('Unable to get contents of package.json');
        }

        return (string) $packageContents;
    }

    private function buildAuthor(stdClass $package): void
    {
        $author = new stdClass();
        $author->name = $this->getBuildTask()->getAnswers()->authorName;

        if (trim((string) $this->getBuildTask()->getAnswers()->authorEmail) !== '') {
            $author->email = $this->getBuildTask()->getAnswers()->authorEmail;
        }

        if (trim((string) $this->getBuildTask()->getAnswers()->authorUrl) !== '') {
            $author->url = $this->getBuildTask()->getAnswers()->authorUrl;
        }

        $package->author = $author;
    }
}
