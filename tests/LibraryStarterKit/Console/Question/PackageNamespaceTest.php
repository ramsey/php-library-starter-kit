<?php

declare(strict_types=1);

namespace Ramsey\Test\Dev\LibraryStarterKit\Console\Question;

use Ramsey\Dev\LibraryStarterKit\Console\Question\PackageNamespace;
use Ramsey\Dev\LibraryStarterKit\Exception\InvalidConsoleInput;
use Ramsey\Dev\LibraryStarterKit\Task\Answers;

class PackageNamespaceTest extends QuestionTestCase
{
    protected function getTestClass(): string
    {
        return PackageNamespace::class;
    }

    protected function getQuestionName(): string
    {
        return 'packageNamespace';
    }

    protected function getQuestionText(): string
    {
        return 'What is the library\'s root namespace? (e.g., Foo\\Bar\\Baz)';
    }

    public function testDefaultWithEmptyPackageName(): void
    {
        $answers = new Answers();
        $question = new PackageNamespace($answers);

        $answers->packageName = '    ';

        $this->assertNull($question->getDefault());
    }

    public function testDefaultWithPackageName(): void
    {
        $answers = new Answers();
        $question = new PackageNamespace($answers);

        $answers->packageName = 'frodo/fellowship-of-the-ring';

        $this->assertSame('Frodo\\Fellowship\\Of\\The\\Ring', $question->getDefault());
        $this->assertSame('What is the library\'s root namespace?', $question->getQuestion());
    }

    public function testDefaultWithOddlyNamedPackageName(): void
    {
        $answers = new Answers();
        $question = new PackageNamespace($answers);

        $answers->packageName = 'foo/1bar-2baz';

        $this->assertSame('Foo\\Bar\\Baz', $question->getDefault());
        $this->assertSame('What is the library\'s root namespace?', $question->getQuestion());
    }

    public function testValidator(): void
    {
        $validator = (new PackageNamespace(new Answers()))->getValidator();

        $this->assertSame('Foo\\Bar\\Baz\\Quux', $validator('Foo\\Bar\\Baz\\Quux'));
    }

    public function testValidatorThrowsExceptionForInvalidNamespaceName(): void
    {
        $validator = (new PackageNamespace(new Answers()))->getValidator();

        $this->expectException(InvalidConsoleInput::class);
        $this->expectExceptionMessage('You must enter a valid library namespace.');

        $validator('1Foo');
    }
}
