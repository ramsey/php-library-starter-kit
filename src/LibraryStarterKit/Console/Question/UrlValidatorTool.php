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
use function str_starts_with;
use function trim;

use const FILTER_VALIDATE_URL;

/**
 * Common URL validation functionality
 */
trait UrlValidatorTool
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

            if (
                filter_var((string) $data, FILTER_VALIDATE_URL)
                && str_starts_with((string) $data, 'http')
            ) {
                return $data;
            }

            throw new InvalidConsoleInput(
                'You must enter a valid URL, beginning with "http://" or "https://".',
            );
        };
    }
}
