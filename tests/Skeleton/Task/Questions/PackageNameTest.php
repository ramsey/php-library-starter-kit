<?php

declare(strict_types=1);

namespace Ramsey\Test\Skeleton\Task\Questions;

use InvalidArgumentException;
use Ramsey\Skeleton\Task\Questions\PackageName;

use const PHP_EOL;

class PackageNameTest extends QuestionTestCase
{
    public function getQuestionClass(): string
    {
        return PackageName::class;
    }

    public function testGetQuestion(): void
    {
        $this->assertSame(
            'What is your package name?',
            $this->getQuestion()->getQuestion(),
        );
    }

    public function testGetName(): void
    {
        $this->assertSame(
            'packageName',
            $this->getQuestion()->getName(),
        );
    }

    public function testGetDefault(): void
    {
        $this->assertSame(
            '{{ vendorName }}/{{ projectName }}',
            $this->getQuestion()->getDefault(),
        );
    }

    public function testGetPrompt(): void
    {
        $expectedPrompt = PHP_EOL;
        $expectedPrompt .= '<fg=cyan>What is your package name?</>';
        $expectedPrompt .= PHP_EOL;
        $expectedPrompt .= '[<fg=blue>{{ vendorName }}/{{ projectName }}</>] ';
        $expectedPrompt .= '<fg=cyan>> </>';
        $expectedPrompt .= '<fg=cyan>{{ vendorName }}/</>';

        $this->assertSame(
            $expectedPrompt,
            $this->getQuestion()->getPrompt(),
        );
    }

    /**
     * @dataProvider provideDataForValidation
     */
    public function testValidate(
        string $packageName,
        ?string $vendorName,
        bool $shouldReceiveException,
        ?string $expected
    ): void {
        $this->getQuestion()->getAnswers()->vendorName = $vendorName;
        $validator = $this->getQuestion()->getValidator();

        $this->assertIsCallable($validator);

        if ($shouldReceiveException) {
            $this->expectException(InvalidArgumentException::class);
            $this->expectExceptionMessage('You must enter a valid package name.');
        }

        $data = $validator($packageName);

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
                'packageName' => '',
                'vendorName' => 'foo',
                'shouldReceiveException' => true,
                'expected' => null,
            ],
            [
                'packageName' => '    ',
                'vendorName' => 'foo',
                'shouldReceiveException' => true,
                'expected' => null,
            ],
            [
                'packageName' => '',
                'vendorName' => 'foo',
                'shouldReceiveException' => true,
                'expected' => null,
            ],
            [
                'packageName' => '',
                'vendorName' => null,
                'shouldReceiveException' => true,
                'expected' => null,
            ],
            [
                'packageName' => 'foobar',
                'vendorName' => null,
                'shouldReceiveException' => true,
                'expected' => null,
            ],
            [
                'packageName' => 'bar/foobar',
                'vendorName' => 'foo',
                'shouldReceiveException' => true,
                'expected' => null,
            ],
            [
                'packageName' => 'an invalid package name',
                'vendorName' => 'foo',
                'shouldReceiveException' => true,
                'expected' => null,
            ],
            [
                'packageName' => 'a-package',
                'vendorName' => 'a-vendor',
                'shouldReceiveException' => false,
                'expected' => 'a-vendor/a-package',
            ],
            [
                'packageName' => 'foobar',
                'vendorName' => 'foo',
                'shouldReceiveException' => false,
                'expected' => 'foo/foobar',
            ],
            [
                'packageName' => 'foo/foobar',
                'vendorName' => 'foo',
                'shouldReceiveException' => false,
                'expected' => 'foo/foobar',
            ],
            [
                'packageName' => 'FOO/FOOBAR',
                'vendorName' => 'foo',
                'shouldReceiveException' => false,
                'expected' => 'foo/foobar',
            ],
            [
                'packageName' => 'FOOBAR',
                'vendorName' => 'foo',
                'shouldReceiveException' => false,
                'expected' => 'foo/foobar',
            ],
        ];
    }
}
