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
 * Asks for a URL that describes governing policies for the code of conduct
 */
class CodeOfConductPoliciesUrl extends Question implements SkippableQuestion, StarterKitQuestion
{
    use AnswersTool;
    use UrlValidatorTool;

    public function getName(): string
    {
        return 'codeOfConductPoliciesUrl';
    }

    public function __construct(Answers $answers)
    {
        parent::__construct(
            'At what URL are your committee\'s governing policies described?',
            $answers->codeOfConductPoliciesUrl,
        );

        $this->answers = $answers;
    }

    public function shouldSkip(): bool
    {
        // This question is only applicable for the Citizen-2.3 code of conduct.
        return $this->getAnswers()->codeOfConduct !== CodeOfConduct::CHOICE_IDENTIFIER_MAP[5];
    }
}
