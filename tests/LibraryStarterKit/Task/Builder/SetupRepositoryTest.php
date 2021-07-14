<?php

declare(strict_types=1);

namespace Ramsey\Test\Dev\LibraryStarterKit\Task\Builder;

use Hamcrest\Type\IsCallable;
use Mockery\MockInterface;
use Ramsey\Dev\LibraryStarterKit\Setup;
use Ramsey\Dev\LibraryStarterKit\Task\Build;
use Ramsey\Dev\LibraryStarterKit\Task\Builder\SetupRepository;
use Ramsey\Test\Dev\LibraryStarterKit\TestCase;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Process\Process;

class SetupRepositoryTest extends TestCase
{
    public function testBuild(): void
    {
        $this->answers->authorName = 'Jane Doe';
        $this->answers->authorEmail = 'jdoe@example.com';

        $console = $this->mockery(SymfonyStyle::class);
        $console->expects()->section('Setting up Git repository');

        // In this case, the local ~/.gitconfig does not have init.defaultBranch set.
        $processDefaultBranch = $this->mockery(Process::class);
        $processDefaultBranch->expects()->run()->andReturn(1);
        $processDefaultBranch->expects()->getOutput()->andReturn('');

        $processMustRun = $this->mockery(Process::class);
        $processMustRun->expects()->mustRun();

        $processMustRunWithCallable = $this->mockery(Process::class);
        $processMustRunWithCallable->expects()->mustRun(new IsCallable())->times(4);

        $environment = $this->mockery(Setup::class);

        $environment
            ->expects()
            ->getProcess(['git', 'config', 'init.defaultBranch'])
            ->andReturn($processDefaultBranch);

        $environment
            ->expects()
            ->getProcess(['git', 'init', '-b', 'main'])
            ->andReturn($processMustRunWithCallable);

        $environment
            ->expects()
            ->getProcess(['composer', 'run-script', 'post-autoload-dump'])
            ->andReturn($processMustRunWithCallable);

        $environment
            ->expects()
            ->getProcess(['composer', 'dev:build:clean'])
            ->andReturn($processMustRun);

        $environment
            ->expects()
            ->getProcess(['git', 'add', '--all'])
            ->andReturn($processMustRunWithCallable);

        $environment
            ->expects()
            ->getProcess([
                'git',
                'commit',
                '-n',
                '-m',
                'chore: initialize project using ramsey/php-library-starter-kit',
            ])
            ->andReturn($processMustRunWithCallable);

        /** @var Build & MockInterface $build */
        $build = $this->mockery(Build::class, [
            'getAnswers' => $this->answers,
            'getConsole' => $console,
            'getSetup' => $environment,
        ]);

        $builder = new SetupRepository($build);

        $builder->build();
    }
}
