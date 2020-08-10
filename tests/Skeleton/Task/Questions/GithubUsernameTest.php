<?php

declare(strict_types=1);

namespace Ramsey\Test\Skeleton\Task\Questions;

use Ramsey\Skeleton\Task\Questions\GithubUsername;

class GithubUsernameTest extends QuestionTestCase
{
    public function getQuestionClass(): string
    {
        return GithubUsername::class;
    }

    public function testGetQuestion(): void
    {
        $this->assertSame(
            'What is the GitHub username or org name for your package?',
            $this->getQuestion()->getQuestion(),
        );
    }

    public function testGetName(): void
    {
        $this->assertSame(
            'githubUsername',
            $this->getQuestion()->getName(),
        );
    }
}
