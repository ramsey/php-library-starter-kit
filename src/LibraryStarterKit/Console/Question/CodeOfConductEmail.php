<?php

/**
 * This file is part of ramsey/php-library-starter-kit
 *
 * ramsey/php-library-starter-kit is open source software: you can
 * distribute it and/or modify it under the terms of the MIT License
 * (the "License"). You may not use this file except in
 * compliance with the License.
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or
 * implied. See the License for the specific language governing
 * permissions and limitations under the License.
 *
 * @copyright Copyright (c) Ben Ramsey <ben@benramsey.com>
 * @license https://opensource.org/licenses/MIT MIT License
 */

declare(strict_types=1);

namespace Ramsey\Dev\LibraryStarterKit\Console\Question;

use Ramsey\Dev\LibraryStarterKit\Task\Answers;
use Symfony\Component\Console\Question\Question;

/**
 * Asks for the email address to which code of conduct issues should be submitted
 */
class CodeOfConductEmail extends Question implements SkippableQuestion, StarterKitQuestion
{
    use AnswersTool;
    use EmailValidatorTool;

    public function getName(): string
    {
        return 'codeOfConductEmail';
    }

    public function __construct(Answers $answers)
    {
        parent::__construct('What email address should people use to report code of conduct issues?');

        $this->answers = $answers;
    }

    public function shouldSkip(): bool
    {
        // Skip if codeOfConduct is `null` (i.e., "None").
        return $this->getAnswers()->codeOfConduct === null;
    }
}
