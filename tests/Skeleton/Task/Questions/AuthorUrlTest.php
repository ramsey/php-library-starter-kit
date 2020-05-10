<?php

declare(strict_types=1);

namespace Ramsey\Test\Skeleton\Task\Questions;

use Ramsey\Skeleton\Task\Questions\AuthorUrl;

class AuthorUrlTest extends QuestionTestCase
{
    public function getQuestionClass(): string
    {
        return AuthorUrl::class;
    }

    public function testGetQuestion(): void
    {
        $this->assertSame(
            'What is your website address?',
            $this->getQuestion()->getQuestion()
        );
    }

    public function testGetName(): void
    {
        $this->assertSame(
            'authorUrl',
            $this->getQuestion()->getName()
        );
    }

    public function testIsOptional(): void
    {
        $this->assertTrue($this->getQuestion()->isOptional());
    }
}
