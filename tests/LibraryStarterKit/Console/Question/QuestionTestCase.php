<?php

declare(strict_types=1);

namespace Ramsey\Test\Dev\LibraryStarterKit\Console\Question;

use Ramsey\Dev\LibraryStarterKit\Console\Question\StarterKitQuestion;
use Ramsey\Test\Dev\LibraryStarterKit\TestCase;
use Symfony\Component\Console\Question\Question;

abstract class QuestionTestCase extends TestCase
{
    /**
     * @return class-string<StarterKitQuestion & Question>
     */
    abstract protected function getTestClass(): string;

    abstract protected function getQuestionName(): string;

    abstract protected function getQuestionText(): string;

    protected function getQuestionDefault(): mixed
    {
        return null;
    }

    public function testGetName(): void
    {
        $questionClass = $this->getTestClass();

        /** @phpstan-ignore-next-line */
        $question = new $questionClass($this->answers);

        $this->assertSame($this->getQuestionName(), $question->getName());
    }

    public function testGetQuestion(): void
    {
        $questionClass = $this->getTestClass();

        /** @phpstan-ignore-next-line */
        $question = new $questionClass($this->answers);

        $this->assertSame($this->getQuestionText(), $question->getQuestion());
    }

    public function testGetAnswers(): void
    {
        $questionClass = $this->getTestClass();

        /** @phpstan-ignore-next-line */
        $question = new $questionClass($this->answers);

        $this->assertSame($this->answers, $question->getAnswers());
    }

    public function testGetDefault(): void
    {
        $questionClass = $this->getTestClass();

        /** @phpstan-ignore-next-line */
        $question = new $questionClass($this->answers);

        $this->assertSame($this->getQuestionDefault(), $question->getDefault());
    }

    public function testGetDefaultWhenAnswerAlreadySet(): void
    {
        $this->answers->{$this->getQuestionName()} = 'foobar';

        $questionClass = $this->getTestClass();

        /** @phpstan-ignore-next-line */
        $question = new $questionClass($this->answers);

        $this->assertSame('foobar', $question->getDefault());
    }
}
