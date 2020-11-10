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

namespace Ramsey\Dev\LibraryStarterKit\Console\Question;

use Ramsey\Dev\LibraryStarterKit\Task\Answers;
use Symfony\Component\Console\Question\Question;

use function array_filter;
use function array_map;
use function array_values;
use function explode;
use function trim;

/**
 * Asks for keywords to associate with the library
 */
class PackageKeywords extends Question implements StarterKitQuestion
{
    use AnswersTool;

    public function getName(): string
    {
        return 'packageKeywords';
    }

    public function __construct(Answers $answers)
    {
        parent::__construct('Enter a set of comma-separated keywords describing your library');

        $this->answers = $answers;
    }

    public function getNormalizer(): callable
    {
        return function (?string $answer): array {
            if ($answer === null || trim($answer) === '') {
                return [];
            }

            return array_values(
                array_filter(
                    array_map(
                        fn ($v) => trim((string) $v),
                        explode(',', $answer),
                    ),
                ),
            );
        };
    }
}
