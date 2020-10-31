<?php

declare(strict_types=1);

namespace Ramsey\Test\Dev\LibraryStarterKit\Task\Questions;

use InvalidArgumentException;
use Ramsey\Dev\LibraryStarterKit\Task\Questions\VendorName;

class VendorNameTest extends QuestionTestCase
{
    public function getQuestionClass(): string
    {
        return VendorName::class;
    }

    public function testGetQuestion(): void
    {
        $this->assertSame(
            'What is your package vendor name?',
            $this->getQuestion()->getQuestion(),
        );
    }

    public function testGetName(): void
    {
        $this->assertSame(
            'vendorName',
            $this->getQuestion()->getName(),
        );
    }

    public function testGetDefault(): void
    {
        $this->assertSame(
            '{{ githubUsername }}',
            $this->getQuestion()->getDefault(),
        );
    }

    /**
     * @dataProvider provideDataForValidation
     */
    public function testValidate(
        string $vendorName,
        bool $shouldReceiveException,
        ?string $expected
    ): void {
        $validator = $this->getQuestion()->getValidator();

        $this->assertIsCallable($validator);

        if ($shouldReceiveException) {
            $this->expectException(InvalidArgumentException::class);
            $this->expectExceptionMessage('You must enter a valid vendor name.');
        }

        $data = $validator($vendorName);

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
                'vendorName' => '',
                'shouldReceiveException' => true,
                'expected' => null,
            ],
            [
                'vendorName' => '_foo',
                'shouldReceiveException' => true,
                'expected' => null,
            ],
            [
                'vendorName' => 'foo/bar',
                'shouldReceiveException' => true,
                'expected' => null,
            ],
            [
                'vendorName' => 'foo',
                'shouldReceiveException' => false,
                'expected' => 'foo',
            ],
            [
                'vendorName' => 'FOO',
                'shouldReceiveException' => false,
                'expected' => 'foo',
            ],
            [
                'vendorName' => 'foo_bar',
                'shouldReceiveException' => false,
                'expected' => 'foo_bar',
            ],
            [
                'vendorName' => 'foo-bar',
                'shouldReceiveException' => false,
                'expected' => 'foo-bar',
            ],
            [
                'vendorName' => '1fOO_bAr-456',
                'shouldReceiveException' => false,
                'expected' => '1foo_bar-456',
            ],
        ];
    }
}
