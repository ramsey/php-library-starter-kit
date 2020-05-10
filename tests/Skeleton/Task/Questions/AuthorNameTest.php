<?php

declare(strict_types=1);

namespace Ramsey\Test\Skeleton\Task\Questions;

use Ramsey\Skeleton\Task\Questions\AuthorName;

class AuthorNameTest extends QuestionTestCase
{
    public function getQuestionClass(): string
    {
        return AuthorName::class;
    }

    public function testGetQuestion(): void
    {
        $this->assertSame(
            'What is your name?',
            $this->getQuestion()->getQuestion()
        );
    }

    public function testGetName(): void
    {
        $this->assertSame(
            'authorName',
            $this->getQuestion()->getName()
        );
    }
}
