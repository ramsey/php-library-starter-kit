<?php

declare(strict_types=1);

namespace Ramsey\Test\Dev\LibraryStarterKit\Console\Question;

use Ramsey\Dev\LibraryStarterKit\Answers;
use Ramsey\Dev\LibraryStarterKit\Console\Question\CopyrightHolder;

class CopyrightHolderTest extends QuestionTestCase
{
    protected function getTestClass(): string
    {
        return CopyrightHolder::class;
    }

    protected function getQuestionName(): string
    {
        return 'copyrightHolder';
    }

    protected function getQuestionText(): string
    {
        return 'Who is the copyright holder?';
    }

    public function testGetDefaultWhenAuthorNameIsSet(): void
    {
        $answers = new Answers();
        $question = new CopyrightHolder($answers);

        $answers->authorName = 'Samwise Gamgee';

        $this->assertSame('Samwise Gamgee', $question->getDefault());
    }

    /**
     * @dataProvider provideSkipValues
     */
    public function testShouldSkip(bool $choice, bool $expected): void
    {
        $answers = new Answers();
        $question = new CopyrightHolder($answers);

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
