<?php

declare(strict_types=1);

namespace Ramsey\Test\Dev\LibraryStarterKit\Console\Question;

use PHPUnit\Framework\Attributes\DataProvider;
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
        $question = new CopyrightHolder($this->answers);

        $this->answers->authorName = 'Samwise Gamgee';

        $this->assertSame('Samwise Gamgee', $question->getDefault());
    }

    #[DataProvider('provideSkipValues')]
    public function testShouldSkip(bool $choice, bool $expected): void
    {
        $question = new CopyrightHolder($this->answers);

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
