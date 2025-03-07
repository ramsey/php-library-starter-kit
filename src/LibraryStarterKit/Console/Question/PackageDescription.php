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
