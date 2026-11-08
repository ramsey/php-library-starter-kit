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
use Ramsey\Dev\LibraryStarterKit\Exception\InvalidConsoleInput;
use Symfony\Component\Console\Question\Question;

use function array_filter;
use function array_keys;
use function array_map;
use function array_pop;
use function array_values;
use function explode;
use function implode;
use function in_array;
use function sprintf;
use function trim;

/**
 * Asks which funding platforms to include in the FUNDING.yml file
 *
 * The answer is the whole funding map, with the chosen platform keys in the
 * order of the FundingPlatform cases. A platform that already has a value
 * keeps it, and a new platform starts with an empty one.
 */
class FundingPlatforms extends Question implements KeepsAnswerWhenSkipped, StarterKitQuestion
{
    use AnswersTool;

    public function getName(): string
    {
        return 'funding';
    }

    public function __construct(Answers $answers)
    {
        parent::__construct(
            'Which funding platforms do you want to include? Enter comma-separated platform keys ('
            . implode(', ', FundingPlatform::keys()) . ')',
            implode(',', array_keys($answers->funding ?? [])) ?: null,
        );

        $this->answers = $answers;
    }

    public function shouldSkip(): bool
    {
        return $this->answers->funding === null;
    }

    /**
     * @return callable(string | null): list<string>
     */
    public function getNormalizer(): callable
    {
        return function (?string $answer): array {
            if ($answer === null) {
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

    /**
     * @return callable(list<string>): array<string, string | list<string>>
     */
    public function getValidator(): callable
    {
        return function (array $keys): array {
            /** @var list<string> $keys */
            foreach ($keys as $key) {
                if (FundingPlatform::tryFrom($key) === null) {
                    $all = FundingPlatform::keys();
                    $last = array_pop($all);

                    throw new InvalidConsoleInput(sprintf(
                        '"%s" is not a funding platform; choose from %s, and %s',
                        $key,
                        implode(', ', $all),
                        $last,
                    ));
                }
            }

            $existing = $this->answers->funding ?? [];
            $funding = [];

            foreach (FundingPlatform::keys() as $key) {
                if (in_array($key, $keys, true)) {
                    $funding[$key] = $existing[$key] ?? '';
                }
            }

            return $funding;
        };
    }
}
