<?php

declare(strict_types=1);

namespace Ramsey\Test\Dev\LibraryStarterKit\Console\Question;

use AssertionError;
use PHPUnit\Framework\Attributes\DataProvider;
use Ramsey\Dev\LibraryStarterKit\Console\Question\PackageName;
use Ramsey\Dev\LibraryStarterKit\Exception\InvalidConsoleInput;

use function ini_get;

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
        $question = new PackageName($this->answers);

        $this->answers->vendorName = 'frodo';
        $this->answers->projectName = 'fellowship';

        $this->assertSame('frodo/fellowship', $question->getDefault());
    }

    public function testValidator(): void
    {
        $validator = (new PackageName($this->answers))->getValidator();

        $this->assertSame('foo/bar', $validator('foo/bar'));
    }

    public function testValidatorWithVendorPrefix(): void
    {
        $validator = (new PackageName($this->answers))->getValidator();

        $this->answers->vendorName = 'frodo';

        $this->assertSame('frodo/fellowship-of-the-ring', $validator('frodo/fellowship-of-the-ring'));
    }

    public function testValidatorWithoutVendorPrefix(): void
    {
        $validator = (new PackageName($this->answers))->getValidator();

        $this->answers->vendorName = 'frodo';

        $this->assertSame('frodo/fellowship_ring', $validator('fellowship_ring'));
    }

    #[DataProvider('provideInvalidPackageNames')]
    public function testValidatorThrowsExceptionForInvalidPackageNames(?string $value, ?string $vendorName): void
    {
        $validator = (new PackageName($this->answers))->getValidator();

        $this->answers->vendorName = $vendorName;

        $this->expectException(InvalidConsoleInput::class);
        $this->expectExceptionMessage('You must enter a valid package name.');

        $validator($value);
    }

    /**
     * @return list<array{value: string | null, vendorName: string | null}>
     */
    public static function provideInvalidPackageNames(): array
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

    public function testDefaultWithBlankVendorNameAndNoProjectName(): void
    {
        $question = new PackageName($this->answers);

        $this->answers->vendorName = ' ';

        $this->assertNull($question->getDefault());
    }

    public function testValidatorLowercasesValue(): void
    {
        $validator = (new PackageName($this->answers))->getValidator();

        $this->assertSame('foo/bar', $validator('Foo/BAR'));
    }

    public function testValidatorLowercasesVendorPrefix(): void
    {
        $validator = (new PackageName($this->answers))->getValidator();

        $this->answers->vendorName = 'Frodo';

        $this->assertSame('frodo/ring', $validator('frodo/ring'));
        $this->assertSame('frodo/ring', $validator('ring'));
    }

    public function testValidatorRejectsValueThatIsNotAString(): void
    {
        if (ini_get('zend.assertions') !== '1') {
            $this->markTestSkipped('Assertions are not enabled.');
        }

        $validator = (new PackageName($this->answers))->getValidator();

        $this->expectException(AssertionError::class);

        $validator(123);
    }
}
