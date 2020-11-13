<?php

declare(strict_types=1);

namespace Ramsey\Test\Dev\LibraryStarterKit\Console\Question;

use Ramsey\Dev\LibraryStarterKit\Answers;
use Ramsey\Dev\LibraryStarterKit\Console\Question\CodeOfConductPoliciesUrl;

class CodeOfConductPoliciesUrlTest extends QuestionTestCase
{
    protected function getTestClass(): string
    {
        return CodeOfConductPoliciesUrl::class;
    }

    protected function getQuestionName(): string
    {
        return 'codeOfConductPoliciesUrl';
    }

    protected function getQuestionText(): string
    {
        return 'At what URL are your committee\'s governing policies described?';
    }

    /**
     * @dataProvider provideSkipValues
     */
    public function testShouldSkip(?string $choice, bool $expected): void
    {
        $answers = new Answers();
        $question = new CodeOfConductPoliciesUrl($answers);

        $answers->codeOfConduct = $choice;

        $this->assertSame($expected, $question->shouldSkip());
    }

    /**
     * @return mixed[]
     */
    public function provideSkipValues(): array
    {
        return [
            [
                'choice' => null,
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
                'choice' => 'Citizen-2.3',
                'expected' => false,
            ],
        ];
    }
}
