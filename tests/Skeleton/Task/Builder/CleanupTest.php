<?php

declare(strict_types=1);

namespace Ramsey\Test\Skeleton\Task\Builder;

use Composer\IO\ConsoleIO;
use Mockery\MockInterface;
use Ramsey\Skeleton\Task\Build;
use Ramsey\Skeleton\Task\Builder\Cleanup;
use Ramsey\Test\Skeleton\SkeletonTestCase;
use Symfony\Component\Filesystem\Filesystem;

use const DIRECTORY_SEPARATOR;

class CleanupTest extends SkeletonTestCase
{
    public function testBuild(): void
    {
        $io = $this->mockery(ConsoleIO::class);
        $io->expects()->write('<info>Cleaning up...</info>');
        $io->expects()->write(
            '<comment>  - Deleted \'/path/to/app/resources' . DIRECTORY_SEPARATOR . 'templates\'.</comment>',
        );
        $io->expects()->write(
            '<comment>  - Deleted \'/path/to/app/src' . DIRECTORY_SEPARATOR . 'Skeleton\'.</comment>',
        );
        $io->expects()->write(
            '<comment>  - Deleted \'/path/to/app/tests' . DIRECTORY_SEPARATOR . 'Skeleton\'.</comment>',
        );
        $io->expects()->write(
            '<comment>  - Deleted \'/path/to/app/.git\'.</comment>',
        );

        $filesystem = $this->mockery(Filesystem::class);
        $filesystem->expects()->remove('/path/to/app/resources' . DIRECTORY_SEPARATOR . 'templates');
        $filesystem->expects()->remove('/path/to/app/src' . DIRECTORY_SEPARATOR . 'Skeleton');
        $filesystem->expects()->remove('/path/to/app/tests' . DIRECTORY_SEPARATOR . 'Skeleton');
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
