<?php

declare(strict_types=1);

namespace Ramsey\Test\Dev\LibraryStarterKit\Console\Question;

use PHPUnit\Framework\Attributes\DataProvider;
use Ramsey\Dev\LibraryStarterKit\Console\Question\PackageKeywords;

class PackageKeywordsTest extends QuestionTestCase
{
    protected function getTestClass(): string
    {
        return PackageKeywords::class;
    }

    protected function getQuestionName(): string
    {
        return 'packageKeywords';
    }

    protected function getQuestionText(): string
    {
        return 'Enter a set of comma-separated keywords describing your library';
    }

    public function testGetDefaultWhenAnswerAlreadySet(): void
    {
        $this->answers->packageKeywords = ['foo', 'bar', 'baz'];

        $question = new PackageKeywords($this->answers);

        $this->assertSame('foo,bar,baz', $question->getDefault());
    }

    /**
     * @param string[] $expected
     */
    #[DataProvider('provideNormalizerTestValues')]
    public function testNormalizer(?string $value, array $expected): void
    {
        $normalizer = (new PackageKeywords($this->answers))->getNormalizer();

        $this->assertSame($expected, $normalizer($value));
    }

    /**
     * @return list<array{value: string | null, expected: string[]}>
     */
    public static function provideNormalizerTestValues(): array
    {
        return [
            [
                'value' => null,
                'expected' => [],
            ],
            [
                'value' => '    ',
                'expected' => [],
            ],
            [
                'value' => 'foo, bar , ,   ,,,,   ,baz ,,quux,,',
                'expected' => [
                    'foo',
                    'bar',
                    'baz',
                    'quux',
                ],
            ],
        ];
    }
}
