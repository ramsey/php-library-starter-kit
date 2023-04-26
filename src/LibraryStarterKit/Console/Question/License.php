<?php

/**
 * This file is part of ramsey/php-library-starter-kit
 *
 * @copyright Copyright (c) Ben Ramsey <ben@benramsey.com>
 * @license https://opensource.org/licenses/MIT MIT License
 */

declare(strict_types=1);

namespace Ramsey\Dev\LibraryStarterKit\Console\Question;

use Ramsey\Dev\LibraryStarterKit\Answers;
use Ramsey\Dev\LibraryStarterKit\Exception\InvalidConsoleInput;
use Symfony\Component\Console\Question\ChoiceQuestion;

use function array_combine;
use function array_search;
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

    public const DEFAULT = 'Proprietary';

    public const CHOICES = [
        1 => self::DEFAULT,
        2 => 'Apache License 2.0',
        3 => 'BSD 2-Clause "Simplified" License',
        4 => 'BSD 3-Clause "New" or "Revised" License',
        5 => 'Creative Commons Zero v1.0 Universal',
        6 => 'GNU Affero General Public License v3.0 or later',
        7 => 'GNU General Public License v3.0 or later',
        8 => 'GNU Lesser General Public License v3.0 or later',
        9 => 'Hippocratic License 2.1',
        10 => 'MIT License',
        11 => 'MIT No Attribution',
        12 => 'Mozilla Public License 2.0',
        13 => 'Unlicense',
    ];

    public const CHOICE_IDENTIFIER_MAP = [
        1 => self::DEFAULT,
        2 => 'Apache-2.0',
        3 => 'BSD-2-Clause',
        4 => 'BSD-3-Clause',
        5 => 'CC0-1.0',
        6 => 'AGPL-3.0-or-later',
        7 => 'GPL-3.0-or-later',
        8 => 'LGPL-3.0-or-later',
        9 => 'Hippocratic-2.1',
        10 => 'MIT',
        11 => 'MIT-0',
        12 => 'MPL-2.0',
        13 => 'Unlicense',
    ];

    public function getName(): string
    {
        return 'license';
    }

    public function __construct(Answers $answers)
    {
        parent::__construct(
            'Choose a license for your project',
            self::CHOICES,
            array_search($answers->license, self::CHOICE_IDENTIFIER_MAP) ?: 1,
        );

        $this->answers = $answers;
    }

    public function getNormalizer(): callable
    {
        $choiceMap = array_combine(self::CHOICES, self::CHOICE_IDENTIFIER_MAP);

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
