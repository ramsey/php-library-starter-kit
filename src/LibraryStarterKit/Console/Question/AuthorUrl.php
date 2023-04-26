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
 * Asks for the URL of the library author
 */
class AuthorUrl extends Question implements StarterKitQuestion
{
    use AnswersTool;
    use UrlValidatorTool;

    public function getName(): string
    {
        return 'authorUrl';
    }

    public function __construct(Answers $answers)
    {
        parent::__construct(
            'What is your website address?',
            $answers->authorUrl,
        );

        $this->answers = $answers;
    }
}
