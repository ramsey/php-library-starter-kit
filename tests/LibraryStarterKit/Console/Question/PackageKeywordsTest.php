<?php

declare(strict_types=1);

namespace Ramsey\Test\Dev\LibraryStarterKit\Console\Question;

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

    /**
     * @param string[] $expected
     *
     * @dataProvider provideNormalizerTestValues
     */
    public function testNormalizer(?string $value, array $expected): void
    {
        $normalizer = (new PackageKeywords($this->answers))->getNormalizer();

        $this->assertSame($expected, $normalizer($value));
    }

    /**
     * @return mixed[]
     */
    public function provideNormalizerTestValues(): array
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
