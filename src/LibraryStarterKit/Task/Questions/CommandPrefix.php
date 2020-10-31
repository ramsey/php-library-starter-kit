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

use function preg_match;

use const PHP_EOL;

/**
 * Asks for a prefix to use for custom Composer commands
 */
class CommandPrefix extends Question
{
    private const VALID_PATTERN = '/^[a-zA-Z0-9_\x80-\xff]{1,16}$/';

    public function getName(): string
    {
        return 'commandPrefix';
    }

    public function getQuestion(): string
    {
        $question = 'What command prefix (namespace) would you like to use?' . PHP_EOL;

        $question .= '<comment>';
        $question .= 'Composer supports namespaces (or prefixes) for custom commands. For' . PHP_EOL;
        $question .= 'example, we might choose to use the prefix `vnd\'. If we define a command' . PHP_EOL;
        $question .= 'named `vnd:test\', we can execute it with `composer vnd:test\'. We can' . PHP_EOL;
        $question .= 'list all commands in the `vnd\' namespace with `composer list vnd\'.';
        $question .= '</comment>' . PHP_EOL . PHP_EOL;

        $question .= '<comment>';
        $question .= 'It\'s best to keep this prefix short. Between 1 and 4 characters is good.';
        $question .= '</comment>' . PHP_EOL;

        return $question;
    }

    public function getDefault(): ?string
    {
        return 'vnd';
    }

    public function getValidator(): callable
    {
        return function (string $data): string {
            if (!preg_match(self::VALID_PATTERN, $data)) {
                throw new InvalidArgumentException(
                    'You must enter a valid command prefix.',
                );
            }

            return $data;
        };
    }
}
