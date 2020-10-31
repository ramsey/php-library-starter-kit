<?php

declare(strict_types=1);

namespace Ramsey\Test\Dev\LibraryStarterKit\Task;

use Composer\IO\IOInterface;
use InvalidArgumentException;
use Mockery\MockInterface;
use Ramsey\Dev\LibraryStarterKit\Task\Answers;
use Ramsey\Dev\LibraryStarterKit\Task\Question;
use Ramsey\Test\Dev\LibraryStarterKit\TestCase;

use function callableValue;

use const PHP_EOL;

class QuestionTest extends TestCase
{
    public function testConstructorGetIOAndGetAnswers(): void
    {
        /** @var IOInterface & MockInterface $io */
        $io = $this->mockery(IOInterface::class);
        $answers = new Answers();

        /** @var Question & MockInterface $question */
        $question = $this->mockery(Question::class, [$io, $answers])->makePartial();

        $this->assertSame($io, $question->getIO());
        $this->assertSame($answers, $question->getAnswers());
    }

    public function testReplaceTokens(): void
    {
        /** @var IOInterface & MockInterface $io */
        $io = $this->mockery(IOInterface::class);
        $answers = new Answers();
        $answers->packageName = 'example/package';

        /** @var Question & MockInterface $question */
        $question = $this->mockery(Question::class, [$io, $answers])->makePartial();

        $this->assertSame(
            'A string with "example/package"',
            $question->replaceTokens('A string with "{{ packageName }}"'),
        );
    }

    public function testShouldSkip(): void
    {
        /** @var Question & MockInterface $question */
        $question = $this->mockery(Question::class);
        $question->shouldReceive('shouldSkip')->passthru();

        $this->assertFalse($question->shouldSkip());
    }

    public function testGetValidatorReturnsEmptyValueWhenOptional(): void
    {
        /** @var Question & MockInterface $question */
        $question = $this->mockery(Question::class, [
            'isOptional' => true,
        ]);
        $question->shouldReceive('getValidator')->passthru();

        $validator = $question->getValidator();

        $this->assertSame('', $validator(''));
    }

    public function testGetValidatorReturnsCallable(): void
    {
        /** @var Question & MockInterface $question */
        $question = $this->mockery(Question::class, [
            'isOptional' => false,
        ]);
        $question->shouldReceive('getValidator')->passthru();

        $this->assertIsCallable($question->getValidator());
    }

    public function testGetValidatorCallableThrowsExceptionForEmptyData(): void
    {
        /** @var Question & MockInterface $question */
        $question = $this->mockery(Question::class, [
            'isOptional' => false,
        ]);
        $question->shouldReceive('getValidator')->passthru();

        $validator = $question->getValidator();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('You must enter a value.');

        $validator('');
    }

    public function testGetValidatorCallableReturnsData(): void
    {
        /** @var Question & MockInterface $question */
        $question = $this->mockery(Question::class, [
            'isOptional' => false,
        ]);
        $question->shouldReceive('getValidator')->passthru();

        $validator = $question->getValidator();

        $this->assertSame('validData', $validator('validData'));
    }

    public function testGetDefault(): void
    {
        /** @var Question & MockInterface $question */
        $question = $this->mockery(Question::class);
        $question->shouldReceive('getDefault')->passthru();

        $this->assertNull($question->getDefault());
    }

    public function testGetPrompt(): void
    {
        $expectedPrompt = PHP_EOL;
        $expectedPrompt .= '<fg=cyan>This is a question.</>';
        $expectedPrompt .= PHP_EOL;
        $expectedPrompt .= '<fg=cyan>> </>';

        /** @var Question & MockInterface $question */
        $question = $this->mockery(Question::class, [
            'getDefault' => null,
            'getQuestion' => 'This is a question.',
            'isOptional' => false,
        ]);

        $question->shouldReceive('getPrompt')->passthru();

        $this->assertSame($expectedPrompt, $question->getPrompt());
    }

