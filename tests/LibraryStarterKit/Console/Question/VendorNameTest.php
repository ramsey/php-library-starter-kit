<?php

declare(strict_types=1);

namespace Ramsey\Test\Dev\LibraryStarterKit\Console\Question;

use Ramsey\Dev\LibraryStarterKit\Console\Question\VendorName;
use Ramsey\Dev\LibraryStarterKit\Exception\InvalidConsoleInput;

class VendorNameTest extends QuestionTestCase
{
    protected function getTestClass(): string
    {
        return VendorName::class;
    }

    protected function getQuestionName(): string
    {
        return 'vendorName';
    }

    protected function getQuestionText(): string
    {
        return 'What is your package vendor name?';
    }

    public function testGetDefaultWhenGitHubNameProvided(): void
    {
        $question = new VendorName($this->answers);

        $this->answers->githubUsername = 'foobar';

        $this->assertSame('foobar', $question->getDefault());
    }

    public function testValidatorReturnsValidValue(): void
    {
        $validator = (new VendorName($this->answers))->getValidator();

        $this->assertSame('foo-bar-baz', $validator('foo-bar-baz'));
    }

    /**
     * @dataProvider provideInvalidValues
     */
    public function testValidatorThrowsExceptionForInvalidValue(?string $value): void
    {
        $validator = (new VendorName($this->answers))->getValidator();

        $this->expectException(InvalidConsoleInput::class);
        $this->expectExceptionMessage('You must enter a valid vendor name.');

        $validator($value);
    }

    /**
     * @return mixed[]
     */
    public function provideInvalidValues(): array
    {
        return [
            ['value' => null],
            ['value' => '   '],
            ['value' => 'foo---bar'],
            ['value' => 'foo/bar'],
        ];
    }
}
