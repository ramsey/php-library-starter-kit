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

namespace Ramsey\Skeleton\Task\Questions;

use InvalidArgumentException;
use Ramsey\Skeleton\Task\Question;

use function preg_match;

/**
 * Asks for the namespace to use for this package
 */
class PackageNamespace extends Question
{
    private const VALID_PATTERN = '/^[a-zA-Z_\x80-\xff][a-zA-Z0-9_\x80-\xff]*$/';

    public function getName(): string
    {
        return 'packageNamespace';
    }

    public function getQuestion(): string
    {
        $example = $this->getDefault();

        if ($example === null) {
            $example = 'Foo\\Bar\\Baz';
        }

        return "What is the package's root namespace? (e.g., {$example})";
    }

    public function getDefault(): ?string
    {
        $packageName = $this->getAnswers()->packageName;

        if ($packageName === null || trim((string) $packageName) === '') {
            return null;
        }

        $packageName = str_replace('/', '\\', $packageName);
        $packageNameParts = explode('\\', $packageName);
        $packageNameParts = array_map($this->namify(), $packageNameParts);

        return implode('\\', $packageNameParts);
    }

    public function getValidator(): callable
    {
        return function (string $data): string {
            $packageNameParts = explode('\\', $data);

            foreach ($packageNameParts as $namePart) {
                if (!preg_match(self::VALID_PATTERN, $namePart)) {
                    throw new InvalidArgumentException(
                        'You must enter a valid package namespace.',
                    );
                }
            }

            return $data;
        };
    }

    private function namify(): callable
    {
        return function (string $value): string {
            // Replace any invalid characters with a space.
            $value = preg_replace('/[^a-zA-Z0-9_\x80-\xff]/', ' ', (string) $value);
            $valueParts = explode(' ', (string) $value);

            foreach ($valueParts as &$part) {
                // Check the first character to make sure it's valid.
                if (preg_match('/[^a-zA-Z_\x80-\xff]/', $part[0]) === 1) {
                    $part = substr($part, 1);
                }
            }

            // Upper-case the words and separate with namespaces.
            return str_replace(' ', '\\', ucwords(implode(' ', $valueParts)));
        };
    }
}
