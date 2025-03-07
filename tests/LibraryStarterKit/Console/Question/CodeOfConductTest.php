<?php

declare(strict_types=1);

namespace Ramsey\Test\Dev\LibraryStarterKit\Console\Question;

use PHPUnit\Framework\Attributes\DataProvider;
use Ramsey\Dev\LibraryStarterKit\Console\Question\CodeOfConduct;
use Ramsey\Dev\LibraryStarterKit\Exception\InvalidConsoleInput;

class CodeOfConductTest extends QuestionTestCase
{
    protected function getTestClass(): string
    {
        return CodeOfConduct::class;
    }

    protected function getQuestionName(): string
    {
        return 'codeOfConduct';
    }

    protected function getQuestionText(): string
    {
        return 'Choose a code of conduct for your project';
    }

    protected function getQuestionDefault(): int
    {
        return 1;
    }

    public function testGetDefaultWhenAnswerAlreadySet(): void
    {
        $this->answers->codeOfConduct = 'Contributor-2.0';

        $question = new CodeOfConduct($this->answers);

        $this->assertSame(3, $question->getDefault());
    }

    public function testGetChoices(): void
    {
        $question = new CodeOfConduct($this->answers);

        $this->assertSame(
            [
                1 => 'None',
                2 => 'Contributor Covenant Code of Conduct, version 1.4',
                3 => 'Contributor Covenant Code of Conduct, version 2.0',
                4 => 'Contributor Covenant Code of Conduct, version 2.1',
                5 => 'Citizen Code of Conduct, version 2.3',
            ],
            $question->getChoices(),
        );
    }

    #[DataProvider('provideNormalizerTestValues')]
    public function testNormalizer(?string $value, string $expected): void
    {
        $normalizer = (new CodeOfConduct($this->answers))->getNormalizer();

        $this->assertSame($expected, $normalizer($value));
    }

    /**
     * @return list<array{value: string | null, expected: string}>
     */
    public static function provideNormalizerTestValues(): array
    {
        return [
            ['value' => '1', 'expected' => 'None'],
            ['value' => '2', 'expected' => 'Contributor-1.4'],
            ['value' => '3', 'expected' => 'Contributor-2.0'],
            ['value' => '4', 'expected' => 'Contributor-2.1'],
            ['value' => '5', 'expected' => 'Citizen-2.3'],
            ['value' => 'None', 'expected' => 'None'],
            ['value' => 'Contributor Covenant Code of Conduct, version 1.4', 'expected' => 'Contributor-1.4'],
            ['value' => 'Contributor Covenant Code of Conduct, version 2.0', 'expected' => 'Contributor-2.0'],
            ['value' => 'Contributor Covenant Code of Conduct, version 2.1', 'expected' => 'Contributor-2.1'],
            ['value' => 'Citizen Code of Conduct, version 2.3', 'expected' => 'Citizen-2.3'],
            ['value' => '6', 'expected' => '6'],
            ['value' => null, 'expected' => ''],
            ['value' => 'foo', 'expected' => 'foo'],
        ];
    }

    #[DataProvider('provideValidValues')]
    public function testValidator(string $value, ?string $expected): void
    {
        $validator = (new CodeOfConduct($this->answers))->getValidator();

        $this->assertSame($expected, $validator($value));
    }

    /**
     * @return list<array{value: string, expected: string | null}>
     */
    public static function provideValidValues(): array
    {
        return [
            [
                'value' => 'None',
                'expected' => 'None',
            ],
            [
                'value' => 'Contributor-1.4',
                'expected' => 'Contributor-1.4',
            ],
            [
                'value' => 'Contributor-2.0',
                'expected' => 'Contributor-2.0',
            ],
            [
                'value' => 'Contributor-2.1',
                'expected' => 'Contributor-2.1',
            ],
            [
                'value' => 'Citizen-2.3',
                'expected' => 'Citizen-2.3',
            ],
        ];
    }

    #[DataProvider('provideInvalidValues')]
    public function testValidatorThrowsExceptionForInvalidValues(?string $value, string $message): void
    {
        $validator = (new CodeOfConduct($this->answers))->getValidator();

        $this->expectException(InvalidConsoleInput::class);
        $this->expectExceptionMessage($message);

        $validator($value);
    }

    /**
     * @return list<array{value: string | null, message: string}>
     */
    public static function provideInvalidValues(): array
    {
        return [
            [
                'value' => null,
                'message' => '"" is not a valid code of conduct choice.',
            ],
            [
                'value' => '   ',
                'message' => '"   " is not a valid code of conduct choice.',
            ],
            [
                'value' => 'foo',
                'message' => '"foo" is not a valid code of conduct choice.',
            ],
            [
                'value' => '6',
                'message' => '"6" is not a valid code of conduct choice.',
            ],
        ];
    }
}
