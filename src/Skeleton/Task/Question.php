<?php

/**
 * This file is part of ramsey/php-library-skeleton
 *
 * ramsey/php-library-skeleton is open source software: you can
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

namespace Ramsey\Skeleton\Task;

use Composer\IO\IOInterface;
use InvalidArgumentException;

use function array_map;
use function implode;
use function is_array;
use function str_replace;
use function strlen;
use function trim;

use const PHP_EOL;

/**
 * Represents a question asked of the user setting up a project
 */
abstract class Question
{
    private IOInterface $io;
    private Answers $answers;

    public function __construct(IOInterface $io, Answers $answers)
    {
        $this->io = $io;
        $this->answers = $answers;
    }

    /**
     * Returns the name of the answer property associated with this question
     */
    abstract public function getName(): string;

    /**
     * Returns the actual question to prompt the user
     */
    abstract public function getQuestion(): string;

    /**
     * Asks the user the question and saves the answer they provide
     */
    public function ask(): void
    {
        if ($this->shouldSkip()) {
            return;
        }

        /** @var mixed $answer */
        $answer = $this->getIO()->askAndValidate(
            $this->replaceTokens($this->getPrompt()),
            $this->getValidator(),
            null,
            $this->replaceTokens((string) $this->getDefault())
        );

        $this->getAnswers()->{$this->getName()} = $answer;
    }

    /**
     * Returns a prompt used to preset the user with a question
     */
    public function getPrompt(): string
    {
        $prompt = PHP_EOL;
        $prompt .= "<fg=cyan>{$this->getQuestion()}</>";
        $prompt .= PHP_EOL;

        if ($this->isOptional()) {
            $prompt .= '<options=bold>optional</> ';
        }

        if ($this->getDefault() !== null) {
            $prompt .= "[<fg=blue>{$this->getDefault()}</>] ";
        }

        $prompt .= '<fg=cyan>> </>';

        return $prompt;
    }

    /**
     * Returns the default answer value or null if there isn't a default
     */
    public function getDefault(): ?string
    {
        return null;
    }

    /**
     * Returns a validation callable
     */
    public function getValidator(): callable
    {
        return function (string $data): string {
            if ($this->isOptional()) {
                return $data;
            }

            if (strlen(trim((string) $data)) > 0) {
                return $data;
            }

            throw new InvalidArgumentException('You must enter a value.');
        };
    }

    /**
     * Returns true if the question should be skipped (based on previous answers)
     */
    public function shouldSkip(): bool
    {
        return false;
    }

    /**
     * Returns true if the answer is optional
     */
    public function isOptional(): bool
    {
        return false;
    }

    /**
     * Returns the IO instance for use with this question
     */
    final public function getIO(): IOInterface
    {
        return $this->io;
    }

    /**
     * Returns the Answers instance for use with this question
     */
    final public function getAnswers(): Answers
    {
        return $this->answers;
    }

    /**
     * Replaces tokens in the given value with previously captured answers
     */
    final public function replaceTokens(string $value): string
    {
        $tokens = array_map(function ($key): string {
            return "{{ {$key} }}";
        }, $this->getAnswers()->getTokens());

        /** @var string[] $replacements */
        $replacements = $this->getAnswers()->getValues();

        /**
         * @var int $index
         * @var mixed $replacement
         */
        foreach ($replacements as $index => $replacement) {
            if (is_array($replacement)) {
                $replacements[$index] = implode(', ', $replacement);
            }
        }

        return str_replace($tokens, $replacements, $value);
    }
}
