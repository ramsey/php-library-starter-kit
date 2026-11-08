<?php

/**
 * This file is part of ramsey/php-library-starter-kit
 *
 * Copyright © Ben Ramsey <ben@ramsey.dev>
 *
 * Licensed under the MIT License (the "License"); you may not use this file
 * except in compliance with the License. You may obtain a copy of the License
 * at <https://opensource.org/license/MIT>.
 *
 * SPDX-License-Identifier: MIT
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

    /**
     * @return callable(string | null): list<string>
     */
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
