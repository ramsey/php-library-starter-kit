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
 * Asks for a brief description of the library
 */
class PackageDescription extends Question implements StarterKitQuestion
{
    use AnswersTool;

    public function getName(): string
    {
        return 'packageDescription';
    }

    public function __construct(Answers $answers)
    {
        parent::__construct(
            'Enter a brief description of your library',
            $answers->packageDescription,
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
}
