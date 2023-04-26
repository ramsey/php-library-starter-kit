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
