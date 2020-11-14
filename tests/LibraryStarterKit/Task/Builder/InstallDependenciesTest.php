<?php

declare(strict_types=1);

namespace Ramsey\Test\Dev\LibraryStarterKit\Task\Builder;

use Mockery\MockInterface;
use Ramsey\Dev\LibraryStarterKit\Setup;
use Ramsey\Dev\LibraryStarterKit\Task\Build;
use Ramsey\Dev\LibraryStarterKit\Task\Builder\InstallDependencies;
use Ramsey\Test\Dev\LibraryStarterKit\TestCase;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Process;

use function callableValue;

use const DIRECTORY_SEPARATOR;

class InstallDependenciesTest extends TestCase
{
    public function testBuild(): void
    {
        $console = $this->mockery(SymfonyStyle::class);
        $console->expects()->note('Installing dependencies');

        $filesystem = $this->mockery(Filesystem::class);
        $filesystem->expects()->remove([
            '/path/to/app' . DIRECTORY_SEPARATOR . 'composer.lock',
            '/path/to/app' . DIRECTORY_SEPARATOR . 'vendor',
        ]);

        $process = $this->mockery(Process::class);
        $process->expects()->mustRun(callableValue());

        $environment = $this->mockery(Setup::class, [
            'getAppPath' => '/path/to/app',
            'getFileSystem' => $filesystem,
        ]);

        $environment
            ->shouldReceive('path')
            ->andReturnUsing(fn (string $path): string => '/path/to/app' . DIRECTORY_SEPARATOR . $path);

        $environment
            ->expects()
            ->getProcess([
                'composer',
                'update',
                '--no-interaction',
                '--ansi',
                '--no-progress',
            ])
            ->andReturn($process);

        /** @var Build & MockInterface $build */
        $build = $this->mockery(Build::class, [
            'getSetup' => $environment,
            'getConsole' => $console,
        ]);

        $builder = new InstallDependencies($build);

        $builder->build();
    }
}
