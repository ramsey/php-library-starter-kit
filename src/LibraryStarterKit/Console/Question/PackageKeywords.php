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
use Symfony\Component\Console\Question\Question;

use function array_filter;
use function array_map;
use function array_values;
use function explode;
use function implode;
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
        parent::__construct(
            'Enter a set of comma-separated keywords describing your library',
            implode(',', $answers->packageKeywords) ?: null,
        );

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
                        fn ($v) => trim($v),
                        explode(',', $answer),
                    ),
                ),
            );
        };
    }
}
