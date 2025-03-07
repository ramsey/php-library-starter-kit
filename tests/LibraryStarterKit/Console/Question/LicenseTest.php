<?php

declare(strict_types=1);

namespace Ramsey\Test\Dev\LibraryStarterKit\Console\Question;

use PHPUnit\Framework\Attributes\DataProvider;
use Ramsey\Dev\LibraryStarterKit\Console\Question\License;
use Ramsey\Dev\LibraryStarterKit\Exception\InvalidConsoleInput;

use function array_map;

class LicenseTest extends QuestionTestCase
{
    protected function getTestClass(): string
    {
        return License::class;
    }

    protected function getQuestionName(): string
    {
        return 'license';
    }

    protected function getQuestionText(): string
    {
        return 'Choose a license for your project';
    }

    protected function getQuestionDefault(): int
    {
        return 1;
    }

    public function testGetDefaultWhenAnswerAlreadySet(): void
    {
        $this->answers->license = 'MIT-0';

        $question = new License($this->answers);

        $this->assertSame(11, $question->getDefault());
    }

    public function testGetChoices(): void
    {
        $question = new License($this->answers);

        $this->assertSame(
            [
                1 => 'Proprietary',
                2 => 'Apache License 2.0',
                3 => 'BSD 2-Clause "Simplified" License',
                4 => 'BSD 3-Clause "New" or "Revised" License',
                5 => 'Creative Commons Zero v1.0 Universal',
                6 => 'GNU Affero General Public License v3.0 or later',
                7 => 'GNU General Public License v3.0 or later',
                8 => 'GNU Lesser General Public License v3.0 or later',
                9 => 'Hippocratic License 2.1',
                10 => 'MIT License',
                11 => 'MIT No Attribution',
                12 => 'Mozilla Public License 2.0',
                13 => 'Unlicense',
            ],
            $question->getChoices(),
        );
    }

    #[DataProvider('provideNormalizerTestValues')]
    public function testNormalizer(?string $value, string $expected): void
    {
        $normalizer = (new License($this->answers))->getNormalizer();

        $this->assertSame($expected, $normalizer($value));
    }

    /**
     * @return list<array{value: string | null, expected: string}>
     */
    public static function provideNormalizerTestValues(): array
    {
        return [
            ['value' => '1', 'expected' => 'Proprietary'],
            ['value' => '2', 'expected' => 'Apache-2.0'],
            ['value' => '3', 'expected' => 'BSD-2-Clause'],
            ['value' => '4', 'expected' => 'BSD-3-Clause'],
            ['value' => '5', 'expected' => 'CC0-1.0'],
            ['value' => '6', 'expected' => 'AGPL-3.0-or-later'],
            ['value' => '7', 'expected' => 'GPL-3.0-or-later'],
            ['value' => '8', 'expected' => 'LGPL-3.0-or-later'],
            ['value' => '9', 'expected' => 'Hippocratic-2.1'],
            ['value' => '10', 'expected' => 'MIT'],
            ['value' => '11', 'expected' => 'MIT-0'],
            ['value' => '12', 'expected' => 'MPL-2.0'],
            ['value' => '13', 'expected' => 'Unlicense'],
            ['value' => 'Proprietary', 'expected' => 'Proprietary'],
            ['value' => 'Apache License 2.0', 'expected' => 'Apache-2.0'],
            ['value' => 'BSD 2-Clause "Simplified" License', 'expected' => 'BSD-2-Clause'],
            ['value' => 'BSD 3-Clause "New" or "Revised" License', 'expected' => 'BSD-3-Clause'],
            ['value' => 'Creative Commons Zero v1.0 Universal', 'expected' => 'CC0-1.0'],
            ['value' => 'GNU Affero General Public License v3.0 or later', 'expected' => 'AGPL-3.0-or-later'],
            ['value' => 'GNU General Public License v3.0 or later', 'expected' => 'GPL-3.0-or-later'],
            ['value' => 'GNU Lesser General Public License v3.0 or later', 'expected' => 'LGPL-3.0-or-later'],
            ['value' => 'Hippocratic License 2.1', 'expected' => 'Hippocratic-2.1'],
            ['value' => 'MIT License', 'expected' => 'MIT'],
            ['value' => 'MIT No Attribution', 'expected' => 'MIT-0'],
            ['value' => 'Mozilla Public License 2.0', 'expected' => 'MPL-2.0'],
            ['value' => 'Unlicense', 'expected' => 'Unlicense'],
            ['value' => '14', 'expected' => '14'],
            ['value' => null, 'expected' => ''],
            ['value' => 'foo', 'expected' => 'foo'],
        ];
    }

    #[DataProvider('provideValidValues')]
    public function testValidator(string $value): void
    {
        $validator = (new License($this->answers))->getValidator();

        $this->assertSame($value, $validator($value));
    }

    /**
     * @return array<array{0: string}>
     */
    public static function provideValidValues(): array
    {
        return array_map(fn (string $v): array => [$v], License::CHOICE_IDENTIFIER_MAP);
    }

    #[DataProvider('provideInvalidValues')]
    public function testValidatorThrowsExceptionForInvalidValues(?string $value, string $message): void
    {
        $validator = (new License($this->answers))->getValidator();

        $this->expectException(InvalidConsoleInput::class);
        $this->expectExceptionMessage($message);

        $validator($value);
    }

    /**
     * @return array<array{value: string | null, message: string}>
     */
    public static function provideInvalidValues(): array
    {
        return [
            [
                'value' => null,
                'message' => '"" is not a valid license choice.',
            ],
            [
                'value' => '   ',
                'message' => '"   " is not a valid license choice.',
            ],
            [
                'value' => 'foo',
                'message' => '"foo" is not a valid license choice.',
            ],
            [
                'value' => '14',
                'message' => '"14" is not a valid license choice.',
            ],
        ];
    }
}
