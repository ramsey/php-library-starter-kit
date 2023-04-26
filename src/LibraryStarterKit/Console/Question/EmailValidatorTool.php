<?php

/**
 * This file is part of ramsey/php-library-starter-kit
 *
 * @copyright Copyright (c) Ben Ramsey <ben@benramsey.com>
 * @license https://opensource.org/licenses/MIT MIT License
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
