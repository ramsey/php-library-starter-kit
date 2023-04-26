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
 * Asks for the URL of the copyright holder
 */
class CopyrightUrl extends Question implements SkippableQuestion, StarterKitQuestion
{
    use AnswersTool;
    use UrlValidatorTool;

    public function getName(): string
    {
        return 'copyrightUrl';
    }

    public function __construct(Answers $answers)
    {
        parent::__construct('What is the copyright holder\'s website address?');

        $this->answers = $answers;
    }

    public function getDefault(): float | bool | int | string | null
    {
        return $this->getAnswers()->copyrightUrl ?? $this->getAnswers()->authorUrl;
    }

    public function shouldSkip(): bool
    {
        return $this->getAnswers()->authorHoldsCopyright === true;
    }
}
