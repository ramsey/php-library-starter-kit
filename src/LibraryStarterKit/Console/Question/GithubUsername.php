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
 * Asks for the GitHub username or org name of the project owner
 */
class GithubUsername extends Question implements StarterKitQuestion
{
    use AnswersTool;

    public function getName(): string
    {
        return 'githubUsername';
    }

    public function __construct(Answers $answers)
    {
        parent::__construct(
            'What is the GitHub username or org name for your package?',
            $answers->githubUsername,
        );

        $this->answers = $answers;
    }
}
