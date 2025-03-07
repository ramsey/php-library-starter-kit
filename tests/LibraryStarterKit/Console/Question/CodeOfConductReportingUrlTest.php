<?php

declare(strict_types=1);

namespace Ramsey\Test\Dev\LibraryStarterKit\Console\Question;

use PHPUnit\Framework\Attributes\DataProvider;
use Ramsey\Dev\LibraryStarterKit\Console\Question\CodeOfConductReportingUrl;

class CodeOfConductReportingUrlTest extends QuestionTestCase
{
    protected function getTestClass(): string
    {
        return CodeOfConductReportingUrl::class;
    }

    protected function getQuestionName(): string
    {
        return 'codeOfConductReportingUrl';
    }

    protected function getQuestionText(): string
    {
        return 'At what URL are your code of conduct reporting guidelines described?';
    }

    #[DataProvider('provideSkipValues')]
    public function testShouldSkip(string $choice, bool $expected): void
    {
        $question = new CodeOfConductReportingUrl($this->answers);

        $this->answers->codeOfConduct = $choice;

        $this->assertSame($expected, $question->shouldSkip());
    }

    /**
     * @return list<array{choice: string, expected: bool}>
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
                'expected' => true,
            ],
            [
                'choice' => 'Contributor-2.0',
                'expected' => true,
            ],
            [
                'choice' => 'Contributor-2.1',
                'expected' => true,
            ],
            [
                'choice' => 'Citizen-2.3',
                'expected' => false,
            ],
        ];
    }
}
