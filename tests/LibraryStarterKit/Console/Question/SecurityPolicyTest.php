<?php

declare(strict_types=1);

namespace Ramsey\Test\Dev\LibraryStarterKit\Console\Question;

use Ramsey\Dev\LibraryStarterKit\Console\Question\SecurityPolicy;

class SecurityPolicyTest extends QuestionTestCase
{
    protected function getTestClass(): string
    {
        return SecurityPolicy::class;
    }

    protected function getQuestionName(): string
    {
        return 'securityPolicy';
    }

    protected function getQuestionText(): string
    {
        return 'Do you want to include a security policy?';
    }

    /**
     * @inheritDoc
     */
    protected function getQuestionDefault()
    {
        return true;
    }

    public function testGetDefaultWhenAnswerAlreadySet(): void
    {
        $this->answers->securityPolicy = false;

        $question = new SecurityPolicy($this->answers);

        $this->assertFalse($question->getDefault());
    }
}
