<?php

declare(strict_types=1);

namespace Ramsey\Test\Dev\LibraryStarterKit\Console\Question;

use PHPUnit\Framework\Attributes\DataProvider;
use Ramsey\Dev\LibraryStarterKit\Console\Question\CopyrightEmail;

class CopyrightEmailTest extends QuestionTestCase
{
    protected function getTestClass(): string
    {
        return CopyrightEmail::class;
    }

    protected function getQuestionName(): string
    {
        return 'copyrightEmail';
    }

    protected function getQuestionText(): string
    {
        return 'What is the copyright holder\'s email address?';
    }

    public function testGetDefaultWhenAuthorEmailIsSet(): void
    {
        $question = new CopyrightEmail($this->answers);

        $this->answers->authorEmail = 'samwise@example.com';

        $this->assertSame('samwise@example.com', $question->getDefault());
    }

    #[DataProvider('provideSkipValues')]
    public function testShouldSkip(bool $choice, bool $expected): void
    {
        $question = new CopyrightEmail($this->answers);

        $this->answers->authorHoldsCopyright = $choice;

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
                'expected' => false,
            ],
            [
                'choice' => true,
                'expected' => true,
            ],
        ];
    }
}
