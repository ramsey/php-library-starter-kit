<?php

declare(strict_types=1);

namespace Ramsey\Test\Dev\LibraryStarterKit\Console;

use Ramsey\Dev\LibraryStarterKit\Console\InstallQuestions;
use Ramsey\Dev\LibraryStarterKit\Console\Question\StarterKitQuestion;
use Ramsey\Test\Dev\LibraryStarterKit\TestCase;
use Symfony\Component\Console\Question\Question;

use function array_keys;
use function array_map;

class InstallQuestionsTest extends TestCase
{
    public function testGetQuestions(): void
    {
        $questions = new InstallQuestions();
        $receivedQuestions = $questions->getQuestions($this->answers);

        $this->assertContainsOnlyInstancesOf(Question::class, $receivedQuestions);
        $this->assertContainsOnlyInstancesOf(StarterKitQuestion::class, $receivedQuestions);
        $this->assertCount(37, $receivedQuestions);

        $names = array_map(fn (StarterKitQuestion $question): string => $question->getName(), $receivedQuestions);

        $this->assertCount(14, array_keys($names, 'funding', true));
        $this->assertSame('codeOfConductReportingUrl', $names[20]);
        $this->assertSame('funding', $names[21]);
        $this->assertSame('packageKeywords', $names[35]);
    }
}