    public function testGetPromptWithDefault(): void
    {
        $expectedPrompt = PHP_EOL;
        $expectedPrompt .= '<fg=cyan>This is a question.</>';
        $expectedPrompt .= PHP_EOL;
        $expectedPrompt .= '[<fg=blue>A default value</>] ';
        $expectedPrompt .= '<fg=cyan>> </>';

        /** @var Question & MockInterface $question */
        $question = $this->mockery(Question::class, [
            'getDefault' => 'A default value',
            'getQuestion' => 'This is a question.',
            'isOptional' => false,
        ]);

        $question->shouldReceive('getPrompt')->passthru();

        $this->assertSame($expectedPrompt, $question->getPrompt());
    }

    public function testGetPromptWithOptional(): void
    {
        $expectedPrompt = PHP_EOL;
        $expectedPrompt .= '<fg=cyan>This is a question.</>';
        $expectedPrompt .= PHP_EOL;
        $expectedPrompt .= '<options=bold>optional</> ';
        $expectedPrompt .= '<fg=cyan>> </>';

        /** @var Question & MockInterface $question */
        $question = $this->mockery(Question::class, [
            'getDefault' => null,
            'getQuestion' => 'This is a question.',
            'isOptional' => true,
        ]);

        $question->shouldReceive('getPrompt')->passthru();

        $this->assertSame($expectedPrompt, $question->getPrompt());
    }

    public function testGetPromptWithDefaultAndOptional(): void
    {
        $expectedPrompt = PHP_EOL;
        $expectedPrompt .= '<fg=cyan>This is a question.</>';
        $expectedPrompt .= PHP_EOL;
        $expectedPrompt .= '<options=bold>optional</> ';
        $expectedPrompt .= '[<fg=blue>A default value</>] ';
        $expectedPrompt .= '<fg=cyan>> </>';

        /** @var Question & MockInterface $question */
        $question = $this->mockery(Question::class, [
            'getDefault' => 'A default value',
            'getQuestion' => 'This is a question.',
            'isOptional' => true,
        ]);

        $question->shouldReceive('getPrompt')->passthru();

        $this->assertSame($expectedPrompt, $question->getPrompt());
    }

    public function testAsk(): void
    {
        $expectedPrompt = PHP_EOL;
        $expectedPrompt .= '<fg=cyan>This is a question.</>';
        $expectedPrompt .= PHP_EOL;
        $expectedPrompt .= '[<fg=blue>aDefaultValue</>] ';
        $expectedPrompt .= '<fg=cyan>> </>';

        /** @var IOInterface & MockInterface $io */
        $io = $this->mockery(IOInterface::class);
        $answers = new Answers();

        /** @var Question & MockInterface $question */
        $question = $this->mockery(Question::class, [$io, $answers])->makePartial();
        $question->expects()->getName()->andReturn('packageName');
        $question->expects()->getQuestion()->andReturn('This is a question.');
        $question->expects()->getDefault()->times(3)->andReturn('aDefaultValue');

        $io
            ->expects()
            ->askAndValidate($expectedPrompt, callableValue(), null, 'aDefaultValue')
            ->andReturn('theAnswer');

        $question->ask();

        $this->assertSame('theAnswer', $answers->packageName);
    }

    public function testIsOptional(): void
    {
        /** @var Question & MockInterface $question */
        $question = $this->mockery(Question::class);
        $question->shouldReceive('isOptional')->passthru();

        $this->assertFalse($question->isOptional());
    }

    public function testAskWhenShouldSkipIsTrue(): void
    {
        /** @var IOInterface & MockInterface $io */
        $io = $this->mockery(IOInterface::class);
        $answers = new Answers();

        /** @var Question & MockInterface $question */
        $question = $this->mockery(Question::class, [$io, $answers])->makePartial();
        $question->expects()->shouldSkip()->once()->andReturn(true);

        $question->expects()->getIO()->never();
        $question->expects()->getAnswers()->never();

        $question->ask();
    }
}
