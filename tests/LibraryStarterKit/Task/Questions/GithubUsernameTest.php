<?php

declare(strict_types=1);

namespace Ramsey\Test\Dev\LibraryStarterKit\Task\Questions;

use Ramsey\Dev\LibraryStarterKit\Task\Questions\GithubUsername;

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
