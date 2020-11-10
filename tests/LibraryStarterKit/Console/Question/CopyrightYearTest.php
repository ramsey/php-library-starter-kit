<?php

declare(strict_types=1);

namespace Ramsey\Test\Dev\LibraryStarterKit\Console\Question;

use Ramsey\Dev\LibraryStarterKit\Console\Question\CopyrightYear;
use Ramsey\Dev\LibraryStarterKit\Exception\InvalidConsoleInput;
use Ramsey\Dev\LibraryStarterKit\Task\Answers;

use function date;

class CopyrightYearTest extends QuestionTestCase
{
    protected function getTestClass(): string
    {
        return CopyrightYear::class;
    }

    protected function getQuestionName(): string
    {
        return 'copyrightYear';
    }

    protected function getQuestionText(): string
    {
        return 'What is the copyright year?';
    }

    public function testGetDefault(): void
    {
        $yearNow = date('Y');
        $question = new CopyrightYear(new Answers());

        $this->assertSame($yearNow, $question->getDefault());
    }

    public function testValidatorReturnsValidValue(): void
    {
        $validator = (new CopyrightYear(new Answers()))->getValidator();

        $this->assertSame('2017', $validator('2017'));
    }

    /**
     * @dataProvider provideInvalidDateValues
     */
    public function testValidatorThrowsExceptionForInvalidValue(?string $value): void
    {
        $validator = (new CopyrightYear(new Answers()))->getValidator();

        $this->expectException(InvalidConsoleInput::class);
        $this->expectExceptionMessage('You must enter a valid, 4-digit year.');

        $validator($value);
    }

    /**
     * @return mixed[]
     */
    public function provideInvalidDateValues(): array
    {
        return [
            ['value' => null],
            ['value' => '19'],
            ['value' => 'foo'],
            ['value' => 'YEAR'],
            ['value' => '997'],
            ['value' => '21201'],
        ];
    }
}
