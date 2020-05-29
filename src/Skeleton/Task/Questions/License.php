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
 * Asks the user what license they wish to use for this project
 */
class License extends Question
{
    public const CHOICES = [
        1 => 'Proprietary',
        2 => 'Apache License 2.0',
        3 => 'BSD 2-Clause "Simplified" License',
        4 => 'BSD 3-Clause "New" or "Revised" License',
        5 => 'GNU Affero General Public License v3.0 or later',
        6 => 'GNU General Public License v3.0 or later',
        7 => 'GNU Lesser General Public License v3.0 or later',
        8 => 'MIT License',
        9 => 'MIT No Attribution',
        10 => 'Mozilla Public License 2.0',
        11 => 'Unlicense',
    ];

    public const CHOICE_IDENTIFIER_MAP = [
        1 => 'Proprietary',
        2 => 'Apache-2.0',
        3 => 'BSD-2-Clause',
        4 => 'BSD-3-Clause',
        5 => 'AGPL-3.0-or-later',
        6 => 'GPL-3.0-or-later',
        7 => 'LGPL-3.0-or-later',
        8 => 'MIT',
        9 => 'MIT-0',
        10 => 'MPL-2.0',
        11 => 'Unlicense',
    ];

    public function getName(): string
    {
        return 'license';
    }

    public function getQuestion(): string
    {
        return 'Choose a license for your project.';
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
            (string) $this->getDefault()
        );

        if (is_array($answer)) {
            $answer = (string) ($answer[0] ?? $this->getDefault());
        }

        $this->getAnswers()->license = self::CHOICE_IDENTIFIER_MAP[(int) $answer];
    }

    public function getPrompt(): string
    {
        $prompt = PHP_EOL;
        $prompt .= "<fg=cyan>{$this->getQuestion()}</>";
        $prompt .= " [<fg=blue>{$this->getDefault()}</>]";

        return $prompt;
    }
}
