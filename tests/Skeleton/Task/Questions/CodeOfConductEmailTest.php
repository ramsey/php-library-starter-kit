<?php

declare(strict_types=1);

namespace Ramsey\Test\Skeleton\Task\Questions;

use Composer\IO\IOInterface;
use Mockery\MockInterface;
use Ramsey\Skeleton\Task\Answers;
use Ramsey\Skeleton\Task\Questions\CodeOfConduct;
use Ramsey\Skeleton\Task\Questions\CodeOfConductEmail;

class CodeOfConductEmailTest extends QuestionTestCase
{
    public function getQuestionClass(): string
    {
        return CodeOfConductEmail::class;
    }

    public function testGetQuestion(): void
    {
        $this->assertSame(
            'What email address should people use to report code of conduct issues?',
            $this->getQuestion()->getQuestion(),
        );
    }

    public function testGetName(): void
    {
        $this->assertSame(
            'codeOfConductEmail',
            $this->getQuestion()->getName(),
        );
    }

    public function testShouldSkipReturnsTrue(): void
    {
        /** @var IOInterface & MockInterface $io */
        $io = $this->mockery(IOInterface::class);
        $answers = new Answers();
        $answers->codeOfConduct = CodeOfConduct::CHOICE_IDENTIFIER_MAP[1];

        $question = new CodeOfConductEmail($io, $answers);

        $this->assertTrue($question->shouldSkip());
    }

    public function testShouldSkipReturnsFalse(): void
    {
        /** @var IOInterface & MockInterface $io */
        $io = $this->mockery(IOInterface::class);
        $answers = new Answers();
        $answers->codeOfConduct = CodeOfConduct::CHOICE_IDENTIFIER_MAP[2];

        $question = new CodeOfConductEmail($io, $answers);

        $this->assertFalse($question->shouldSkip());
    }

    public function testShouldSkipReturnsTrueWhenCodeOfConductNotSet(): void
    {
        /** @var IOInterface & MockInterface $io */
        $io = $this->mockery(IOInterface::class);
        $answers = new Answers();

        $question = new CodeOfConductEmail($io, $answers);

        $this->assertTrue($question->shouldSkip());
    }
}
