<?php

declare(strict_types=1);

namespace Ramsey\Test\Skeleton\Task\Questions;

use Composer\IO\IOInterface;
use Mockery\MockInterface;
use Ramsey\Skeleton\Task\Answers;
use Ramsey\Skeleton\Task\Question;
use Ramsey\Test\Skeleton\SkeletonTestCase;

abstract class QuestionTestCase extends SkeletonTestCase
{
    private Question $question;

    /**
     * @return class-string<Question>
     */
    abstract public function getQuestionClass(): string;

    public function setUp(): void
    {
        /** @var IOInterface & MockInterface $io */
        $io = $this->mockery(IOInterface::class);
        $answers = new Answers();

        /** @var class-string<Question> $questionClass */
        $questionClass = $this->getQuestionClass();

        $this->question = new $questionClass($io, $answers);
    }

    final public function getQuestion(): Question
    {
        return $this->question;
    }
}
