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
use Symfony\Component\Console\Question\ChoiceQuestion;

use function array_key_exists;
use function explode;
use function implode;
use function in_array;
use function is_string;
use function sprintf;
use function strcasecmp;
use function trim;

/**
 * Asks which funding platforms to include in the FUNDING.yml file
 *
 * The platforms are listed by number, and people may enter numbers, names, or
 * platform keys, separated by commas. The answer is the whole funding map,
 * with the chosen platform keys in the order of the FundingPlatform cases. A
 * platform that already has a value keeps it, and a new platform starts with
 * an empty one.
 */
class FundingPlatforms extends ChoiceQuestion implements KeepsAnswerWhenSkipped, StarterKitQuestion
{
    use AnswersTool;

    public function getName(): string
    {
        return 'funding';
    }

    public function __construct(Answers $answers)
    {
        $choices = [];
        $default = [];

        foreach (FundingPlatform::cases() as $index => $platform) {
            $choices[$index + 1] = $platform->label();

            if (array_key_exists($platform->value, $answers->funding ?? [])) {
                $default[] = $index + 1;
            }
        }

        parent::__construct(
            'Which funding platforms do you want to include?',
            $choices,
            implode(',', $default) ?: null,
        );

        $this->setMultiselect(true);

        $this->answers = $answers;
    }

    public function shouldSkip(): bool
    {
        return $this->answers->funding === null;
    }

    /**
     * Returns the platform key for each entry, or the entry itself when it
     * names no platform
     *
     * @return callable(string | null): list<string>
     */
    public function getNormalizer(): callable
    {
        return function (?string $answer): array {
            $keys = [];

            foreach (explode(',', $answer ?? '') as $entry) {
                $entry = trim($entry);

                if ($entry !== '') {
                    $keys[] = $this->findKey($entry);
                }
            }

            return $keys;
        };
    }

    /**
     * @return callable(string | list<string>): array<string, string | list<string>>
     */
    public function getValidator(): callable
    {
        return function (string | array $keys): array {
            /** @var string | list<string> $keys */
            if (is_string($keys)) {
                $keys = ($this->getNormalizer())($keys);
            }

            if ($keys === []) {
                throw new InvalidConsoleInput('Choose at least one funding platform');
            }

            foreach ($keys as $key) {
                if (FundingPlatform::tryFrom($key) === null) {
                    throw new InvalidConsoleInput(sprintf(
                        '"%s" is not a funding platform; enter numbers or names from the list',
                        $key,
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

    /**
     * Returns the key of the platform an entry names by its number in the
     * list, its name, or its key, ignoring case
     */
    private function findKey(string $entry): string
    {
        foreach (FundingPlatform::cases() as $index => $platform) {
            if (
                $entry === (string) ($index + 1)
                || strcasecmp($entry, $platform->label()) === 0
                || strcasecmp($entry, $platform->value) === 0
            ) {
                return $platform->value;
            }
        }

        return $entry;
    }
}
