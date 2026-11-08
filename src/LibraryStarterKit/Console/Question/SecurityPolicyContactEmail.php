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
 * Asks for the email address to which researchers should submit vulnerability reports
 */
class SecurityPolicyContactEmail extends Question implements SkippableQuestion, StarterKitQuestion
{
    use AnswersTool;
    use EmailValidatorTool;

    public function getName(): string
    {
        return 'securityPolicyContactEmail';
    }

    public function __construct(Answers $answers)
    {
        parent::__construct(
            'What email address should researchers use to submit vulnerability reports?',
            $answers->securityPolicyContactEmail,
        );

        $this->answers = $answers;
    }

    public function shouldSkip(): bool
    {
        return $this->getAnswers()->securityPolicy === false;
    }
}
