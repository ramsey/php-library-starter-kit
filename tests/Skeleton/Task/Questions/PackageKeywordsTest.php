<?php

declare(strict_types=1);

namespace Ramsey\Test\Skeleton\Task\Questions;

use Composer\IO\IOInterface;
use Mockery\MockInterface;
use Ramsey\Skeleton\Task\Answers;
use Ramsey\Skeleton\Task\Questions\PackageKeywords;

use const PHP_EOL;

class PackageKeywordsTest extends QuestionTestCase
{
    public function getQuestionClass(): string
    {
        return PackageKeywords::class;
    }

    public function testGetQuestion(): void
    {
        $this->assertSame(
            'Enter a set of comma-separated keywords describing your library.',
            $this->getQuestion()->getQuestion()
        );
    }

    public function testGetName(): void
    {
        $this->assertSame(
            'packageKeywords',
            $this->getQuestion()->getName()
        );
    }

    public function testIsOptional(): void
    {
        $this->assertTrue($this->getQuestion()->isOptional());
    }

    public function testAskStoresKeywordsAsArrayInAnswers(): void
    {
        $expectedPrompt = PHP_EOL;
        $expectedPrompt .= '<fg=cyan>Enter a set of comma-separated keywords describing your library.</>';
        $expectedPrompt .= PHP_EOL;
        $expectedPrompt .= '<options=bold>optional</> <fg=cyan>> </>';

        /** @var IOInterface & MockInterface $io */
        $io = $this->mockery(IOInterface::class);
        $io->expects()->ask($expectedPrompt)->andReturn('foo ,   bar,baz   ');

        $answers = new Answers();

        $question = new PackageKeywords($io, $answers);
        $question->ask();

        $this->assertSame(['foo', 'bar', 'baz'], $answers->packageKeywords);
    }

    public function testAskStoresEmptyArrayForNoInput(): void
    {
        $expectedPrompt = PHP_EOL;
        $expectedPrompt .= '<fg=cyan>Enter a set of comma-separated keywords describing your library.</>';
        $expectedPrompt .= PHP_EOL;
        $expectedPrompt .= '<options=bold>optional</> <fg=cyan>> </>';

        /** @var IOInterface & MockInterface $io */
        $io = $this->mockery(IOInterface::class);
        $io->expects()->ask($expectedPrompt)->andReturn('        ');

        $answers = new Answers();

        $question = new PackageKeywords($io, $answers);
        $question->ask();

        $this->assertSame([], $answers->packageKeywords);
    }
}
