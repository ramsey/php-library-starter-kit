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
 * Asks for the creator to select a code of conduct for the project
 */
class CodeOfConduct extends ChoiceQuestion implements StarterKitQuestion
{
    use AnswersTool;

    public const DEFAULT = 'None';

    public const CHOICES = [
        1 => self::DEFAULT,
        2 => 'Contributor Covenant Code of Conduct, version 1.4',
        3 => 'Contributor Covenant Code of Conduct, version 2.0',
        4 => 'Contributor Covenant Code of Conduct, version 2.1',
        5 => 'Citizen Code of Conduct, version 2.3',
    ];

    public const CHOICE_IDENTIFIER_MAP = [
        1 => self::DEFAULT,
        2 => 'Contributor-1.4',
        3 => 'Contributor-2.0',
        4 => 'Contributor-2.1',
        5 => 'Citizen-2.3',
    ];

    public function getName(): string
    {
        return 'codeOfConduct';
    }

    public function __construct(Answers $answers)
    {
        parent::__construct(
            'Choose a code of conduct for your project',
            self::CHOICES,
            array_search($answers->codeOfConduct, self::CHOICE_IDENTIFIER_MAP) ?: 1,
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
        return function (?string $value): ?string {
            if (!in_array($value, self::CHOICE_IDENTIFIER_MAP)) {
                throw new InvalidConsoleInput(sprintf(
                    '"%s" is not a valid code of conduct choice.',
                    (string) $value,
                ));
            }

            return $value;
        };
    }
}
