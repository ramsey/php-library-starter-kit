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

namespace Ramsey\Dev\LibraryStarterKit\Task;

use Ramsey\Dev\LibraryStarterKit\Console\Question\SkippableQuestion;
use Ramsey\Dev\LibraryStarterKit\Console\Question\StarterKitQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Represents a user prompt
 */
class Prompt
{
    private Answers $answers;
    private InstallQuestions $questions;

    public function __construct(InstallQuestions $questions, Answers $answers)
    {
        $this->questions = $questions;
        $this->answers = $answers;
    }

    /**
     * Executes the prompt, asking the user each question
     */
    public function run(SymfonyStyle $console): void
    {
        /**
         * @var Question & StarterKitQuestion $question
         */
        foreach ($this->questions->getQuestions($this->answers) as $question) {
            if ($question instanceof SkippableQuestion && $question->shouldSkip()) {
                $this->answers->{$question->getName()} = $question->getDefault();

                continue;
            }

            $this->answers->{$question->getName()} = $console->askQuestion($question);
        }
    }
}
