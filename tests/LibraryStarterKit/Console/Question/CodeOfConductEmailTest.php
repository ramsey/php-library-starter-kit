<?php

declare(strict_types=1);

namespace Ramsey\Test\Dev\LibraryStarterKit\Console\Question;

use PHPUnit\Framework\Attributes\DataProvider;
use Ramsey\Dev\LibraryStarterKit\Console\Question\CodeOfConductEmail;

class CodeOfConductEmailTest extends QuestionTestCase
{
    protected function getTestClass(): string
    {
        return CodeOfConductEmail::class;
    }

    protected function getQuestionName(): string
    {
        return 'codeOfConductEmail';
    }

    protected function getQuestionText(): string
    {
        return 'What email address should people use to report code of conduct issues?';
    }

    #[DataProvider('provideSkipValues')]
    public function testShouldSkip(string $choice, bool $expected): void
    {
        $question = new CodeOfConductEmail($this->answers);

        $this->answers->codeOfConduct = $choice;

        $this->assertSame($expected, $question->shouldSkip());
    }

    /**
     * @return array<array{choice: string, expected: bool}>
     */
    public static function provideSkipValues(): array
    {
        return [
            [
                'choice' => 'None',
                'expected' => true,
            ],
            [
                'choice' => 'Contributor-1.4',
                'expected' => false,
            ],
            [
                'choice' => 'Contributor-2.0',
                'expected' => false,
            ],
            [
                'choice' => 'Contributor-2.1',
                'expected' => false,
            ],
            [
                'choice' => 'Citizen-2.3',
                'expected' => false,
            ],
        ];
    }
}
