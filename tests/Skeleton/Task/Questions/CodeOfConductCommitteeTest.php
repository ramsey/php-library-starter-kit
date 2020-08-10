<?php

declare(strict_types=1);

namespace Ramsey\Test\Skeleton\Task\Questions;

use Composer\IO\IOInterface;
use Mockery\MockInterface;
use Ramsey\Skeleton\Task\Answers;
use Ramsey\Skeleton\Task\Questions\CodeOfConductCommittee;

class CodeOfConductCommitteeTest extends QuestionTestCase
{
    public function getQuestionClass(): string
    {
        return CodeOfConductCommittee::class;
    }

    public function testGetQuestion(): void
    {
        $this->assertSame(
            'What is the name of your group or committee who oversees code of conduct issues?',
            $this->getQuestion()->getQuestion(),
        );
    }

    public function testGetName(): void
    {
        $this->assertSame(
            'codeOfConductCommittee',
            $this->getQuestion()->getName(),
        );
    }

    public function testIsOptional(): void
    {
        $this->assertTrue($this->getQuestion()->isOptional());
    }

    /**
     * @dataProvider shouldSkipProvider
     */
    public function testShouldSkip(?string $answer, bool $expected): void
    {
        /** @var IOInterface & MockInterface $io */
        $io = $this->mockery(IOInterface::class);
        $answers = new Answers();
        $answers->codeOfConduct = $answer;

        $question = new CodeOfConductCommittee($io, $answers);

        $this->assertSame($expected, $question->shouldSkip());
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function shouldSkipProvider(): array
    {
        return [
            [
                'answer' => null,
                'expected' => true,
            ],
            [
                'answer' => 'Contributor-1.4',
                'expected' => true,
            ],
            [
                'answer' => 'Contributor-2.0',
                'expected' => true,
            ],
            [
                'answer' => 'Citizen-2.3',
                'expected' => false,
            ],
        ];
    }
}
