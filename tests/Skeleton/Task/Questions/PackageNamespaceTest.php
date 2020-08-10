<?php

declare(strict_types=1);

namespace Ramsey\Test\Skeleton\Task\Questions;

use InvalidArgumentException;
use Ramsey\Skeleton\Task\Questions\PackageNamespace;

class PackageNamespaceTest extends QuestionTestCase
{
    public function getQuestionClass(): string
    {
        return PackageNamespace::class;
    }

    public function testGetQuestion(): void
    {
        $this->assertSame(
            'What is the package\'s root namespace? (e.g., Foo\\Bar\\Baz)',
            $this->getQuestion()->getQuestion(),
        );
    }

    public function testGetName(): void
    {
        $this->assertSame(
            'packageNamespace',
            $this->getQuestion()->getName(),
        );
    }

    public function testGetDefaultWithoutPackageName(): void
    {
        $this->assertNull($this->getQuestion()->getDefault());
    }

    /**
     * @dataProvider packageNameAndNamespaceProvider
     */
    public function testGetDefaultWithPackageName(string $packageName, string $expectedNamespace): void
    {
        $this->getQuestion()->getAnswers()->packageName = $packageName;

        $this->assertSame($expectedNamespace, $this->getQuestion()->getDefault());
    }

    /**
     * @return array<mixed>
     */
    public function packageNameAndNamespaceProvider(): array
    {
        return [
            [
                'packageName' => 'ramsey/php-library-skeleton',
                'expectedNamespace' => 'Ramsey\\Php\\Library\\Skeleton',
            ],
            [
                'packageName' => '1invalid/2namespace-3name',
                'expectedNamespace' => 'Invalid\\Namespace\\Name',
            ],
        ];
    }

    /**
     * @dataProvider provideDataForValidation
     */
    public function testValidate(
        string $packageNamespace,
        bool $shouldReceiveException,
        ?string $expected
    ): void {
        $validator = $this->getQuestion()->getValidator();

        $this->assertIsCallable($validator);

        if ($shouldReceiveException) {
            $this->expectException(InvalidArgumentException::class);
            $this->expectExceptionMessage('You must enter a valid package namespace.');
        }

        $data = $validator($packageNamespace);

        if (!$shouldReceiveException) {
            $this->assertSame($expected, $data);
        }
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function provideDataForValidation(): array
    {
        return [
            [
                'packageNamespace' => '',
                'shouldReceiveException' => true,
                'expected' => null,
            ],
            [
                'packageNamespace' => '1Foo\\Bar\\Baz',
                'shouldReceiveException' => true,
                'expected' => null,
            ],
            [
                'packageNamespace' => 'Foo\\2Bar\\Baz',
                'shouldReceiveException' => true,
                'expected' => null,
            ],
            [
                'packageNamespace' => 'Foo\\Bar\\3Baz',
                'shouldReceiveException' => true,
                'expected' => null,
            ],
            [
                'packageNamespace' => '\\Foo\\Bar\\Baz',
                'shouldReceiveException' => true,
                'expected' => null,
            ],
            [
                'packageNamespace' => 'Foo\\Bar\\Baz',
                'shouldReceiveException' => false,
                'expected' => 'Foo\\Bar\\Baz',
            ],
            [
                'packageNamespace' => 'Foo',
                'shouldReceiveException' => false,
                'expected' => 'Foo',
            ],
        ];
    }
}
