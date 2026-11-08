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
use Symfony\Component\Console\Question\ConfirmationQuestion;

/**
 * Asks whether the library creator wishes to include a security policy
 */
class SecurityPolicy extends ConfirmationQuestion implements StarterKitQuestion
{
    use AnswersTool;

    public function getName(): string
    {
        return 'securityPolicy';
    }

    public function __construct(Answers $answers)
    {
        parent::__construct(
            'Do you want to include a security policy?',
            $answers->securityPolicy,
        );

        $this->answers = $answers;
    }
}
