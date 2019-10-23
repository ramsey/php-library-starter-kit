<?php

declare(strict_types=1);

namespace Ramsey\Skeleton\Test\Task;

use Composer\IO\IOInterface;
use Ramsey\Skeleton\Task\Prompt;
use Ramsey\Skeleton\Task\Questions\InstallQuestions;
use Ramsey\Skeleton\Test\TestCase;

class PromptTest extends TestCase
{
    public function testSetGetQuestions()
    {
        $questions = \Mockery::mock(InstallQuestions::class);

        $task = \Mockery::mock(Prompt::class);
        $task->shouldReceive('setQuestions')->passthru();
        $task->shouldReceive('getQuestions')->passthru();

        $this->assertSame($task, $task->setQuestions($questions));
        $this->assertSame($questions, $task->getQuestions());
    }

    public function testGetAnswersReturnsEmptyArray()
    {
        $task = \Mockery::mock(Prompt::class);
        $task->shouldReceive('getAnswers')->passthru();

        $this->assertSame([], $task->getAnswers());
    }

    public function testRun()
    {
        $expected = [
            'question1' => 'My name is Sir Lancelot of Camelot.',
            'question2' => 'To seek the Holy Grail.',
            'question3' => 'Blue.',
            'question4' => 'I don\'t know that.',
            'question5' => 'What do you mean? An African or European swallow?',
        ];

        $questions = [
            'question1' => [
                'prompt' => 'What is your name?',
                'operation' => 'foobar',
            ],
            'question2' => [
                'prompt' => 'What is your quest?',
                'default' => 'To seek the Holy Grail.',
            ],
            'question3' => [
                'prompt' => 'What is your favorite color?',
                'default' => 'Blue.',
                'operation' => function (IOInterface $io, string $prompt, array $answers, $default) use ($expected) {
                    $answersAtThisPoint = [
                        'question1' => $expected['question1'],
                        'question2' => $expected['question2'],
                    ];

                    if ($prompt !== 'What is your favorite color?') {
                        throw new \Exception('Unexpected prompt received: ' . $prompt);
                    }

                    if ($answers !== $answersAtThisPoint) {
                        throw new \Exception('Unexpected answers received: ' . var_export($answers, true));
                    }

                    return $default;
                },
            ],
            'question4' => [
                'prompt' => 'What is the capital of Assyria?',
                'operation' => function (IOInterface $io, string $prompt, array $answers, $default) use ($expected) {
                    $answersAtThisPoint = [
                        'question1' => $expected['question1'],
                        'question2' => $expected['question2'],
                        'question3' => $expected['question3'],
                    ];

                    if ($prompt !== 'What is the capital of Assyria?') {
                        throw new \Exception('Unexpected prompt received: ' . $prompt);
                    }

                    if ($answers !== $answersAtThisPoint) {
                        throw new \Exception('Unexpected answers received: ' . var_export($answers, true));
                    }

                    if ($default !== null) {
                        throw new \Exception('Unexpected default received: ' . var_export($default, true));
                    }

                    return 'I don\'t know that.';
                },
            ],
            'question5' => [
                'prompt' => 'What is the air-speed velocity of an unladen swallow?',
            ],
        ];

        $io = \Mockery::mock(IOInterface::class);

        $io
            ->expects()
            ->ask($questions['question1']['prompt'], null)
            ->andReturn($expected['question1']);

        $io
            ->expects()
            ->ask($questions['question2']['prompt'], $questions['question2']['default'])
            ->andReturn($expected['question2']);

        $io
            ->expects()
            ->ask($questions['question5']['prompt'], null)
            ->andReturn($expected['question5']);

        $task = \Mockery::mock(Prompt::class, [
            'getIO' => $io,
            'getQuestions->getQuestions' => $questions,
        ]);
        $task->shouldReceive('run')->passthru();
        $task->shouldReceive('getAnswers')->passthru();

        $task->run();

        $this->assertSame($expected, $task->getAnswers());
    }
}
