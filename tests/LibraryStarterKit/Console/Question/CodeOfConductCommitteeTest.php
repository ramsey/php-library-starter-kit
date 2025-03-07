<?php

declare(strict_types=1);

namespace Ramsey\Test\Dev\LibraryStarterKit\Console\Question;

use PHPUnit\Framework\Attributes\DataProvider;
use Ramsey\Dev\LibraryStarterKit\Console\Question\CodeOfConductCommittee;

class CodeOfConductCommitteeTest extends QuestionTestCase
{
    protected function getTestClass(): string
    {
        return CodeOfConductCommittee::class;
    }

    protected function getQuestionName(): string
    {
        return 'codeOfConductCommittee';
    }

    protected function getQuestionText(): string
    {
        return 'What is the name of your group or committee who oversees code of conduct issues?';
    }

    public function testValidator(): void
    {
        $validator = (new CodeOfConductCommittee($this->answers))->getValidator();

        $this->assertSame('The Big Committee', $validator('The Big Committee'));
        $this->assertNull($validator(null));
    }

    #[DataProvider('provideSkipValues')]
    public function testShouldSkip(string $choice, bool $expected): void
    {
        $question = new CodeOfConductCommittee($this->answers);

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
