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

use Ramsey\Dev\LibraryStarterKit\Exception\InvalidConsoleInput;
use Ramsey\Dev\LibraryStarterKit\Task\Answers;
use Symfony\Component\Console\Question\ChoiceQuestion;

use function array_combine;
use function ctype_digit;
use function in_array;
use function sprintf;
use function trim;

/**
 * Asks the user what license they wish to use for this project
 */
class License extends ChoiceQuestion implements StarterKitQuestion
{
    use AnswersTool;

    public const CHOICES = [
        1 => 'Proprietary',
        2 => 'Apache License 2.0',
        3 => 'BSD 2-Clause "Simplified" License',
        4 => 'BSD 3-Clause "New" or "Revised" License',
        5 => 'GNU Affero General Public License v3.0 or later',
        6 => 'GNU General Public License v3.0 or later',
        7 => 'GNU Lesser General Public License v3.0 or later',
        8 => 'MIT License',
        9 => 'MIT No Attribution',
        10 => 'Mozilla Public License 2.0',
        11 => 'Unlicense',
    ];

    public const CHOICE_IDENTIFIER_MAP = [
        1 => 'Proprietary',
        2 => 'Apache-2.0',
        3 => 'BSD-2-Clause',
        4 => 'BSD-3-Clause',
        5 => 'AGPL-3.0-or-later',
        6 => 'GPL-3.0-or-later',
        7 => 'LGPL-3.0-or-later',
        8 => 'MIT',
        9 => 'MIT-0',
        10 => 'MPL-2.0',
        11 => 'Unlicense',
    ];

    public function getName(): string
    {
        return 'license';
    }

    public function __construct(Answers $answers)
    {
        parent::__construct('Choose a license for your project', self::CHOICES, 1);

        $this->answers = $answers;
    }

    public function getNormalizer(): callable
    {
        $choiceMap = (array) array_combine(self::CHOICES, self::CHOICE_IDENTIFIER_MAP);

        return function (?string $value) use ($choiceMap): string {
            if (ctype_digit((string) $value)) {
                return (string) (self::CHOICE_IDENTIFIER_MAP[(int) $value] ?? $value);
            }

            return (string) ($choiceMap[trim((string) $value)] ?? $value);
        };
    }

    public function getValidator(): callable
    {
        return function (?string $value): string {
            if (!in_array($value, self::CHOICE_IDENTIFIER_MAP)) {
                throw new InvalidConsoleInput(sprintf(
                    '"%s" is not a valid license choice.',
                    (string) $value,
                ));
            }

            return (string) $value;
        };
    }
}
