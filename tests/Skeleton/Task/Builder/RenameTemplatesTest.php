<?php

declare(strict_types=1);

namespace Ramsey\Test\Skeleton\Task\Builder;

use ArrayObject;
use Composer\IO\ConsoleIO;
use Mockery\MockInterface;
use Ramsey\Skeleton\Task\Build;
use Ramsey\Skeleton\Task\Builder\RenameTemplates;
use Ramsey\Test\Skeleton\SkeletonTestCase;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

use function implode;

use const DIRECTORY_SEPARATOR;

class RenameTemplatesTest extends SkeletonTestCase
{
    public function testBuild(): void
    {
        $pathParts = [
            'path',
            'to',
            'a',
            'filename.template',
        ];

        $expectedPathParts = [
            'path',
            'to',
            'a',
            'filename',
        ];

        $path = implode(DIRECTORY_SEPARATOR, $pathParts);
        $expectedPath = implode(DIRECTORY_SEPARATOR, $expectedPathParts);

        $io = $this->mockery(ConsoleIO::class);
        $io->expects()->write('<info>Renaming template files</info>');
        $io->expects()->write("<comment>Renaming '{$path}' to '{$expectedPath}'.</comment>");

        $filesystem = $this->mockery(Filesystem::class);
        $filesystem->expects()->rename($path, $expectedPath);

        $file1 = $this->mockery(SplFileInfo::class, [
            'getRealPath' => $path,
        ]);

        $finder = $this->mockery(Finder::class, [
            'getIterator' => new ArrayObject([$file1]),
        ]);

        $finder->expects()->ignoreDotFiles(false)->andReturnSelf();
        $finder->expects()->exclude(['build', 'vendor'])->andReturnSelf();
        $finder->expects()->in('/path/to/app')->andReturnSelf();
        $finder->expects()->name('*.template')->andReturnSelf();
        $finder->expects()->name('.*.template');

        /** @var Build & MockInterface $task */
        $task = $this->mockery(Build::class, [
            'getAppPath' => '/path/to/app',
            'getFilesystem' => $filesystem,
            'getFinder' => $finder,
            'getIO' => $io,
        ]);

        $renameTemplates = new RenameTemplates($task);

        $renameTemplates->build();
    }
}
