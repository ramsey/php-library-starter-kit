<?php

declare(strict_types=1);

namespace Ramsey\Test\Dev\LibraryStarterKit\Task\Questions;

use Composer\IO\IOInterface;
use Mockery\MockInterface;
use Ramsey\Dev\LibraryStarterKit\Task\Answers;
use Ramsey\Dev\LibraryStarterKit\Task\Questions\CopyrightUrl;

class CopyrightUrlTest extends QuestionTestCase
{
    public function getQuestionClass(): string
    {
        return CopyrightUrl::class;
    }

    public function testGetQuestion(): void
    {
        $this->assertSame(
            'What is the copyright holder\'s website address?',
            $this->getQuestion()->getQuestion(),
        );
    }

    public function testGetName(): void
    {
        $this->assertSame(
            'copyrightUrl',
            $this->getQuestion()->getName(),
        );
    }

    public function testShouldSkipReturnsTrue(): void
    {
        $io = $this->mockery(IOInterface::class);
        $answers = new Answers();
        $answers->authorHoldsCopyright = true;
        $answers->authorUrl = 'https://example.com';

        $question = new CopyrightUrl($io, $answers);

        $this->assertTrue($question->shouldSkip());
        $this->assertSame('https://example.com', $answers->copyrightUrl);
    }

    public function testShouldSkipReturnsFalse(): void
    {
        /** @var IOInterface & MockInterface $io */
        $io = $this->mockery(IOInterface::class);
        $answers = new Answers();
        $answers->authorHoldsCopyright = false;

        $question = new CopyrightUrl($io, $answers);

        $this->assertFalse($question->shouldSkip());
    }

    public function testShouldSkipReturnsFalseWhenAuthorHoldsCopyrightNotSet(): void
    {
        /** @var IOInterface & MockInterface $io */
        $io = $this->mockery(IOInterface::class);
        $answers = new Answers();

        $question = new CopyrightUrl($io, $answers);

        $this->assertFalse($question->shouldSkip());
    }

    public function testShouldSkipUsesEmptyAuthorUrlWhenAuthorUrlNotSet(): void
    {
        /** @var IOInterface & MockInterface $io */
        $io = $this->mockery(IOInterface::class);
        $answers = new Answers();
        $answers->authorHoldsCopyright = true;

        $question = new CopyrightUrl($io, $answers);

        $this->assertTrue($question->shouldSkip());
        $this->assertNull($answers->copyrightUrl);
    }

    public function testIsOptional(): void
    {
        $this->assertTrue($this->getQuestion()->isOptional());
    }
}
