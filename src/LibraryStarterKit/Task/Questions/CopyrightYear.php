<?php

/**
 * This file is part of ramsey/php-library-starter-kit
 *
 * ramsey/php-library-starter-kit is open source software: you can
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

namespace Ramsey\Dev\LibraryStarterKit\Task\Questions;

use InvalidArgumentException;
use Ramsey\Dev\LibraryStarterKit\Task\Question;

use function date;
use function preg_match;
use function trim;

/**
 * Asks for the initial copyright year
 */
class CopyrightYear extends Question
{
    public function getName(): string
    {
        return 'copyrightYear';
    }

    public function getQuestion(): string
    {
        return 'What is the copyright year?';
    }

    public function getDefault(): ?string
    {
        return date('Y');
    }

    public function getValidator(): callable
    {
        return function (string $data): string {
            if (preg_match('/^\d{4}$/', trim($data)) === 1) {
                return $data;
            }

            throw new InvalidArgumentException('You must enter a valid, 4-digit year.');
        };
    }
}
