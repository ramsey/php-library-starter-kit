<?php

declare(strict_types=1);

namespace Ramsey\Test\Dev\LibraryStarterKit\Task\Questions;

use Composer\IO\IOInterface;
use Mockery\MockInterface;
use Ramsey\Dev\LibraryStarterKit\Task\Answers;
use Ramsey\Dev\LibraryStarterKit\Task\Question;
use Ramsey\Test\Dev\LibraryStarterKit\TestCase;

abstract class QuestionTestCase extends TestCase
{
    private Question $question;

    /**
     * @return class-string<Question>
     */
    abstract public function getQuestionClass(): string;

    public function setUp(): void
    {
        /** @var IOInterface & MockInterface $io */
        $io = $this->mockery(IOInterface::class);
        $answers = new Answers();

        /** @var class-string<Question> $questionClass */
        $questionClass = $this->getQuestionClass();

        $this->question = new $questionClass($io, $answers);
    }

    final public function getQuestion(): Question
    {
        return $this->question;
    }
}
