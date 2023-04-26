<?php

declare(strict_types=1);

namespace Ramsey\Test\Dev\LibraryStarterKit\Console\Question;

use PHPUnit\Framework\Attributes\DataProvider;
use Ramsey\Dev\LibraryStarterKit\Console\Question\SecurityPolicyContactEmail;

class SecurityPolicyContactEmailTest extends QuestionTestCase
{
    protected function getTestClass(): string
    {
        return SecurityPolicyContactEmail::class;
    }

    protected function getQuestionName(): string
    {
        return 'securityPolicyContactEmail';
    }

    protected function getQuestionText(): string
    {
        return 'What email address should researchers use to submit vulnerability reports?';
    }

    #[DataProvider('provideSkipValues')]
    public function testShouldSkip(bool $choice, bool $expected): void
    {
        $question = new SecurityPolicyContactEmail($this->answers);

        $this->answers->securityPolicy = $choice;

        $this->assertSame($expected, $question->shouldSkip());
    }

    /**
     * @return array<array{choice: bool, expected: bool}>
     */
    public static function provideSkipValues(): array
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
