<?php

declare(strict_types=1);

namespace Ramsey\Test\Dev\LibraryStarterKit\Task\Builder;

use Mockery\MockInterface;
use Ramsey\Dev\LibraryStarterKit\Answers;
use Ramsey\Dev\LibraryStarterKit\Setup;
use Ramsey\Dev\LibraryStarterKit\Task\Build;
use Ramsey\Dev\LibraryStarterKit\Task\Builder\SetupRepository;
use Ramsey\Test\Dev\LibraryStarterKit\TestCase;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Process\Process;

use function callableValue;

class SetupRepositoryTest extends TestCase
{
    public function testBuild(): void
    {
        $answers = new Answers();
        $answers->authorName = 'Jane Doe';
        $answers->authorEmail = 'jdoe@example.com';

        $console = $this->mockery(SymfonyStyle::class);
        $console->expects()->note('Setting up Git repository');

        $processMustRun = $this->mockery(Process::class);
        $processMustRun->expects()->mustRun()->times(3);

        $processMustRunWithCallable = $this->mockery(Process::class);
        $processMustRunWithCallable->expects()->mustRun(callableValue())->times(4);

        $environment = $this->mockery(Setup::class);

        $environment
            ->expects()
            ->getProcess(['git', 'init'])
            ->andReturn($processMustRunWithCallable);

        $environment
            ->expects()
            ->getProcess(['composer', 'run-script', 'post-autoload-dump'])
            ->andReturn($processMustRunWithCallable);

        $environment
            ->expects()
            ->getProcess(['composer', 'run-script', 'dev:build:clean'])
            ->andReturn($processMustRun);

        $environment
            ->expects()
            ->getProcess(['git', 'config', 'user.name', 'Jane Doe'])
            ->andReturn($processMustRun);

        $environment
            ->expects()
            ->getProcess(['git', 'config', 'user.email', 'jdoe@example.com'])
            ->andReturn($processMustRun);

        $environment
            ->expects()
            ->getProcess(['git', 'add', '--all'])
            ->andReturn($processMustRunWithCallable);

        $environment
            ->expects()
            ->getProcess(['git', 'commit', '-m', 'chore: initialize project using ramsey/php-library-starter-kit'])
            ->andReturn($processMustRunWithCallable);

        /** @var Build & MockInterface $build */
        $build = $this->mockery(Build::class, [
            'getAnswers' => $answers,
            'getConsole' => $console,
            'getSetup' => $environment,
        ]);

        $builder = new SetupRepository($build);

        $builder->build();
    }

    public function testBuildWithoutAuthor(): void
    {
        $answers = new Answers();

        $console = $this->mockery(SymfonyStyle::class);
        $console->expects()->note('Setting up Git repository');

        $processMustRun = $this->mockery(Process::class);
        $processMustRun->expects()->mustRun()->once();

        $processMustRunWithCallable = $this->mockery(Process::class);
        $processMustRunWithCallable->expects()->mustRun(callableValue())->times(4);

        $environment = $this->mockery(Setup::class);

        $environment
            ->expects()
            ->getProcess(['git', 'init'])
            ->andReturn($processMustRunWithCallable);

        $environment
            ->expects()
            ->getProcess(['composer', 'run-script', 'post-autoload-dump'])
            ->andReturn($processMustRunWithCallable);

        $environment
            ->expects()
            ->getProcess(['composer', 'run-script', 'dev:build:clean'])
            ->andReturn($processMustRun);

        $environment
            ->expects()
            ->getProcess(['git', 'config', 'user.name', 'Jane Doe'])
            ->never();

        $environment
            ->expects()
            ->getProcess(['git', 'config', 'user.email', 'jdoe@example.com'])
            ->never();

        $environment
            ->expects()
            ->getProcess(['git', 'add', '--all'])
            ->andReturn($processMustRunWithCallable);

        $environment
            ->expects()
            ->getProcess(['git', 'commit', '-m', 'chore: initialize project using ramsey/php-library-starter-kit'])
            ->andReturn($processMustRunWithCallable);

        /** @var Build & MockInterface $build */
        $build = $this->mockery(Build::class, [
            'getAnswers' => $answers,
            'getConsole' => $console,
            'getSetup' => $environment,
        ]);

        $builder = new SetupRepository($build);

        $builder->build();
    }
}
