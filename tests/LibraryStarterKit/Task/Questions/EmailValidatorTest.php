<?php

declare(strict_types=1);

namespace Ramsey\Test\Dev\LibraryStarterKit\Task\Questions;

use InvalidArgumentException;
use Mockery\MockInterface;
use Ramsey\Dev\LibraryStarterKit\Task\Question;
use Ramsey\Dev\LibraryStarterKit\Task\Questions\EmailValidator;
use Ramsey\Test\Dev\LibraryStarterKit\TestCase;

class EmailValidatorTest extends TestCase
{
    /**
     * @dataProvider provideDataForValidation
     */
    public function testValidate(
        string $email,
        bool $isOptional,
        bool $shouldReceiveException
    ): void {
        /** @var Question & MockInterface $emailValidator */
        $emailValidator = $this->mockery(EmailValidator::class, [
            'isOptional' => $isOptional,
        ]);

        $validator = $emailValidator->getValidator();

        $this->assertIsCallable($validator);

        if ($shouldReceiveException) {
            $this->expectException(InvalidArgumentException::class);
            $this->expectExceptionMessage('You must enter a valid email address.');
        }

        $data = $validator($email);

        if (!$shouldReceiveException) {
            $this->assertSame($email, $data);
        }
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function provideDataForValidation(): array
    {
        return [
            [
                'email' => '',
                'isOptional' => true,
                'shouldReceiveException' => false,
            ],
            [
                'email' => '     ',
                'isOptional' => true,
                'shouldReceiveException' => false,
            ],
            [
                'email' => '',
                'isOptional' => false,
                'shouldReceiveException' => true,
            ],
            [
                'email' => 'anInvalidAddress',
                'isOptional' => true,
                'shouldReceiveException' => true,
            ],
            [
                'email' => 'anotherInvalidAddress',
                'isOptional' => false,
                'shouldReceiveException' => true,
            ],
            [
                'email' => 'aValidAddress@example.com',
                'isOptional' => true,
                'shouldReceiveException' => false,
            ],
            [
                'email' => 'anotherValidAddress@example.com',
                'isOptional' => false,
                'shouldReceiveException' => false,
            ],
        ];
    }
}
