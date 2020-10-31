<?php

declare(strict_types=1);

namespace Ramsey\Test\Dev\LibraryStarterKit\Task\Questions;

use InvalidArgumentException;
use Ramsey\Dev\LibraryStarterKit\Task\Questions\CopyrightYear;

use function date;

class CopyrightYearTest extends QuestionTestCase
{
    public function getQuestionClass(): string
    {
        return CopyrightYear::class;
    }

    public function testGetQuestion(): void
    {
        $this->assertSame(
            'What is the copyright year?',
            $this->getQuestion()->getQuestion(),
        );
    }

    public function testGetName(): void
    {
        $this->assertSame(
            'copyrightYear',
            $this->getQuestion()->getName(),
        );
    }

    public function testGetDefault(): void
    {
        $this->assertSame(
            date('Y'),
            $this->getQuestion()->getDefault(),
        );
    }

    public function testGetValidatorReturnsCallable(): void
    {
        $this->assertIsCallable($this->getQuestion()->getValidator());
    }

    public function testValidatorReturnsValidData(): void
    {
        $validator = $this->getQuestion()->getValidator();

        $this->assertSame('2020', $validator('2020'));
    }

    public function testValidatorThrowsExceptionForInvalidData(): void
    {
        $validator = $this->getQuestion()->getValidator();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('You must enter a valid, 4-digit year.');

        $validator('foobar');
    }
}
