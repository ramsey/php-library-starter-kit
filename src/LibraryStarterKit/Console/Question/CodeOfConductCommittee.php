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
 * Asks for the name of the committee responsible for handling code of conduct issues
 */
class CodeOfConductCommittee extends Question implements SkippableQuestion, StarterKitQuestion
{
    use AnswersTool;

    public function getName(): string
    {
        return 'codeOfConductCommittee';
    }

    public function __construct(Answers $answers)
    {
        parent::__construct(
            'What is the name of your group or committee who oversees code of conduct issues?',
            $answers->codeOfConductCommittee,
        );

        $this->answers = $answers;
    }

    /**
     * @return callable(string | null): (string | null)
     */
    public function getValidator(): callable
    {
        return fn (?string $data): ?string => $data;
    }

    public function shouldSkip(): bool
    {
        // This question is only applicable for the Citizen-2.3 code of conduct.
        return $this->getAnswers()->codeOfConduct !== CodeOfConduct::CHOICE_IDENTIFIER_MAP[5];
    }
}
