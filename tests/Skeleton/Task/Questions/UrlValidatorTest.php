<?php

declare(strict_types=1);

namespace Ramsey\Test\Skeleton\Task\Questions;

use InvalidArgumentException;
use Mockery\MockInterface;
use Ramsey\Skeleton\Task\Question;
use Ramsey\Skeleton\Task\Questions\UrlValidator;
use Ramsey\Test\Skeleton\SkeletonTestCase;

class UrlValidatorTest extends SkeletonTestCase
{
    /**
     * @dataProvider provideDataForValidation
     */
    public function testValidate(
        string $url,
        bool $isOptional,
        bool $shouldReceiveException
    ): void {
        /** @var Question & MockInterface $urlValidator */
        $urlValidator = $this->mockery(UrlValidator::class, [
            'isOptional' => $isOptional,
        ]);

        $validator = $urlValidator->getValidator();

        $this->assertIsCallable($validator);

        if ($shouldReceiveException) {
            $this->expectException(InvalidArgumentException::class);
            $this->expectExceptionMessage('You must enter a valid URL, beginning with "http://" or "https://".');
        }

        $data = $validator($url);

        if (!$shouldReceiveException) {
            $this->assertSame($url, $data);
        }
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function provideDataForValidation(): array
    {
        return [
            [
                'url' => '',
                'isOptional' => true,
                'shouldReceiveException' => false,
            ],
            [
                'url' => '     ',
                'isOptional' => true,
                'shouldReceiveException' => false,
            ],
            [
                'url' => '',
                'isOptional' => false,
                'shouldReceiveException' => true,
            ],
            [
                'url' => 'anInvalidUrl',
                'isOptional' => true,
                'shouldReceiveException' => true,
            ],
            [
                'url' => 'anotherInvalidUrl',
                'isOptional' => false,
                'shouldReceiveException' => true,
            ],
            [
                'url' => 'example.com',
                'isOptional' => true,
                'shouldReceiveException' => true,
            ],
            [
                'url' => 'another.example.com',
                'isOptional' => false,
                'shouldReceiveException' => true,
            ],
            [
                'url' => 'http://example.com',
                'isOptional' => true,
                'shouldReceiveException' => false,
            ],
            [
                'url' => 'https://another.example.com',
                'isOptional' => false,
                'shouldReceiveException' => false,
            ],
        ];
    }
}
