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

use Ramsey\Dev\LibraryStarterKit\Exception\InvalidConsoleInput;

use function filter_var;
use function trim;

use const FILTER_VALIDATE_EMAIL;

/**
 * Common question email validation functionality
 */
trait EmailValidatorTool
{
    private bool $isOptional = true;

    /**
     * @return callable(string | null): (string | null)
     */
    public function getValidator(): callable
    {
        return function (?string $data): ?string {
            if ($this->isOptional && ($data === null || trim($data) === '')) {
                return null;
            }

            if (filter_var($data, FILTER_VALIDATE_EMAIL)) {
                return $data;
            }

            throw new InvalidConsoleInput('You must enter a valid email address.');
        };
    }
}
