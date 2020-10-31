<?php

declare(strict_types=1);

namespace Ramsey\Test\Skeleton\Task\Builder;

use Composer\IO\ConsoleIO;
use Mockery\MockInterface;
use Ramsey\Skeleton\Task\Answers;
use Ramsey\Skeleton\Task\Build;
use Ramsey\Skeleton\Task\Builder\RunTests;
use Ramsey\Test\Skeleton\SkeletonTestCase;
use Symfony\Component\Process\Process;

use function callableValue;

class RunTestsTest extends SkeletonTestCase
{
    public function testBuild(): void
    {
        $io = $this->mockery(ConsoleIO::class);
        $io->expects()->write('<info>Running project tests...</info>');

        $process = $this->mockery(Process::class);
        $process->expects()->mustRun(callableValue());

        /** @var Build & MockInterface $task */
        $task = $this->mockery(Build::class, [
            'getAnswers' => new Answers(),
            'getIO' => $io,
            'streamProcessOutput' => fn () => null,
        ]);

        $task
            ->expects()
            ->getProcess(['composer', 'run-script', 'vnd:test:all'])
            ->andReturn($process);

        $builder = new RunTests($task);

        $builder->build();
    }
}
