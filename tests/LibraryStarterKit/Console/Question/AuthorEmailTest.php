<?php

declare(strict_types=1);

namespace Ramsey\Test\Dev\LibraryStarterKit\Console\Question;

use PHPUnit\Framework\Attributes\DataProvider;
use Ramsey\Dev\LibraryStarterKit\Console\Question\AuthorEmail;
use Ramsey\Dev\LibraryStarterKit\Exception\InvalidConsoleInput;

class AuthorEmailTest extends QuestionTestCase
{
    protected function getTestClass(): string
    {
        return AuthorEmail::class;
    }

    protected function getQuestionName(): string
    {
        return 'authorEmail';
    }

    protected function getQuestionText(): string
    {
        return 'What is your email address?';
    }

    #[DataProvider('provideEmptyValues')]
    public function testValidatorRequiresAnEmailAddress(?string $value): void
    {
        $validator = (new AuthorEmail($this->answers))->getValidator();

        $this->expectException(InvalidConsoleInput::class);
        $this->expectExceptionMessage('You must enter a valid email address.');

        $validator($value);
    }

    /**
     * @return list<array{value: string | null}>
     */
    public static function provideEmptyValues(): array
    {
        return [
            ['value' => null],
            ['value' => ''],
            ['value' => '   '],
        ];
    }
}
