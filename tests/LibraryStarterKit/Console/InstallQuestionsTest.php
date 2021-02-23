<?php

declare(strict_types=1);

namespace Ramsey\Test\Dev\LibraryStarterKit\Console;

use Ramsey\Dev\LibraryStarterKit\Console\InstallQuestions;
use Ramsey\Dev\LibraryStarterKit\Console\Question\StarterKitQuestion;
use Ramsey\Test\Dev\LibraryStarterKit\TestCase;
use Symfony\Component\Console\Question\Question;

class InstallQuestionsTest extends TestCase
{
    public function testGetQuestions(): void
    {
        $questions = new InstallQuestions();
        $receivedQuestions = $questions->getQuestions($this->answers);

        $this->assertContainsOnlyInstancesOf(Question::class, $receivedQuestions);
        $this->assertContainsOnlyInstancesOf(StarterKitQuestion::class, $receivedQuestions);
        $this->assertCount(20, $receivedQuestions);
    }
}
