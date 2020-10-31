<?php

declare(strict_types=1);

namespace Ramsey\Test\Dev\LibraryStarterKit\Task\Questions;

use Composer\IO\IOInterface;
use Mockery\MockInterface;
use Ramsey\Dev\LibraryStarterKit\Task\Answers;
use Ramsey\Dev\LibraryStarterKit\Task\Questions\AuthorHoldsCopyright;

use const PHP_EOL;

class AuthorHoldsCopyrightTest extends QuestionTestCase
{
    public function getQuestionClass(): string
    {
        return AuthorHoldsCopyright::class;
    }

    public function testGetQuestion(): void
    {
        $this->assertSame(
            'Are you the copyright holder?',
            $this->getQuestion()->getQuestion(),
        );
    }

    public function testGetName(): void
    {
        $this->assertSame(
            'authorHoldsCopyright',
            $this->getQuestion()->getName(),
        );
    }

    public function testGetDefault(): void
    {
        $this->assertSame(
            'Y/n',
            $this->getQuestion()->getDefault(),
        );
    }

    public function testAsk(): void
    {
        $expectedPrompt = PHP_EOL;
        $expectedPrompt .= '<fg=cyan>Are you the copyright holder?</>';
        $expectedPrompt .= PHP_EOL;
        $expectedPrompt .= '[<fg=blue>Y/n</>] ';
        $expectedPrompt .= '<fg=cyan>> </>';

        /** @var IOInterface & MockInterface $io */
        $io = $this->mockery(IOInterface::class);
        $io
            ->expects()
            ->askConfirmation($expectedPrompt, true)
            ->andReturn(true);

        $answers = new Answers();

        $question = new AuthorHoldsCopyright($io, $answers);
        $question->ask();

        $this->assertTrue($answers->authorHoldsCopyright);
    }
}
