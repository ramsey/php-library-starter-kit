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
use Symfony\Component\Console\Question\Question;

/**
 * Asks for the name of the library author
 */
class AuthorName extends Question implements StarterKitQuestion
{
    use AnswersTool;

    public function getName(): string
    {
        return 'authorName';
    }

    public function __construct(Answers $answers)
    {
        parent::__construct(
            'What is your name?',
            $answers->authorName,
        );

        $this->answers = $answers;
    }
}
