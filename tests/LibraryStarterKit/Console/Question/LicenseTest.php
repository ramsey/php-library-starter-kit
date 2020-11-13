<?php

declare(strict_types=1);

namespace Ramsey\Test\Dev\LibraryStarterKit\Console\Question;

use Ramsey\Dev\LibraryStarterKit\Answers;
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

    /**
     * @inheritDoc
     */
    protected function getQuestionDefault()
    {
        return 1;
    }

    public function testGetChoices(): void
    {
        $question = new License(new Answers());

        $this->assertSame(
            [
                1 => 'Proprietary',
                2 => 'Apache License 2.0',
                3 => 'BSD 2-Clause "Simplified" License',
                4 => 'BSD 3-Clause "New" or "Revised" License',
                5 => 'GNU Affero General Public License v3.0 or later',
                6 => 'GNU General Public License v3.0 or later',
                7 => 'GNU Lesser General Public License v3.0 or later',
                8 => 'MIT License',
                9 => 'MIT No Attribution',
                10 => 'Mozilla Public License 2.0',
                11 => 'Unlicense',
            ],
            $question->getChoices(),
        );
    }

    /**
     * @dataProvider provideNormalizerTestValues
     */
    public function testNormalizer(?string $value, string $expected): void
    {
        $normalizer = (new License(new Answers()))->getNormalizer();

        $this->assertSame($expected, $normalizer($value));
    }

    /**
     * @return mixed[]
     */
    public function provideNormalizerTestValues(): array
    {
        return [
            ['value' => '1', 'expected' => 'Proprietary'],
            ['value' => '2', 'expected' => 'Apache-2.0'],
            ['value' => '3', 'expected' => 'BSD-2-Clause'],
            ['value' => '4', 'expected' => 'BSD-3-Clause'],
            ['value' => '5', 'expected' => 'AGPL-3.0-or-later'],
            ['value' => '6', 'expected' => 'GPL-3.0-or-later'],
            ['value' => '7', 'expected' => 'LGPL-3.0-or-later'],
            ['value' => '8', 'expected' => 'MIT'],
            ['value' => '9', 'expected' => 'MIT-0'],
            ['value' => '10', 'expected' => 'MPL-2.0'],
            ['value' => '11', 'expected' => 'Unlicense'],
            ['value' => 'Proprietary', 'expected' => 'Proprietary'],
            ['value' => 'Apache License 2.0', 'expected' => 'Apache-2.0'],
            ['value' => 'BSD 2-Clause "Simplified" License', 'expected' => 'BSD-2-Clause'],
            ['value' => 'BSD 3-Clause "New" or "Revised" License', 'expected' => 'BSD-3-Clause'],
            ['value' => 'GNU Affero General Public License v3.0 or later', 'expected' => 'AGPL-3.0-or-later'],
            ['value' => 'GNU General Public License v3.0 or later', 'expected' => 'GPL-3.0-or-later'],
            ['value' => 'GNU Lesser General Public License v3.0 or later', 'expected' => 'LGPL-3.0-or-later'],
            ['value' => 'MIT License', 'expected' => 'MIT'],
            ['value' => 'MIT No Attribution', 'expected' => 'MIT-0'],
            ['value' => 'Mozilla Public License 2.0', 'expected' => 'MPL-2.0'],
            ['value' => 'Unlicense', 'expected' => 'Unlicense'],
            ['value' => '12', 'expected' => '12'],
            ['value' => null, 'expected' => ''],
            ['value' => 'foo', 'expected' => 'foo'],
        ];
    }

    /**
     * @dataProvider provideValidValues
     */
    public function testValidator(string $value): void
    {
        $validator = (new License(new Answers()))->getValidator();

        $this->assertSame($value, $validator($value));
    }

    /**
     * @return mixed[]
     */
    public function provideValidValues(): array
    {
        return array_map(fn (string $v): array => [$v], License::CHOICE_IDENTIFIER_MAP);
    }

    /**
     * @dataProvider provideInvalidValues
     */
    public function testValidatorThrowsExceptionForInvalidValues(?string $value, string $message): void
    {
        $validator = (new License(new Answers()))->getValidator();

        $this->expectException(InvalidConsoleInput::class);
        $this->expectExceptionMessage($message);

        $validator($value);
    }

    /**
     * @return mixed[]
     */
    public function provideInvalidValues(): array
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
                'value' => '12',
                'message' => '"12" is not a valid license choice.',
            ],
        ];
    }
}
