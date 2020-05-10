<?php

declare(strict_types=1);

namespace Ramsey\Test\Skeleton\Task\Questions;

use Composer\IO\IOInterface;
use Mockery\MockInterface;
use Ramsey\Skeleton\Task\Answers;
use Ramsey\Skeleton\Task\Questions\CopyrightHolder;

class CopyrightHolderTest extends QuestionTestCase
{
    public function getQuestionClass(): string
    {
        return CopyrightHolder::class;
    }

    public function testGetQuestion(): void
    {
        $this->assertSame(
            'Who is the copyright holder?',
            $this->getQuestion()->getQuestion()
        );
    }

    public function testGetName(): void
    {
        $this->assertSame(
            'copyrightHolder',
            $this->getQuestion()->getName()
        );
    }

    public function testGetDefault(): void
    {
        $this->assertSame(
            '{{ authorName }}',
            $this->getQuestion()->getDefault()
        );
    }

    public function testShouldSkipReturnsTrue(): void
    {
        /** @var IOInterface & MockInterface $io */
        $io = $this->mockery(IOInterface::class);
        $answers = new Answers();
        $answers->authorHoldsCopyright = true;
        $answers->authorName = 'Janice Smith';

        $question = new CopyrightHolder($io, $answers);

        $this->assertTrue($question->shouldSkip());
        $this->assertSame('Janice Smith', $answers->copyrightHolder);
    }

    public function testShouldSkipReturnsFalse(): void
    {
        /** @var IOInterface & MockInterface $io */
        $io = $this->mockery(IOInterface::class);
        $answers = new Answers();
        $answers->authorHoldsCopyright = false;

        $question = new CopyrightHolder($io, $answers);

        $this->assertFalse($question->shouldSkip());
    }

    public function testShouldSkipReturnsFalseWhenAuthorHoldsCopyrightNotSet(): void
    {
        /** @var IOInterface & MockInterface $io */
        $io = $this->mockery(IOInterface::class);
        $answers = new Answers();

        $question = new CopyrightHolder($io, $answers);

        $this->assertFalse($question->shouldSkip());
    }

    public function testShouldSkipUsesEmptyAuthorNameWhenAuthorNameNotSet(): void
    {
        /** @var IOInterface & MockInterface $io */
        $io = $this->mockery(IOInterface::class);
        $answers = new Answers();
        $answers->authorHoldsCopyright = true;

        $question = new CopyrightHolder($io, $answers);

        $this->assertTrue($question->shouldSkip());
        $this->assertNull($answers->copyrightHolder);
    }
}
