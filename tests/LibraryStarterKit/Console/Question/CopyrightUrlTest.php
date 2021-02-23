<?php

declare(strict_types=1);

namespace Ramsey\Test\Dev\LibraryStarterKit\Console\Question;

use Ramsey\Dev\LibraryStarterKit\Console\Question\CopyrightUrl;

class CopyrightUrlTest extends QuestionTestCase
{
    protected function getTestClass(): string
    {
        return CopyrightUrl::class;
    }

    protected function getQuestionName(): string
    {
        return 'copyrightUrl';
    }

    protected function getQuestionText(): string
    {
        return 'What is the copyright holder\'s website address?';
    }

    public function testGetDefaultWhenAuthorNameIsSet(): void
    {
        $question = new CopyrightUrl($this->answers);

        $this->answers->authorUrl = 'https://example.com/copyright';

        $this->assertSame('https://example.com/copyright', $question->getDefault());
    }

    /**
     * @dataProvider provideSkipValues
     */
    public function testShouldSkip(bool $choice, bool $expected): void
    {
        $question = new CopyrightUrl($this->answers);

        $this->answers->authorHoldsCopyright = $choice;

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
