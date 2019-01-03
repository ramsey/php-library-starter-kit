<?php
declare(strict_types=1);

namespace Ramsey\Skeleton\Test\Task\Questions;

use Composer\IO\IOInterface;
use Ramsey\Skeleton\Task\Questions\InstallQuestions;
use Ramsey\Skeleton\Test\TestCase;

class InstallQuestionsTest extends TestCase
{
    public function testGetQuestions()
    {
        $expectedKeys = [
            'authorName',
            'authorEmail',
            'authorUrl',
            'copyrightHolder',
            'copyrightEmail',
            'copyrightUrl',
            'copyrightYear',
            'conductEmail',
            'githubUsername',
            'githubProject',
            'packageName',
            'packageDescription',
            'keywords',
            'namespace',
            'baseClass',
        ];

        $questions = \Mockery::mock(InstallQuestions::class);
        $questions->shouldReceive('getQuestions')->passthru();

        $receivedQuestions = $questions->getQuestions();

        $this->assertSame($expectedKeys, array_keys($receivedQuestions));
    }

    public function testDefaultCustomPromptCallback()
    {
        $answers = [
            'authorName' => 'Jane Doe',
        ];

        $io = \Mockery::mock(IOInterface::class);

        $io
            ->expects()
            ->askAndValidate(
                PHP_EOL . 'What is the name of the copyright holder?' . PHP_EOL . '[Jane Doe] > ',
                anInstanceOf(\Closure::class),
                null,
                'Jane Doe'
            )
            ->andReturn('foobar');

        $questions = \Mockery::mock(InstallQuestions::class);
        $questions->shouldReceive('getQuestions')->passthru();

        $copyrightHolder = $questions->getQuestions()['copyrightHolder'];

        $this->assertSame(
            'foobar',
            $copyrightHolder['operation']($io, $copyrightHolder['prompt'], $answers, $copyrightHolder['default'])
        );
    }

    public function testAskAndValidateCallback()
    {
        $answers = [
            'authorName' => 'Jane Doe',
        ];

        $io = \Mockery::mock(IOInterface::class);

        $io
            ->shouldReceive('askAndValidate')
            ->withArgs(function (string $prompt, \Closure $validator, $attempts, $default) {
                if ($validator($default) === 'Jane Doe') {
                    return true;
                }

                return false;
            })
            ->andReturn('foobar');

        $questions = \Mockery::mock(InstallQuestions::class);
        $questions->shouldReceive('getQuestions')->passthru();

        $copyrightHolder = $questions->getQuestions()['copyrightHolder'];

        $this->assertSame(
            'foobar',
            $copyrightHolder['operation']($io, $copyrightHolder['prompt'], $answers, $copyrightHolder['default'])
        );
    }

    public function testAskAndValidateCallbackThrowsExceptionWhenValueIsEmpty()
    {
        $answers = [
            'authorName' => 'Jane Doe',
        ];

        $io = \Mockery::mock(IOInterface::class);

        $io
            ->shouldReceive('askAndValidate')
            ->withArgs(function (string $prompt, \Closure $validator, $attempts, $default) {
                return $validator('');
            })
            ->andReturn('foobar');

        $questions = \Mockery::mock(InstallQuestions::class);
        $questions->shouldReceive('getQuestions')->passthru();

        $copyrightHolder = $questions->getQuestions()['copyrightHolder'];

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('You must enter a value.');

        $copyrightHolder['operation']($io, $copyrightHolder['prompt'], $answers, $copyrightHolder['default']);
    }
}
