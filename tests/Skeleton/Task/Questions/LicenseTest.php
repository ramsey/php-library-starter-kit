<?php

declare(strict_types=1);

namespace Ramsey\Test\Skeleton\Task\Questions;

use Composer\IO\IOInterface;
use Mockery\MockInterface;
use Ramsey\Skeleton\Task\Answers;
use Ramsey\Skeleton\Task\Questions\License;

use const PHP_EOL;

class LicenseTest extends QuestionTestCase
{
    public function getQuestionClass(): string
    {
        return License::class;
    }

    public function testGetQuestion(): void
    {
        $this->assertSame(
            'Choose a license for your project.',
            $this->getQuestion()->getQuestion(),
        );
    }

    public function testGetName(): void
    {
        $this->assertSame(
            'license',
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
        $expectedPrompt .= '<fg=cyan>Choose a license for your project.</>';
        $expectedPrompt .= ' [<fg=blue>1</>]';

        $this->assertSame($expectedPrompt, $this->getQuestion()->getPrompt());
    }

    public function testAsk(): void
    {
        $expectedPrompt = PHP_EOL;
        $expectedPrompt .= '<fg=cyan>Choose a license for your project.</>';
        $expectedPrompt .= ' [<fg=blue>1</>]';

        /** @var IOInterface & MockInterface $io */
        $io = $this->mockery(IOInterface::class);
        $io
            ->expects()
            ->select($expectedPrompt, License::CHOICES, '1')
            ->andReturn('3');

        $answers = new Answers();

        $question = new License($io, $answers);
        $question->ask();

        $this->assertSame('BSD-2-Clause', $answers->license);
    }

    public function testAskWithArrayOfResponses(): void
    {
        $expectedPrompt = PHP_EOL;
        $expectedPrompt .= '<fg=cyan>Choose a license for your project.</>';
        $expectedPrompt .= ' [<fg=blue>1</>]';

        /** @var IOInterface & MockInterface $io */
        $io = $this->mockery(IOInterface::class);
        $io
            ->expects()
            ->select($expectedPrompt, License::CHOICES, '1')
            ->andReturn([4, 3]);

        $answers = new Answers();

        $question = new License($io, $answers);
        $question->ask();

        $this->assertSame('BSD-3-Clause', $answers->license);
    }

    public function testAskWithEmptyArrayUsesDefault(): void
    {
        $expectedPrompt = PHP_EOL;
        $expectedPrompt .= '<fg=cyan>Choose a license for your project.</>';
        $expectedPrompt .= ' [<fg=blue>1</>]';

        /** @var IOInterface & MockInterface $io */
        $io = $this->mockery(IOInterface::class);
        $io
            ->expects()
            ->select($expectedPrompt, License::CHOICES, '1')
            ->andReturn([]);

        $answers = new Answers();

        $question = new License($io, $answers);
        $question->ask();

        $this->assertSame('Proprietary', $answers->license);
    }

    public function testChoicesConstant(): void
    {
        $this->assertSame(
            [
                1 => 'Proprietary',
                2 => 'Apache License 2.0',
                3 => 'BSD 2-Clause "Simplified" License',
                4 => 'BSD 3-Clause "New" or "Revised" License',
                5 => 'GNU Affero General Public License v3.0 or later',
                6 => 'GNU General Public License v3.0 or later',
                7 => 'GNU Lesser General Public License v3.0 or later',
                8 => 'MIT License',
                9 => 'MIT No Attribution',
                10 => 'Mozilla Public License 2.0',
                11 => 'Unlicense',
            ],
            License::CHOICES,
        );
    }

    public function testChoiceIdentifierMapConstant(): void
    {
        $this->assertSame(
            [
                1 => 'Proprietary',
                2 => 'Apache-2.0',
                3 => 'BSD-2-Clause',
                4 => 'BSD-3-Clause',
                5 => 'AGPL-3.0-or-later',
                6 => 'GPL-3.0-or-later',
                7 => 'LGPL-3.0-or-later',
                8 => 'MIT',
                9 => 'MIT-0',
                10 => 'MPL-2.0',
                11 => 'Unlicense',
            ],
            License::CHOICE_IDENTIFIER_MAP,
        );
    }
}
