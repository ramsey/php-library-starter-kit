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
