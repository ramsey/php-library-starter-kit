<?php

declare(strict_types=1);

namespace Ramsey\Test\Dev\LibraryStarterKit\Console\Question;

use PHPUnit\Framework\Attributes\DataProvider;
use Ramsey\Dev\LibraryStarterKit\Console\Question\Funding;

class FundingTest extends QuestionTestCase
{
    protected function getTestClass(): string
    {
        return Funding::class;
    }

    protected function getQuestionName(): string
    {
        return 'funding';
    }

    protected function getQuestionText(): string
    {
        return 'Do you want a FUNDING.yml file for a GitHub sponsor button?';
    }

    protected function getQuestionDefault(): bool
    {
        return false;
    }

    public function testGetDefaultWhenAnswerAlreadySet(): void
    {
        $this->answers->funding = ['github' => ['ramsey']];

        $this->assertTrue((new Funding($this->answers))->getDefault());
    }

    /**
     * @param array<string, string | list<string>> | null $existing
     * @param array<string, string | list<string>> | null $expected
     */
    #[DataProvider('confirmations')]
    public function testNormalizerReturnsAMapForYesAndNullForNo(mixed $answer, ?array $existing, ?array $expected): void
    {
        $this->answers->funding = $existing;
        $normalizer = (new Funding($this->answers))->getNormalizer();

        $this->assertNotNull($normalizer);
        $this->assertSame($expected, $normalizer($answer));
    }

    /**
     * @return iterable<string, array{answer: mixed, existing: array<string, string | list<string>> | null, expected: array<string, string | list<string>> | null}>
     */
    public static function confirmations(): iterable
    {
        yield 'yes with nothing yet' => ['answer' => 'yes', 'existing' => null, 'expected' => []];
        yield 'y keeps the existing map' => [
            'answer' => 'y',
            'existing' => ['github' => ['ramsey']],
            'expected' => ['github' => ['ramsey']],
        ];
        yield 'no' => ['answer' => 'no', 'existing' => ['github' => ['ramsey']], 'expected' => null];
        yield 'enter with no default is no' => ['answer' => '', 'existing' => null, 'expected' => null];
        yield 'enter with a default is yes' => [
            'answer' => '',
            'existing' => ['github' => ['ramsey']],
            'expected' => ['github' => ['ramsey']],
        ];
        yield 'boolean true' => ['answer' => true, 'existing' => null, 'expected' => []];
        yield 'boolean false' => ['answer' => false, 'existing' => ['github' => ['ramsey']], 'expected' => null];
    }
}
