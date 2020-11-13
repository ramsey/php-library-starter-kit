<?php

declare(strict_types=1);

namespace Ramsey\Test\Dev\LibraryStarterKit\Console\Question;

use Ramsey\Dev\LibraryStarterKit\Answers;
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
        $answers = new Answers();
        $question = new CopyrightEmail($answers);

        $answers->authorEmail = 'samwise@example.com';

        $this->assertSame('samwise@example.com', $question->getDefault());
    }

    /**
     * @dataProvider provideSkipValues
     */
    public function testShouldSkip(bool $choice, bool $expected): void
    {
        $answers = new Answers();
        $question = new CopyrightEmail($answers);

        $answers->authorHoldsCopyright = $choice;

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
                'expected' => false,
            ],
            [
                'choice' => true,
                'expected' => true,
            ],
        ];
    }
}
