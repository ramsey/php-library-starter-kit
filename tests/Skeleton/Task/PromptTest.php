<?php

declare(strict_types=1);

namespace Ramsey\Test\Skeleton\Task;

use Composer\IO\IOInterface;
use Mockery\MockInterface;
use Ramsey\Skeleton\Task\Answers;
use Ramsey\Skeleton\Task\InstallQuestions;
use Ramsey\Skeleton\Task\Prompt;
use Ramsey\Skeleton\Task\Question;
use Ramsey\Test\Skeleton\SkeletonTestCase;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

class PromptTest extends SkeletonTestCase
{
    private Prompt $prompt;

    public function setUp(): void
    {
        /** @var IOInterface & MockInterface $io */
        $io = $this->mockery(IOInterface::class);

        /** @var Filesystem & MockInterface $filesystem */
        $filesystem = $this->mockery(Filesystem::class);

        /** @var Finder & MockInterface $finder */
        $finder = $this->mockery(Finder::class);

        $this->prompt = new Prompt('/path/to/app', $io, $filesystem, $finder);
    }

    public function testSetQuestions(): void
    {
        $questions = new InstallQuestions([]);

        $this->assertSame($this->prompt, $this->prompt->setQuestions($questions));
        $this->assertSame($questions, $this->prompt->getQuestions());
    }

    public function testSetAnswers(): void
    {
        $answers = new Answers();

        $this->assertSame($this->prompt, $this->prompt->setAnswers($answers));
        $this->assertSame($answers, $this->prompt->getAnswers());
    }

    public function testRun(): void
    {
        $question1 = $this->mockery(Question::class);
        $question1->expects()->ask();

        $question2 = $this->mockery(Question::class);
        $question2->expects()->ask();

        $question3 = $this->mockery(Question::class);
        $question3->expects()->ask();

        /** @var InstallQuestions & MockInterface $questions */
        $questions = $this->mockery(InstallQuestions::class, [
            'getQuestions' => [
                $question1,
                $question2,
                $question3,
            ],
        ]);

        $this->prompt->setQuestions($questions);
        $this->prompt->setAnswers(new Answers());

        $this->prompt->run();
    }
}
