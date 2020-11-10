<?php

declare(strict_types=1);

namespace Ramsey\Test\Dev\LibraryStarterKit\Task;

use Mockery;
use Mockery\MockInterface;
use Ramsey\Dev\LibraryStarterKit\Console\Question\SkippableQuestion;
use Ramsey\Dev\LibraryStarterKit\Console\Question\StarterKitQuestion;
use Ramsey\Dev\LibraryStarterKit\Task\Answers;
use Ramsey\Dev\LibraryStarterKit\Task\InstallQuestions;
use Ramsey\Dev\LibraryStarterKit\Task\Prompt;
use Ramsey\Test\Dev\LibraryStarterKit\TestCase;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;

class PromptTest extends TestCase
{
    public function testRun(): void
    {
        /** @var StarterKitQuestion & Question & MockInterface $question1 */
        $question1 = $this->mockery(Question::class, [
            'getName' => 'authorName',
        ]);

        /** @var StarterKitQuestion & Question & MockInterface $question2 */
        $question2 = $this->mockery(Question::class, [
            'getName' => 'authorEmail',
        ]);

        /** @var StarterKitQuestion & Question & MockInterface $question3 */
        $question3 = $this->mockery(Question::class, [
            'getName' => 'packageNamespace',
        ]);

        /** @var StarterKitQuestion & SkippableQuestion & Question & MockInterface $question4 */
        $question4 = Mockery::mock(Question::class . ',' . SkippableQuestion::class, [
            'getName' => 'copyrightHolder',
            'shouldSkip' => true,
            'getDefault' => 'Default Holder',
        ]);

        /** @var StarterKitQuestion & SkippableQuestion & Question & MockInterface $question5 */
        $question5 = Mockery::mock(Question::class . ',' . SkippableQuestion::class, [
            'getName' => 'copyrightEmail',
            'shouldSkip' => false,
        ]);

        /** @var InstallQuestions & MockInterface $questions */
        $questions = $this->mockery(InstallQuestions::class, [
            'getQuestions' => [
                $question1,
                $question2,
                $question3,
                $question4,
                $question5,
            ],
        ]);

        /** @var SymfonyStyle & MockInterface $console */
        $console = $this->mockery(SymfonyStyle::class);
        $console->expects()->askQuestion($question1)->andReturn('Frodo Baggins');
        $console->expects()->askQuestion($question2)->andReturn('frodo@example.com');
        $console->expects()->askQuestion($question3)->andReturn('Fellowship\\Ring');
        $console->expects()->askQuestion($question5)->andReturn('copyright@example.com');

        $answers = new Answers();
        $prompt = new Prompt($questions, $answers);

        $prompt->run($console);

        $this->assertSame('Frodo Baggins', $answers->authorName);
        $this->assertSame('frodo@example.com', $answers->authorEmail);
        $this->assertSame('Fellowship\\Ring', $answers->packageNamespace);
        $this->assertSame('Default Holder', $answers->copyrightHolder);
        $this->assertSame('copyright@example.com', $answers->copyrightEmail);
    }
}
