<?php

/**
 * This file is part of the ramsey/php-library-skeleton project
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright Copyright (c) Ben Ramsey <ben@benramsey.com>
 * @license http://opensource.org/licenses/MIT MIT
 */

declare(strict_types=1);

namespace Ramsey\Skeleton\Task;

use Composer\IO\IOInterface;
use Ramsey\Skeleton\Task\Questions\InstallQuestions;

/**
 * The `Prompt` task prompts the user for answers that are used to configure
 * the project.
 */
class Prompt extends AbstractTask
{
    /**
     * @var InstallQuestions
     */
    private $questions;

    /**
     * @var array
     */
    private $answers = [];

    /**
     * Sets questions to use when prompting the user.
     *
     * @param InstallQuestions $questions
     *
     * @return Prompt
     */
    public function setQuestions(InstallQuestions $questions): self
    {
        $this->questions = $questions;

        return $this;
    }

    /**
     * Returns questions to prompt the user.
     *
     * @return InstallQuestions
     */
    public function getQuestions(): InstallQuestions
    {
        return $this->questions;
    }

    /**
     * Returns the user's answers to the prompted questions.
     *
     * @return array
     */
    public function getAnswers(): array
    {
        return $this->answers;
    }

    /**
     * Runs the `Prompt` task.
     */
    public function run(): void
    {
        foreach ($this->getQuestions()->getQuestions() as $key => $question) {
            if (isset($question['operation']) && is_callable($question['operation'])) {
                $this->answers[$key] = $question['operation'](
                    $this->getIO(),
                    $question['prompt'],
                    $this->answers,
                    $question['default'] ?? null
                );
            } else {
                $this->answers[$key] = $this->getIO()->ask(
                    $question['prompt'],
                    $question['default'] ?? null
                );
            }
        }
    }
}
