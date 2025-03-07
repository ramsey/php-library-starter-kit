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

/**
 * A starter kit question
 *
 * Except {@see self::getAnswers()} and {@see self::getName()}, all methods on
 * this interface are from {@see \Symfony\Component\Console\Question\Question}.
 */
interface StarterKitQuestion
{
    public function getAnswers(): Answers;

    public function getName(): string;

    public function getQuestion(): string;

    public function getDefault(): string | bool | int | float | null;

    public function isMultiline(): bool;

    public function setMultiline(bool $multiline): Question;

    public function isHidden(): bool;

    public function setHidden(bool $hidden): Question;

    public function isHiddenFallback(): bool;

    public function setHiddenFallback(bool $fallback): Question;

    /**
     * @return iterable<string> | null
     */
    public function getAutocompleterValues(): ?iterable;

    /**
     * @param iterable<string> | null $values
     */
    public function setAutocompleterValues(?iterable $values): Question;

    public function getAutocompleterCallback(): ?callable;

    public function setAutocompleterCallback(?callable $callback): Question;

    public function setValidator(?callable $validator): Question;

    public function getValidator(): ?callable;

    public function setMaxAttempts(?int $attempts): Question;

    public function getMaxAttempts(): ?int;

    public function setNormalizer(callable $normalizer): Question;

    public function getNormalizer(): ?callable;

    public function isTrimmable(): bool;

    public function setTrimmable(bool $trimmable): Question;
}
