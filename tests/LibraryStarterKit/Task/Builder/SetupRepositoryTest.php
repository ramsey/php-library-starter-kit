<?php

declare(strict_types=1);

namespace Ramsey\Test\Dev\LibraryStarterKit\Task\Builder;

use Closure;
use Hamcrest\Type\IsCallable;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use Ramsey\Dev\LibraryStarterKit\Setup;
use Ramsey\Dev\LibraryStarterKit\Task\Build;
use Ramsey\Dev\LibraryStarterKit\Task\Builder\SetupRepository;
use Ramsey\Test\Dev\LibraryStarterKit\TestCase;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Process\Process;

use function sprintf;

class SetupRepositoryTest extends TestCase
{
    /**
     * @return list<array{
     *     configUserName: string,
     *     configUserEmail: string,
     *     configDefaultBranch: string,
     *     authorName: string,
     *     authorEmail: string,
     *     expectedName: string,
     *     expectedEmail: string,
     *     expectedDefaultBranch: string,
     * }>
     */
    public static function buildProvider(): array
    {
        return [
            [
                'configUserName' => 'Jane Doe',
                'configUserEmail' => 'jdoe@example.com',
                'configDefaultBranch' => '',
                'authorName' => 'Jane Doe',
                'authorEmail' => 'jdoe@example.com',
                'expectedName' => 'Jane Doe',
                'expectedEmail' => 'jdoe@example.com',
                'expectedDefaultBranch' => 'main',
            ],
            [
                'configUserName' => '',
                'configUserEmail' => '',
                'configDefaultBranch' => 'my-custom-branch-name',
                'authorName' => 'Jane Doe',
                'authorEmail' => 'jdoe@example.com',
                'expectedName' => 'Jane Doe',
                'expectedEmail' => 'jdoe@example.com',
                'expectedDefaultBranch' => 'my-custom-branch-name',
            ],
            [
                'configUserName' => 'Frodo Baggins',
                'configUserEmail' => '',
                'configDefaultBranch' => '',
                'authorName' => 'Jane Doe',
                'authorEmail' => 'jdoe@example.com',
                'expectedName' => 'Jane Doe',
                'expectedEmail' => 'jdoe@example.com',
                'expectedDefaultBranch' => 'main',
            ],
            [
                'configUserName' => '',
                'configUserEmail' => 'frodo@example.com',
                'configDefaultBranch' => '',
                'authorName' => 'Jane Doe',
                'authorEmail' => 'jdoe@example.com',
                'expectedName' => 'Jane Doe',
                'expectedEmail' => 'jdoe@example.com',
                'expectedDefaultBranch' => 'main',
            ],
            [
                'configUserName' => 'Samwise Gamgee',
                'configUserEmail' => 'samwise@example.com',
                'configDefaultBranch' => 'default',
                'authorName' => 'Jane Doe',
                'authorEmail' => 'jdoe@example.com',
                'expectedName' => 'Jane Doe',
                'expectedEmail' => 'jdoe@example.com',
                'expectedDefaultBranch' => 'default',
            ],
        ];
    }

    #[DataProvider('buildProvider')]
    public function testBuild(
        string $configUserName,
        string $configUserEmail,
        string $configDefaultBranch,
        string $authorName,
        string $authorEmail,
        string $expectedName,
        string $expectedEmail,
        string $expectedDefaultBranch,
    ): void {
        $this->answers->authorName = $authorName;
        $this->answers->authorEmail = $authorEmail;

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

        if ($configUserName !== $authorName) {
            $environment
                ->expects()
                ->getProcess(['git', 'config', 'user.name', $expectedName])
                ->andReturn($processMustRunWithCallable);
        }

        if ($configUserEmail !== $authorEmail) {
            $environment
                ->expects()
                ->getProcess(['git', 'config', 'user.email', $expectedEmail])
                ->andReturn($processMustRunWithCallable);
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
                '--author',
                sprintf('%s <%s>', $expectedName, $expectedEmail),
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
