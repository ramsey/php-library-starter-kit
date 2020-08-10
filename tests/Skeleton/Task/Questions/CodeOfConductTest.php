<?php

declare(strict_types=1);

namespace Ramsey\Test\Skeleton\Task\Questions;

use Composer\IO\IOInterface;
use Mockery\MockInterface;
use Ramsey\Skeleton\Task\Answers;
use Ramsey\Skeleton\Task\Questions\CodeOfConduct;

use const PHP_EOL;

class CodeOfConductTest extends QuestionTestCase
{
    public function getQuestionClass(): string
    {
        return CodeOfConduct::class;
    }

    public function testGetQuestion(): void
    {
        $this->assertSame(
            'Choose a code of conduct for your project.',
            $this->getQuestion()->getQuestion(),
        );
    }

    public function testGetName(): void
    {
        $this->assertSame(
            'codeOfConduct',
            $this->getQuestion()->getName(),
        );
    }

    public function testGetDefault(): void
    {
        $this->assertSame(
            '1',
            $this->getQuestion()->getDefault(),
        );
    }

    public function testGetPrompt(): void
    {
        $expectedPrompt = PHP_EOL;
        $expectedPrompt .= '<fg=cyan>Choose a code of conduct for your project.</>';
        $expectedPrompt .= ' [<fg=blue>1</>]';

        $this->assertSame($expectedPrompt, $this->getQuestion()->getPrompt());
    }

    public function testAsk(): void
    {
        $expectedPrompt = PHP_EOL;
        $expectedPrompt .= '<fg=cyan>Choose a code of conduct for your project.</>';
        $expectedPrompt .= ' [<fg=blue>1</>]';

        /** @var IOInterface & MockInterface $io */
        $io = $this->mockery(IOInterface::class);
        $io
            ->expects()
            ->select($expectedPrompt, CodeOfConduct::CHOICES, '1')
            ->andReturn('3');

        $answers = new Answers();

        $question = new CodeOfConduct($io, $answers);
        $question->ask();

        $this->assertSame('Contributor-2.0', $answers->codeOfConduct);
    }

    public function testAskWithArrayOfResponses(): void
    {
        $expectedPrompt = PHP_EOL;
        $expectedPrompt .= '<fg=cyan>Choose a code of conduct for your project.</>';
        $expectedPrompt .= ' [<fg=blue>1</>]';

        /** @var IOInterface & MockInterface $io */
        $io = $this->mockery(IOInterface::class);
        $io
            ->expects()
            ->select($expectedPrompt, CodeOfConduct::CHOICES, '1')
            ->andReturn([4, 3]);

        $answers = new Answers();

        $question = new CodeOfConduct($io, $answers);
        $question->ask();

        $this->assertSame('Citizen-2.3', $answers->codeOfConduct);
    }

    public function testAskWithEmptyArrayUsesDefault(): void
    {
        $expectedPrompt = PHP_EOL;
        $expectedPrompt .= '<fg=cyan>Choose a code of conduct for your project.</>';
        $expectedPrompt .= ' [<fg=blue>1</>]';

        /** @var IOInterface & MockInterface $io */
        $io = $this->mockery(IOInterface::class);
        $io
            ->expects()
            ->select($expectedPrompt, CodeOfConduct::CHOICES, '1')
            ->andReturn([]);

        $answers = new Answers();

        $question = new CodeOfConduct($io, $answers);
        $question->ask();

        $this->assertNull($answers->codeOfConduct);
    }

    public function testChoicesConstant(): void
    {
        $this->assertSame(
            [
                1 => 'None',
                2 => 'Contributor Covenant Code of Conduct, version 1.4',
                3 => 'Contributor Covenant Code of Conduct, version 2.0',
                4 => 'Citizen Code of Conduct, version 2.3',
            ],
            CodeOfConduct::CHOICES,
        );
    }

    public function testChoiceIdentifierMapConstant(): void
    {
        $this->assertSame(
            [
                1 => null,
                2 => 'Contributor-1.4',
                3 => 'Contributor-2.0',
                4 => 'Citizen-2.3',
            ],
            CodeOfConduct::CHOICE_IDENTIFIER_MAP,
        );
    }
}
