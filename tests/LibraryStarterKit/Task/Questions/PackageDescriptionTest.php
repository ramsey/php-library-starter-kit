<?php

declare(strict_types=1);

namespace Ramsey\Test\Dev\LibraryStarterKit\Task\Questions;

use Ramsey\Dev\LibraryStarterKit\Task\Questions\PackageDescription;

class PackageDescriptionTest extends QuestionTestCase
{
    public function getQuestionClass(): string
    {
        return PackageDescription::class;
    }

    public function testGetQuestion(): void
    {
        $this->assertSame(
            'Enter a brief description of your library.',
            $this->getQuestion()->getQuestion(),
        );
    }

    public function testGetName(): void
    {
        $this->assertSame(
            'packageDescription',
            $this->getQuestion()->getName(),
        );
    }

    public function testIsOptional(): void
    {
        $this->assertTrue($this->getQuestion()->isOptional());
    }
}
