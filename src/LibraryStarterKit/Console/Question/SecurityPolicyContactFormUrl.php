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
 * Asks for a URL that where security researchers may submit reports
 */
class SecurityPolicyContactFormUrl extends Question implements SkippableQuestion, StarterKitQuestion
{
    use AnswersTool;
    use UrlValidatorTool;

    public function getName(): string
    {
        return 'securityPolicyContactFormUrl';
    }

    public function __construct(Answers $answers)
    {
        parent::__construct(
            'At what URL should researchers submit vulnerability reports?',
            $answers->securityPolicyContactFormUrl,
        );

        $this->answers = $answers;
    }

    public function shouldSkip(): bool
    {
        return $this->getAnswers()->securityPolicy === false;
    }
}
