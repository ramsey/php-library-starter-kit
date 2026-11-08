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
 * Asks for the email address of the copyright holder
 */
class CopyrightEmail extends Question implements SkippableQuestion, StarterKitQuestion
{
    use AnswersTool;
    use EmailValidatorTool;

    public function getName(): string
    {
        return 'copyrightEmail';
    }

    public function __construct(Answers $answers)
    {
        parent::__construct('What is the copyright holder\'s email address?');

        $this->answers = $answers;
    }

    public function getDefault(): float | bool | int | string | null
    {
        return $this->getAnswers()->copyrightEmail ?? $this->getAnswers()->authorEmail;
    }

    public function shouldSkip(): bool
    {
        return $this->getAnswers()->authorHoldsCopyright === true;
    }
}
