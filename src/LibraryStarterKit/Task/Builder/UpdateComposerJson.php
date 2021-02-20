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
use Symfony\Component\Finder\SplFileInfo;

use function in_array;
use function json_decode;
use function json_encode;
use function trim;

use const JSON_PRETTY_PRINT;
use const JSON_UNESCAPED_SLASHES;
use const JSON_UNESCAPED_UNICODE;

/**
 * Updates values in the composer.json file
 */
class UpdateComposerJson extends Builder
{
    private const WHITELIST_REQUIRE = [
        'php',
    ];

    private const WHITELIST_AUTOLOAD = [
        'Vendor\\SubNamespace\\',
    ];

    private const WHITELIST_AUTOLOAD_DEV = [
        'Vendor\\Test\\SubNamespace\\',
    ];

    private const JSON_OPTIONS = JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE;

    public function build(): void
    {
        $this->getConsole()->note('Updating composer.json');

        /**
         * @var array<string, mixed>|null $composer
         */
        $composer = json_decode($this->getComposerContents(), true);
        if ($composer === null) {
            throw new RuntimeException('Unable to decode contents of composer.json');
        }

        $composer['name'] = (string) $this->getAnswers()->packageName;
        $composer['description'] = (string) $this->getAnswers()->packageDescription;
        $composer['type'] = 'library';
        $composer['keywords'] = $this->getAnswers()->packageKeywords;
        $composer['license'] = $this->getAnswers()->license;

        $this->buildAuthors($composer);
        $this->buildRequire($composer);
        $this->buildAutoload($composer);
        $this->buildAutoloadDev($composer);

        unset($composer['scripts']);
        unset($composer['scripts-descriptions']);

        $this->getEnvironment()->getFilesystem()->dumpFile(
            $this->getEnvironment()->path('composer.json'),
            (string) json_encode($composer, self::JSON_OPTIONS),
        );
    }

    /**
     * @param array<string, string> $data
     * @param string[] $whitelist
     *
     * @return array<string, string>
     */
    private function filterPropertiesByWhitelist(array $data, array $whitelist): array
    {
        $filtered = [];

        /**
         * @var string $property
         * @var string $value
         */
        foreach ($data as $property => $value) {
            if (in_array($property, $whitelist)) {
                $filtered[$property] = $value;
            }
        }

        return $filtered;
    }

    private function getComposerContents(): string
    {
        $finder = $this->getEnvironment()->getFinder();
        $finder
            ->in($this->getEnvironment()->getAppPath())
            ->files()
            ->depth('== 0')
            ->name('composer.json');

        $composerContents = null;

        /** @var SplFileInfo $file */
        foreach ($finder as $file) {
            $composerContents = $file->getContents();

            break;
        }

        if ($composerContents === null) {
            throw new RuntimeException('Unable to get contents of composer.json');
        }

        return $composerContents;
    }

    /**
     * @param array<string, mixed> $composer
     */
    private function buildAuthors(array &$composer): void
    {
        $author = [];
        $author['name'] = $this->getAnswers()->authorName;

        if (trim((string) $this->getAnswers()->authorEmail) !== '') {
            $author['email'] = $this->getAnswers()->authorEmail;
        }

        if (trim((string) $this->getAnswers()->authorUrl) !== '') {
            $author['homepage'] = $this->getAnswers()->authorUrl;
        }

        $composer['authors'] = [$author];
    }

    /**
     * @param array<string, mixed> $composer
     */
    private function buildRequire(array &$composer): void
    {
        if (!isset($composer['require'])) {
            return;
        }

        /** @var array<string, string> $require */
        $require = $composer['require'];

        $composer['require'] = $this->filterPropertiesByWhitelist(
            $require,
            self::WHITELIST_REQUIRE,
        );
    }

    /**
     * @param array<string, mixed> $composer
     */
    private function buildAutoload(array &$composer): void
    {
        if (!isset($composer['autoload']['psr-4'])) {
            return;
        }

        /** @var array<string, string> $autoload */
        $autoload = $composer['autoload']['psr-4'];

        $composer['autoload']['psr-4'] = $this->filterPropertiesByWhitelist(
            $autoload,
            self::WHITELIST_AUTOLOAD,
        );
    }

    /**
     * @param array<string, mixed> $composer
     */
    private function buildAutoloadDev(array &$composer): void
    {
        if (!isset($composer['autoload-dev']['psr-4'])) {
            return;
        }

        /** @var array<string, string> $autoloadDev */
        $autoloadDev = $composer['autoload-dev']['psr-4'];

        $composer['autoload-dev']['psr-4'] = $this->filterPropertiesByWhitelist(
            $autoloadDev,
            self::WHITELIST_AUTOLOAD_DEV,
        );
    }
}
