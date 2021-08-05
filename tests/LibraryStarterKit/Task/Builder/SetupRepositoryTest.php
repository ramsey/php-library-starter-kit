<?php

declare(strict_types=1);

namespace Ramsey\Test\Dev\LibraryStarterKit\Task\Builder;

use Closure;
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
    /**
     * @return array<array{configUserName: string, configUserEmail: string, configDefaultBranch: string, expectedDefaultBranch: string}>
     */
    public function buildProvider(): array
    {
        return [
            [
                'configUserName' => '',
                'configUserEmail' => '',
                'configDefaultBranch' => '',
                'expectedDefaultBranch' => 'main',
            ],
            [
                'configUserName' => '',
                'configUserEmail' => '',
                'configDefaultBranch' => 'my-custom-branch-name',
                'expectedDefaultBranch' => 'my-custom-branch-name',
            ],
            [
                'configUserName' => 'Frodo Baggins',
                'configUserEmail' => '',
                'configDefaultBranch' => '',
                'expectedDefaultBranch' => 'main',
            ],
            [
                'configUserName' => '',
                'configUserEmail' => 'frodo@example.com',
                'configDefaultBranch' => '',
                'expectedDefaultBranch' => 'main',
            ],
            [
                'configUserName' => 'Samwise Gamgee',
                'configUserEmail' => 'samwise@example.com',
                'configDefaultBranch' => 'default',
                'expectedDefaultBranch' => 'default',
            ],
        ];
    }

    /**
     * @dataProvider buildProvider
     */
    public function testBuild(
        string $configUserName,
        string $configUserEmail,
        string $configDefaultBranch,
        string $expectedDefaultBranch
    ): void {
        $this->answers->authorName = 'Jane Doe';
        $this->answers->authorEmail = 'jdoe@example.com';

        $console = $this->mockery(SymfonyStyle::class);
        $console->expects()->section('Setting up Git repository');
        $console->expects()->write('a string to write to the console');

        $processConfigUserName = $this->mockery(Process::class);
        $processConfigUserName->expects()->run()->andReturn($configUserName ? 0 : 1);
        $processConfigUserName->expects()->getOutput()->andReturn($configUserName);

        $processConfigUserEmail = $this->mockery(Process::class);
        $processConfigUserEmail->expects()->run()->andReturn($configUserEmail ? 0 : 1);
        $processConfigUserEmail->expects()->getOutput()->andReturn($configUserEmail);

        $processDefaultBranch = $this->mockery(Process::class);
        $processDefaultBranch->expects()->run()->andReturn($configDefaultBranch ? 0 : 1);
        $processDefaultBranch->expects()->getOutput()->andReturn($configDefaultBranch);

        $processMustRun = $this->mockery(Process::class);
        $processMustRun->expects()->mustRun();

        $processMustRunWithCallable = $this->mockery(Process::class);
        $processMustRunWithCallable->shouldReceive('mustRun')->with(new IsCallable())->atLeast()->times(4);

        $environment = $this->mockery(Setup::class);

        $environment
            ->expects()
            ->path('vendor')
            ->andReturn('/path/to/vendor');

        $environment
            ->expects()
            ->getProcess(['git', 'config', 'user.name'])
            ->andReturn($processConfigUserName);

        $environment
            ->expects()
            ->getProcess(['git', 'config', 'user.email'])
            ->andReturn($processConfigUserEmail);

        $environment
            ->expects()
            ->getProcess(['git', 'config', 'init.defaultBranch'])
            ->andReturn($processDefaultBranch);

        $processMustRunWritesOutput = $this->mockery(Process::class);
        $processMustRunWritesOutput
            ->shouldReceive('mustRun')
            ->once()
            ->withArgs(function (Closure $consoleWriter): bool {
                $consoleWriter('out', 'a string to write to the console');

                return true;
            });

        $environment
            ->expects()
            ->getProcess(['git', 'init'])
            ->andReturn($processMustRunWritesOutput);

        if ($configUserName === '') {
            // If there is no global user.name set, then we expect this to be called.
            $environment
                ->expects()
                ->getProcess(['git', 'config', 'user.name', 'Jane Doe'])
                ->andReturn($processMustRunWithCallable);
        } else {
            $environment
                ->expects()
                ->getProcess(['git', 'config', 'user.name', 'Jane Doe'])
                ->never();
            $environment
                ->expects()
                ->getProcess(['git', 'config', 'user.name', $configUserName])
                ->never();
        }

        if ($configUserEmail === '') {
            // If there is no global user.email set, then we expect this to be called.
            $environment
                ->expects()
                ->getProcess(['git', 'config', 'user.email', 'jdoe@example.com'])
                ->andReturn($processMustRunWithCallable);
        } else {
            $environment
                ->expects()
                ->getProcess(['git', 'config', 'user.email', 'jdoe@example.com'])
                ->never();
            $environment
                ->expects()
                ->getProcess(['git', 'config', 'user.email', $configUserEmail])
                ->never();
        }

        $environment
            ->expects()
            ->getProcess(['git', 'branch', '-M', $expectedDefaultBranch])
            ->andReturn($processMustRunWithCallable);

        $environment
            ->expects()
            ->getProcess(['/path/to/vendor/bin/captainhook', 'install', '--force', '--skip-existing'])
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
