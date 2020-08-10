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

namespace Ramsey\Skeleton\Task\Questions;

use Ramsey\Skeleton\Task\Question;

use const PHP_EOL;

/**
 * Asks for the creator to select a code of conduct for the project
 */
class CodeOfConduct extends Question
{
    public const CHOICES = [
        1 => 'None',
        2 => 'Contributor Covenant Code of Conduct, version 1.4',
        3 => 'Contributor Covenant Code of Conduct, version 2.0',
        4 => 'Citizen Code of Conduct, version 2.3',
    ];

    public const CHOICE_IDENTIFIER_MAP = [
        1 => null,
        2 => 'Contributor-1.4',
        3 => 'Contributor-2.0',
        4 => 'Citizen-2.3',
    ];

    public function getName(): string
    {
        return 'codeOfConduct';
    }

    public function getQuestion(): string
    {
        return 'Choose a code of conduct for your project.';
    }

    public function getDefault(): ?string
    {
        return '1';
    }

    public function ask(): void
    {
        /** @var string|int|array<string|int> $answer */
        $answer = $this->getIO()->select(
            $this->replaceTokens($this->getPrompt()),
            self::CHOICES,
            (string) $this->getDefault(),
        );

        if (is_array($answer)) {
            $answer = (string) ($answer[0] ?? $this->getDefault());
        }

        $this->getAnswers()->codeOfConduct = self::CHOICE_IDENTIFIER_MAP[(int) $answer];
    }

    public function getPrompt(): string
    {
        $prompt = PHP_EOL;
        $prompt .= "<fg=cyan>{$this->getQuestion()}</>";
        $prompt .= " [<fg=blue>{$this->getDefault()}</>]";

        return $prompt;
    }
}
