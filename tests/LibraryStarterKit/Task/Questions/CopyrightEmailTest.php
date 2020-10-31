<?php

declare(strict_types=1);

namespace Ramsey\Test\Dev\LibraryStarterKit\Task\Questions;

use Composer\IO\IOInterface;
use Mockery\MockInterface;
use Ramsey\Dev\LibraryStarterKit\Task\Answers;
use Ramsey\Dev\LibraryStarterKit\Task\Questions\CopyrightEmail;

class CopyrightEmailTest extends QuestionTestCase
{
    public function getQuestionClass(): string
    {
        return CopyrightEmail::class;
    }

    public function testGetQuestion(): void
    {
        $this->assertSame(
            'What is the copyright holder\'s email address?',
            $this->getQuestion()->getQuestion(),
        );
    }

    public function testGetName(): void
    {
        $this->assertSame(
            'copyrightEmail',
            $this->getQuestion()->getName(),
        );
    }

    public function testShouldSkipReturnsTrue(): void
    {
        /** @var IOInterface & MockInterface $io */
        $io = $this->mockery(IOInterface::class);
        $answers = new Answers();
        $answers->authorHoldsCopyright = true;
        $answers->authorEmail = 'jsmith@example.com';

        $question = new CopyrightEmail($io, $answers);

        $this->assertTrue($question->shouldSkip());
        $this->assertSame('jsmith@example.com', $answers->copyrightEmail);
    }

    public function testShouldSkipReturnsFalse(): void
    {
        /** @var IOInterface & MockInterface $io */
        $io = $this->mockery(IOInterface::class);
        $answers = new Answers();
        $answers->authorHoldsCopyright = false;

        $question = new CopyrightEmail($io, $answers);

        $this->assertFalse($question->shouldSkip());
    }

    public function testShouldSkipReturnsFalseWhenAuthorHoldsCopyrightNotSet(): void
    {
        /** @var IOInterface & MockInterface $io */
        $io = $this->mockery(IOInterface::class);
        $answers = new Answers();

        $question = new CopyrightEmail($io, $answers);

        $this->assertFalse($question->shouldSkip());
    }

    public function testShouldSkipUsesEmptyAuthorEmailWhenAuthorEmailNotSet(): void
    {
        /** @var IOInterface & MockInterface $io */
        $io = $this->mockery(IOInterface::class);
        $answers = new Answers();
        $answers->authorHoldsCopyright = true;

        $question = new CopyrightEmail($io, $answers);

        $this->assertTrue($question->shouldSkip());
        $this->assertNull($answers->copyrightEmail);
    }

    public function testIsOptional(): void
    {
        $this->assertTrue($this->getQuestion()->isOptional());
    }
}
