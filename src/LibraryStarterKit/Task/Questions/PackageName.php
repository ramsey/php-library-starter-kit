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

namespace Ramsey\Dev\LibraryStarterKit\Task\Questions;

use InvalidArgumentException;
use Ramsey\Dev\LibraryStarterKit\Task\Question;

use function preg_match;
use function strpos;
use function strtolower;

/**
 * Asks for the package name (i.e., the name to use for this package on Packagist.org)
 */
class PackageName extends Question
{
    private const VALID_PATTERN = '/^[a-z0-9]([_.-]?[a-z0-9]+)*\/[a-z0-9](([_.]?|-{0,2})[a-z0-9]+)*$/';

    public function getName(): string
    {
        return 'packageName';
    }

    public function getQuestion(): string
    {
        return 'What is your package name?';
    }

    public function getDefault(): ?string
    {
        return '{{ vendorName }}/{{ projectName }}';
    }

    public function getPrompt(): string
    {
        $prompt = parent::getPrompt();
        $prompt .= '<fg=cyan>{{ vendorName }}/</>';

        return $prompt;
    }

    public function getValidator(): callable
    {
        return function (string $data): string {
            $vendorPrefix = strtolower(($this->getAnswers()->vendorName ?? '') . '/');
            $data = strtolower($data);

            if (strpos($data, $vendorPrefix) !== 0) {
                $data = $vendorPrefix . $data;
            }

            if (preg_match(self::VALID_PATTERN, $data)) {
                return $data;
            }

            throw new InvalidArgumentException(
                'You must enter a valid package name.',
            );
        };
    }
}
