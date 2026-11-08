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
use function array_key_exists;
use function array_map;
use function array_values;
use function count;
use function explode;
use function filter_var;
use function implode;
use function is_array;
use function ltrim;
use function preg_match;
use function sprintf;
use function trim;

use const FILTER_VALIDATE_URL;

/**
 * Asks for the value of one chosen funding platform
 *
 * The answer is the whole funding map, with this platform's value set, or its
 * key removed when the value is empty.
 */
class FundingPlatformValue extends Question implements SkippableQuestion, StarterKitQuestion
{
    use AnswersTool;

    private const MAX_LIST_ENTRIES = 4;

    public function getName(): string
    {
        return 'funding';
    }

    public function __construct(Answers $answers, public readonly FundingPlatform $platform)
    {
        $value = $answers->funding[$platform->value] ?? null;

        parent::__construct(
            $platform->prompt(),
            (is_array($value) ? implode(',', $value) : $value) ?: null,
        );

        $this->answers = $answers;
    }

    public function shouldSkip(): bool
    {
        return !array_key_exists($this->platform->value, $this->answers->funding ?? []);
    }

    /**
     * @return callable(string | null): (string | list<string>)
     */
    public function getNormalizer(): callable
    {
        return fn (?string $answer): string | array => $this->clean(
            $this->platform->acceptsList() ? explode(',', $answer ?? '') : ($answer ?? ''),
        );
    }

    /**
     * @return callable(string | list<string>): array<string, string | list<string>>
     */
    public function getValidator(): callable
    {
        return function (string | array $value): array {
            /** @var string | list<string> $value */
            $funding = $this->answers->funding ?? [];
            $value = $this->shapeValue($this->clean($value));

            if ($value === '' || $value === []) {
                unset($funding[$this->platform->value]);

                return $funding;
            }

            $this->checkValue($value);

            $funding[$this->platform->value] = $value;

            return $funding;
        };
    }

    /**
     * Trims every entry, drops empty entries, and removes a leading "@" from
     * each GitHub Sponsors username
     *
     * A string given to a platform that accepts a list is one entry.
     *
     * @param string | list<string> $value
     *
     * @return string | list<string>
     */
    private function clean(string | array $value): string | array
    {
        if (!is_array($value)) {
            if (!$this->platform->acceptsList()) {
                return trim($value);
            }

            $value = [$value];
        }

        $entries = array_map(fn (string $entry): string => trim($entry), $value);

        if ($this->platform === FundingPlatform::Github) {
            $entries = array_map(fn (string $entry): string => trim(ltrim($entry, '@')), $entries);
        }

        return array_values(array_filter($entries, fn (string $entry): bool => $entry !== ''));
    }

    /**
     * Returns the value in the shape the platform takes: a list for a platform
     * that accepts one, and a string for a platform that does not
     *
     * @param string | list<string> $value
     *
     * @return string | list<string>
     */
    private function shapeValue(string | array $value): string | array
    {
        if ($this->platform->acceptsList()) {
            return is_array($value) ? $value : ($value === '' ? [] : [$value]);
        }

        if (!is_array($value)) {
            return $value;
        }

        if (count($value) > 1) {
            throw new InvalidConsoleInput(sprintf('Enter only one %s entry', $this->platform->label()));
        }

        return $value[0] ?? '';
    }

    /**
     * @param string | list<string> $value
     */
    private function checkValue(string | array $value): void
    {
        if (is_array($value)) {
            if (count($value) > self::MAX_LIST_ENTRIES) {
                throw new InvalidConsoleInput(sprintf(
                    'Enter no more than four %s entries',
                    $this->platform->label(),
                ));
            }

            if ($this->platform === FundingPlatform::Custom) {
                foreach ($value as $url) {
                    $this->checkUrl($url);
                }
            }

            return;
        }

        if ($this->platform === FundingPlatform::Tidelift) {
            if (preg_match('#^(npm|pypi|rubygems|maven|packagist|nuget)/\S+\z#', $value) !== 1) {
                throw new InvalidConsoleInput(sprintf(
                    'A Tidelift value is PLATFORM-NAME/PACKAGE-NAME, where the platform is npm, pypi, rubygems, '
                    . 'maven, packagist, or nuget; "%s" given',
                    $value,
                ));
            }
        } elseif ($this->platform === FundingPlatform::ThanksDev) {
            if (preg_match('#^u/gh/\S+\z#', $value) !== 1) {
                throw new InvalidConsoleInput(sprintf(
                    'A thanks.dev value is u/gh/USERNAME; "%s" given',
                    $value,
                ));
            }
        }
    }

    private function checkUrl(string $url): void
    {
        if (preg_match('#^https?://#', $url) === 1 && filter_var($url, FILTER_VALIDATE_URL) !== false) {
            return;
        }

        throw new InvalidConsoleInput(sprintf(
            '"%s" is not a URL beginning with "http://" or "https://"',
            $url,
        ));
    }
}
