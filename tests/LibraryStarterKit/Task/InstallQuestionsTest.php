<?php

declare(strict_types=1);

namespace Ramsey\Test\Dev\LibraryStarterKit\Task;

use Composer\IO\IOInterface;
use Mockery\MockInterface;
use Ramsey\Dev\LibraryStarterKit\Task\Answers;
use Ramsey\Dev\LibraryStarterKit\Task\InstallQuestions;
use Ramsey\Dev\LibraryStarterKit\Task\Question;
use Ramsey\Test\Dev\LibraryStarterKit\TestCase;

class InstallQuestionsTest extends TestCase
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
