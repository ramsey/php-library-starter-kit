<?php

declare(strict_types=1);

namespace Ramsey\Test\Dev\LibraryStarterKit\Console\Question;

use PHPUnit\Framework\Attributes\DataProvider;
use Ramsey\Dev\LibraryStarterKit\Console\Question\EmailValidatorTool;
use Ramsey\Dev\LibraryStarterKit\Exception\InvalidConsoleInput;
use Ramsey\Test\Dev\LibraryStarterKit\TestCase;

class EmailValidatorToolTest extends TestCase
{
    #[DataProvider('provideNullableValues')]
    public function testValidatorThrowsExceptionForEmptyValuesWhenQuestionIsNotOptional(?string $value): void
    {
        $question = new class () {
            use EmailValidatorTool;

            public function __construct()
            {
                $this->isOptional = false;
            }
        };

        $validator = $question->getValidator();

        $this->expectException(InvalidConsoleInput::class);
        $this->expectExceptionMessage('You must enter a valid email address.');

        $validator($value);
    }

    #[DataProvider('provideNullableValues')]
    public function testValidatorReturnsNullWhenQuestionIsOptional(?string $value): void
    {
        $question = new class () {
            use EmailValidatorTool;
        };

        $validator = $question->getValidator();

        $this->assertNull($validator($value));
    }

    /**
     * @return list<array{value: string | null}>
     */
    public static function provideNullableValues(): array
    {
        return [
            ['value' => null],
            ['value' => ''],
            ['value' => '    '],
        ];
    }

    public function testValidatorReturnsValueWhenValid(): void
    {
        $question = new class () {
            use EmailValidatorTool;
        };

        $validator = $question->getValidator();

        $this->assertSame('jane@example.com', $validator('jane@example.com'));
    }

    public function testValidatorThrowsExceptionWhenNotValid(): void
    {
        $question = new class () {
            use EmailValidatorTool;
        };

        $validator = $question->getValidator();

        $this->expectException(InvalidConsoleInput::class);
        $this->expectExceptionMessage('You must enter a valid email address.');

        $validator('not-a-valid-address');
    }
}
