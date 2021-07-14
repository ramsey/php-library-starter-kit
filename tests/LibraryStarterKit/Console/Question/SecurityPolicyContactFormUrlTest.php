<?php

declare(strict_types=1);

namespace Ramsey\Test\Dev\LibraryStarterKit\Console\Question;

use Ramsey\Dev\LibraryStarterKit\Console\Question\SecurityPolicyContactFormUrl;

class SecurityPolicyContactFormUrlTest extends QuestionTestCase
{
    protected function getTestClass(): string
    {
        return SecurityPolicyContactFormUrl::class;
    }

    protected function getQuestionName(): string
    {
        return 'securityPolicyContactFormUrl';
    }

    protected function getQuestionText(): string
    {
        return 'At what URL should researchers submit vulnerability reports?';
    }

    /**
     * @dataProvider provideSkipValues
     */
    public function testShouldSkip(bool $choice, bool $expected): void
    {
        $question = new SecurityPolicyContactFormUrl($this->answers);

        $this->answers->securityPolicy = $choice;

        $this->assertSame($expected, $question->shouldSkip());
    }

    /**
     * @return mixed[]
     */
    public function provideSkipValues(): array
    {
        return [
            [
                'choice' => false,
                'expected' => true,
            ],
            [
                'choice' => true,
                'expected' => false,
            ],
        ];
    }
}
