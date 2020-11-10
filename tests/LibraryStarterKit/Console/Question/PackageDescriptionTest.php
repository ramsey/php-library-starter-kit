<?php

declare(strict_types=1);

namespace Ramsey\Test\Dev\LibraryStarterKit\Console\Question;

use Ramsey\Dev\LibraryStarterKit\Console\Question\PackageDescription;
use Ramsey\Dev\LibraryStarterKit\Task\Answers;

class PackageDescriptionTest extends QuestionTestCase
{
    protected function getTestClass(): string
    {
        return PackageDescription::class;
    }

    protected function getQuestionName(): string
    {
        return 'packageDescription';
    }

    protected function getQuestionText(): string
    {
        return 'Enter a brief description of your library';
    }

    public function testValidator(): void
    {
        $validator = (new PackageDescription(new Answers()))->getValidator();

        $this->assertSame(
            'A brief description of my library.',
            $validator('A brief description of my library.'),
        );
        $this->assertNull($validator(null));
    }
}
