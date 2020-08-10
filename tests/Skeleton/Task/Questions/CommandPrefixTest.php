<?php

declare(strict_types=1);

namespace Ramsey\Test\Skeleton\Task\Questions;

use InvalidArgumentException;
use Ramsey\Skeleton\Task\Questions\CommandPrefix;

use const PHP_EOL;

class CommandPrefixTest extends QuestionTestCase
{
    public function getQuestionClass(): string
    {
        return CommandPrefix::class;
    }

    public function testGetQuestion(): void
    {
        $question = 'What command prefix (namespace) would you like to use?' . PHP_EOL;

        $question .= '<comment>';
        $question .= 'Composer supports namespaces (or prefixes) for custom commands. For' . PHP_EOL;
        $question .= 'example, we might choose to use the prefix `vnd\'. If we define a command' . PHP_EOL;
        $question .= 'named `vnd:test\', we can execute it with `composer vnd:test\'. We can' . PHP_EOL;
        $question .= 'list all commands in the `vnd\' namespace with `composer list vnd\'.';
        $question .= '</comment>' . PHP_EOL . PHP_EOL;

        $question .= '<comment>';
        $question .= 'It\'s best to keep this prefix short. Between 1 and 4 characters is good.';
        $question .= '</comment>' . PHP_EOL;

        $this->assertSame(
            $question,
            $this->getQuestion()->getQuestion(),
        );
    }

    public function testGetName(): void
    {
        $this->assertSame(
            'commandPrefix',
            $this->getQuestion()->getName(),
        );
    }

    public function testGetDefault(): void
    {
        $this->assertSame(
            'vnd',
            $this->getQuestion()->getDefault(),
        );
    }

    /**
     * @dataProvider provideDataForValidation
     */
    public function testValidate(
        string $commandPrefix,
        bool $shouldReceiveException,
        ?string $expected
    ): void {
        $validator = $this->getQuestion()->getValidator();

        $this->assertIsCallable($validator);

        if ($shouldReceiveException) {
            $this->expectException(InvalidArgumentException::class);
            $this->expectExceptionMessage('You must enter a valid command prefix.');
        }

        $data = $validator($commandPrefix);

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
                'commandPrefix' => 'vnd',
                'shouldReceiveException' => false,
                'expected' => 'vnd',
            ],
            [
                'commandPrefix' => '1234',
                'shouldReceiveException' => false,
                'expected' => '1234',
            ],
            [
                'commandPrefix' => 'føübáR',
                'shouldReceiveException' => false,
                'expected' => 'føübáR',
            ],
            [
                'commandPrefix' => '_',
                'shouldReceiveException' => false,
                'expected' => '_',
            ],
            [
                'commandPrefix' => '_ab',
                'shouldReceiveException' => false,
                'expected' => '_ab',
            ],
            [
                'commandPrefix' => '0123456789abcdef',
                'shouldReceiveException' => false,
                'expected' => '0123456789abcdef',
            ],
            [
                'commandPrefix' => '-',
                'shouldReceiveException' => true,
                'expected' => null,
            ],
            [
                'commandPrefix' => '!',
                'shouldReceiveException' => true,
                'expected' => null,
            ],
            [
                'commandPrefix' => 'ab!',
                'shouldReceiveException' => true,
                'expected' => null,
            ],
            [
                'commandPrefix' => '',
                'shouldReceiveException' => true,
                'expected' => null,
            ],
            [
                'commandPrefix' => 'abcdefghijklmnopq',
                'shouldReceiveException' => true,
                'expected' => null,
            ],
        ];
    }
}
