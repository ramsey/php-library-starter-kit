<?php

declare(strict_types=1);

namespace Ramsey\Test\Skeleton\Task\Builder;

use Composer\IO\ConsoleIO;
use Mockery\MockInterface;
use Ramsey\Skeleton\Task\Answers;
use Ramsey\Skeleton\Task\Build;
use Ramsey\Skeleton\Task\Builder\SetupRepository;
use Ramsey\Test\Skeleton\SkeletonTestCase;
use Symfony\Component\Process\Process;

class SetupRepositoryTest extends SkeletonTestCase
{
    public function testBuild(): void
    {
        $answers = new Answers();
        $answers->authorName = 'Jane Doe';
        $answers->authorEmail = 'jdoe@example.com';

        $io = $this->mockery(ConsoleIO::class);
        $io->expects()->write('<info>Setting up Git repository</info>');

        $processMustRun = $this->mockery(Process::class);
        $processMustRun->expects()->mustRun()->times(3);

        $processMustRunWithCallable = $this->mockery(Process::class);
        $processMustRunWithCallable->expects()->mustRun(callableValue())->times(4);

        /** @var Build & MockInterface $task */
        $task = $this->mockery(Build::class, [
            'getAnswers' => $answers,
            'getIO' => $io,
            'streamProcessOutput' => fn () => null,
        ]);

        $task
            ->expects()
            ->getProcess(['git', 'init'])
            ->andReturn($processMustRunWithCallable);

        $task
            ->expects()
            ->getProcess(['composer', 'run-script', 'vnd:build:clean'])
            ->andReturn($processMustRun);

        $task
            ->expects()
            ->getProcess(['git', 'config', 'user.name', 'Jane Doe'])
            ->andReturn($processMustRun);

        $task
            ->expects()
            ->getProcess(['git', 'config', 'user.email', 'jdoe@example.com'])
            ->andReturn($processMustRun);

        $task
            ->expects()
            ->getProcess(['yarn', 'add', 'husky'])
            ->andReturn($processMustRunWithCallable);

        $task
            ->expects()
            ->getProcess(['git', 'add', '--all'])
            ->andReturn($processMustRunWithCallable);

        $task
            ->expects()
            ->getProcess(['git', 'commit', '-m', 'chore: initialize project using ramsey/php-library-skeleton'])
            ->andReturn($processMustRunWithCallable);

        $builder = new SetupRepository($task);

        $builder->build();
    }

    public function testBuildWithoutAuthor(): void
    {
        $answers = new Answers();

        $io = $this->mockery(ConsoleIO::class);
        $io->expects()->write('<info>Setting up Git repository</info>');

        $processMustRun = $this->mockery(Process::class);
        $processMustRun->expects()->mustRun()->once();

        $processMustRunWithCallable = $this->mockery(Process::class);
        $processMustRunWithCallable->expects()->mustRun(callableValue())->times(4);

        /** @var Build & MockInterface $task */
        $task = $this->mockery(Build::class, [
            'getAnswers' => $answers,
            'getIO' => $io,
            'streamProcessOutput' => fn () => null,
        ]);

        $task
            ->expects()
            ->getProcess(['git', 'init'])
            ->andReturn($processMustRunWithCallable);

        $task
            ->expects()
            ->getProcess(['composer', 'run-script', 'vnd:build:clean'])
            ->andReturn($processMustRun);

        $task
            ->expects()
            ->getProcess(['git', 'config', 'user.name', 'Jane Doe'])
            ->never();

        $task
            ->expects()
            ->getProcess(['git', 'config', 'user.email', 'jdoe@example.com'])
            ->never();

        $task
            ->expects()
            ->getProcess(['yarn', 'add', 'husky'])
            ->andReturn($processMustRunWithCallable);

        $task
            ->expects()
            ->getProcess(['git', 'add', '--all'])
            ->andReturn($processMustRunWithCallable);

        $task
            ->expects()
            ->getProcess(['git', 'commit', '-m', 'chore: initialize project using ramsey/php-library-skeleton'])
            ->andReturn($processMustRunWithCallable);

        $builder = new SetupRepository($task);

        $builder->build();
    }
}
