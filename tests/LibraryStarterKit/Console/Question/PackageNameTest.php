<?php

declare(strict_types=1);

namespace Ramsey\Test\Dev\LibraryStarterKit\Console\Question;

use Ramsey\Dev\LibraryStarterKit\Console\Question\PackageName;
use Ramsey\Dev\LibraryStarterKit\Exception\InvalidConsoleInput;
use Ramsey\Dev\LibraryStarterKit\Task\Answers;

class PackageNameTest extends QuestionTestCase
{
    protected function getTestClass(): string
    {
        return PackageName::class;
    }

    protected function getQuestionName(): string
    {
        return 'packageName';
    }

    protected function getQuestionText(): string
    {
        return 'What is your package name?';
    }

    public function testDefaultWithVendorAndProjectNames(): void
    {
        $answers = new Answers();
        $question = new PackageName($answers);

        $answers->vendorName = 'frodo';
        $answers->projectName = 'fellowship';

        $this->assertSame('frodo/fellowship', $question->getDefault());
    }

    public function testValidator(): void
    {
        $validator = (new PackageName(new Answers()))->getValidator();

        $this->assertSame('foo/bar', $validator('foo/bar'));
    }

    public function testValidatorWithVendorPrefix(): void
    {
        $answers = new Answers();
        $validator = (new PackageName($answers))->getValidator();

        $answers->vendorName = 'frodo';

        $this->assertSame('frodo/fellowship-of-the-ring', $validator('frodo/fellowship-of-the-ring'));
    }

    public function testValidatorWithoutVendorPrefix(): void
    {
        $answers = new Answers();
        $validator = (new PackageName($answers))->getValidator();

        $answers->vendorName = 'frodo';

        $this->assertSame('frodo/fellowship_ring', $validator('fellowship_ring'));
    }

    /**
     * @dataProvider provideInvalidPackageNames
     */
    public function testValidatorThrowsExceptionForInvalidPackageNames(?string $value, ?string $vendorName): void
    {
        $answers = new Answers();
        $validator = (new PackageName($answers))->getValidator();

        $answers->vendorName = $vendorName;

        $this->expectException(InvalidConsoleInput::class);
        $this->expectExceptionMessage('You must enter a valid package name.');

        $validator($value);
    }

    /**
     * @return mixed[]
     */
    public function provideInvalidPackageNames(): array
    {
        return [
            [
                'value' => null,
                'vendorName' => null,
            ],
            [
                'value' => '    ',
                'vendorName' => null,
            ],
            [
                'value' => 'foo/bar/baz',
                'vendorName' => null,
            ],
            [
                'value' => null,
                'vendorName' => 'foo',
            ],
            [
                'value' => 'bar/baz',
                'vendorName' => 'foo',
            ],
            [
                'value' => 'bar---baz',
                'vendorName' => 'foo',
            ],
            [
                'value' => '_bar',
                'vendorName' => 'foo',
            ],
            [
                'value' => 'bar',
                'vendorName' => '-foo',
            ],
        ];
    }
}
