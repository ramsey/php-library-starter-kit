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

/**
 * Asks for keywords to associate with the library
 */
class PackageKeywords extends Question
{
    public function getName(): string
    {
        return 'packageKeywords';
    }

    public function getQuestion(): string
    {
        return 'Enter a set of comma-separated keywords describing your library.';
    }

    public function isOptional(): bool
    {
        return true;
    }

    public function ask(): void
    {
        $answer = $this->getIO()->ask($this->getPrompt());

        if (trim((string) $answer) === '') {
            return;
        }

        $answer = explode(',', $answer);

        $answer = array_map(function ($value) {
            return trim((string) $value);
        }, $answer);

        $this->getAnswers()->packageKeywords = $answer;
    }
}
