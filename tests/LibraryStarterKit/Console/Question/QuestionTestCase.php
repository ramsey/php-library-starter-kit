<?php

declare(strict_types=1);

namespace Ramsey\Test\Dev\LibraryStarterKit\Console\Question;

use Ramsey\Test\Dev\LibraryStarterKit\TestCase;

abstract class QuestionTestCase extends TestCase
{
    abstract protected function getTestClass(): string;

    abstract protected function getQuestionName(): string;

    abstract protected function getQuestionText(): string;

    /**
     * @return mixed
     */
    protected function getQuestionDefault()
    {
        return null;
    }

    public function testGetName(): void
    {
        $questionClass = $this->getTestClass();
        $question = new $questionClass($this->answers);

        $this->assertSame($this->getQuestionName(), $question->getName());
    }

    public function testGetQuestion(): void
    {
        $questionClass = $this->getTestClass();
        $question = new $questionClass($this->answers);

        $this->assertSame($this->getQuestionText(), $question->getQuestion());
    }

    public function testGetAnswers(): void
    {
        $questionClass = $this->getTestClass();
        $question = new $questionClass($this->answers);

        $this->assertSame($this->answers, $question->getAnswers());
    }

    public function testGetDefault(): void
    {
        $questionClass = $this->getTestClass();
        $question = new $questionClass($this->answers);

        $this->assertSame($this->getQuestionDefault(), $question->getDefault());
    }
}
