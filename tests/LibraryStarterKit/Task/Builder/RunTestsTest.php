<?php

declare(strict_types=1);

namespace Ramsey\Test\Dev\LibraryStarterKit\Task\Builder;

use Mockery\MockInterface;
use Ramsey\Dev\LibraryStarterKit\Setup;
use Ramsey\Dev\LibraryStarterKit\Task\Build;
use Ramsey\Dev\LibraryStarterKit\Task\Builder\RunTests;
use Ramsey\Test\Dev\LibraryStarterKit\TestCase;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Process\Process;

use function callableValue;

class RunTestsTest extends TestCase
{
    public function testBuild(): void
    {
        $console = $this->mockery(SymfonyStyle::class);
        $console->expects()->note('Running project tests...');

        $process = $this->mockery(Process::class);
        $process->expects()->mustRun(callableValue());

        $environment = $this->mockery(Setup::class);

        $environment
            ->expects()
            ->getProcess(['composer', 'run-script', 'dev:test:all'])
            ->andReturn($process);

        /** @var Build & MockInterface $build */
        $build = $this->mockery(Build::class, [
            'getAnswers' => $this->answers,
            'getConsole' => $console,
            'getSetup' => $environment,
        ]);

        $builder = new RunTests($build);

        $builder->build();
    }
}
