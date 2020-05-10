<?php

declare(strict_types=1);

namespace Ramsey\Test\Skeleton\Task;

use Composer\IO\IOInterface;
use Mockery\MockInterface;
use Ramsey\Skeleton\Task\Answers;
use Ramsey\Skeleton\Task\InstallQuestions;
use Ramsey\Skeleton\Task\Question;
use Ramsey\Test\Skeleton\SkeletonTestCase;

class InstallQuestionsTest extends SkeletonTestCase
{
    public function testGetQuestions(): void
    {
        /** @var IOInterface & MockInterface $io */
        $io = $this->mockery(IOInterface::class);
        $answers = new Answers();

        $questions = new InstallQuestions(['projectName' => 'aProjectName']);
        $receivedQuestions = $questions->getQuestions($io, $answers);

        $this->assertSame('aProjectName', $answers->projectName);
        $this->assertContainsOnlyInstancesOf(Question::class, $receivedQuestions);
        $this->assertCount(21, $receivedQuestions);
    }
}
