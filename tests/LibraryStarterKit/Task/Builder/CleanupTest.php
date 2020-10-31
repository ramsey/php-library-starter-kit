<?php

declare(strict_types=1);

namespace Ramsey\Test\Dev\LibraryStarterKit\Task\Builder;

use Composer\IO\ConsoleIO;
use Mockery\MockInterface;
use Ramsey\Dev\LibraryStarterKit\Task\Build;
use Ramsey\Dev\LibraryStarterKit\Task\Builder\Cleanup;
use Ramsey\Test\Dev\LibraryStarterKit\TestCase;
use Symfony\Component\Filesystem\Filesystem;

use const DIRECTORY_SEPARATOR;

class CleanupTest extends TestCase
{
    public function testBuild(): void
    {
        $io = $this->mockery(ConsoleIO::class);
        $io->expects()->write('<info>Cleaning up...</info>');
        $io->expects()->write(
            '<comment>  - Deleted \'/path/to/app/resources' . DIRECTORY_SEPARATOR . 'templates\'.</comment>',
        );
        $io->expects()->write(
            '<comment>  - Deleted \'/path/to/app/src' . DIRECTORY_SEPARATOR . 'LibraryStarterKit\'.</comment>',
        );
        $io->expects()->write(
            '<comment>  - Deleted \'/path/to/app/tests' . DIRECTORY_SEPARATOR . 'LibraryStarterKit\'.</comment>',
        );
        $io->expects()->write(
            '<comment>  - Deleted \'/path/to/app/.git\'.</comment>',
        );

        $filesystem = $this->mockery(Filesystem::class);
        $filesystem->expects()->remove('/path/to/app/resources' . DIRECTORY_SEPARATOR . 'templates');
        $filesystem->expects()->remove('/path/to/app/src' . DIRECTORY_SEPARATOR . 'LibraryStarterKit');
        $filesystem->expects()->remove('/path/to/app/tests' . DIRECTORY_SEPARATOR . 'LibraryStarterKit');
        $filesystem->expects()->remove('/path/to/app/.git');

        /** @var Build & MockInterface $task */
        $task = $this->mockery(Build::class, [
            'getFilesystem' => $filesystem,
            'getIO' => $io,
        ]);

        $task
            ->shouldReceive('path')
            ->andReturnUsing(fn (string $path): string => '/path/to/app/' . $path);

        $builder = new Cleanup($task);

        $builder->build();
    }
}
