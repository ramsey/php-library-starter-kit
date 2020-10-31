<?php

declare(strict_types=1);

namespace Ramsey\Test\Dev\LibraryStarterKit\Task\Builder;

use Composer\IO\ConsoleIO;
use Mockery\MockInterface;
use Ramsey\Dev\LibraryStarterKit\Task\Build;
use Ramsey\Dev\LibraryStarterKit\Task\Builder\InstallDependencies;
use Ramsey\Test\Dev\LibraryStarterKit\TestCase;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Process;

use function callableValue;

use const DIRECTORY_SEPARATOR;

class InstallDependenciesTest extends TestCase
{
    public function testBuild(): void
    {
        $io = $this->mockery(ConsoleIO::class);
        $io->expects()->write('<info>Installing dependencies</info>');

        $filesystem = $this->mockery(Filesystem::class);
        $filesystem->expects()->remove(
            [
                '/path/to/app' . DIRECTORY_SEPARATOR . 'composer.lock',
                '/path/to/app' . DIRECTORY_SEPARATOR . 'vendor',
            ],
        );

        $process1 = $this->mockery(Process::class);
        $process1->expects()->mustRun();

        $process2 = $this->mockery(Process::class);
        $process2->expects()->mustRun(callableValue());

        /** @var Build & MockInterface $task */
        $task = $this->mockery(Build::class, [
            'getAppPath' => '/path/to/app',
            'getFilesystem' => $filesystem,
            'getIO' => $io,
            'streamProcessOutput' => fn () => null,
        ]);

        $task
            ->shouldReceive('path')
            ->andReturnUsing(fn (string $path): string => '/path/to/app' . DIRECTORY_SEPARATOR . $path);

        $task
            ->expects()
            ->getProcess(
                [
                    'composer',
                    'remove',
                    '--no-interaction',
                    '--ansi',
                    '--dev',
                    '--no-update',
                    'composer/composer',
                ],
            )
            ->andReturn($process1);

        $task
            ->expects()
            ->getProcess(
                [
                    'composer',
                    'install',
                    '--no-interaction',
                    '--ansi',
                    '--no-progress',
                    '--no-suggest',
                ],
            )
            ->andReturn($process2);

        $builder = new InstallDependencies($task);

        $builder->build();
    }
}
