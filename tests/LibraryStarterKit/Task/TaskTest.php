<?php

declare(strict_types=1);

namespace Ramsey\Test\Dev\LibraryStarterKit\Task;

use Composer\IO\IOInterface;
use Mockery\MockInterface;
use Ramsey\Dev\LibraryStarterKit\Task\Task;
use Ramsey\Test\Dev\LibraryStarterKit\TestCase;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Process\Process;

use const DIRECTORY_SEPARATOR;

class TaskTest extends TestCase
{
    public function testGetters(): void
    {
        /** @var IOInterface & MockInterface $io */
        $io = $this->mockery(IOInterface::class);

        /** @var Filesystem & MockInterface $filesystem */
        $filesystem = $this->mockery(Filesystem::class);

        /** @var Finder & MockInterface $finder */
        $finder = $this->mockery(Finder::class);

        /** @var Task & MockInterface $task */
        $task = $this->mockery(Task::class, ['/path/to/app', $io, $filesystem, $finder])->makePartial();

        $this->assertSame($io, $task->getIO());
        $this->assertSame($filesystem, $task->getFilesystem());
        $this->assertNotSame($finder, $task->getFinder());
        $this->assertInstanceOf(Finder::class, $task->getFinder());
    }

    public function testGetProcess(): void
    {
        /** @var IOInterface & MockInterface $io */
        $io = $this->mockery(IOInterface::class);

        /** @var Filesystem & MockInterface $filesystem */
        $filesystem = $this->mockery(Filesystem::class);

        /** @var Finder & MockInterface $finder */
        $finder = $this->mockery(Finder::class);

        /** @var Task & MockInterface $task */
        $task = $this->mockery(Task::class, ['/path/to/app', $io, $filesystem, $finder])->makePartial();

        $this->assertInstanceOf(Process::class, $task->getProcess(['echo', 'foo']));
    }

    public function testPath(): void
    {
        /** @var IOInterface & MockInterface $io */
        $io = $this->mockery(IOInterface::class);

        /** @var Filesystem & MockInterface $filesystem */
        $filesystem = $this->mockery(Filesystem::class);

        /** @var Finder & MockInterface $finder */
        $finder = $this->mockery(Finder::class);

        /** @var Task & MockInterface $task */
        $task = $this->mockery(Task::class, ['/path/to/app', $io, $filesystem, $finder])->makePartial();

        $this->assertSame(
            '/path/to/app' . DIRECTORY_SEPARATOR . 'someFile.twig',
            $task->path('someFile.twig'),
        );
    }

    public function testStreamProcessOutput(): void
    {
        /** @var Filesystem & MockInterface $filesystem */
        $filesystem = $this->mockery(Filesystem::class);

        /** @var Finder & MockInterface $finder */
        $finder = $this->mockery(Finder::class);

        /** @var IOInterface & MockInterface $io */
        $io = $this->mockery(IOInterface::class);
        $io->expects()->writeError('this is an error', false);
        $io->expects()->write('this is some output', false);

        /** @var Task & MockInterface $task */
        $task = $this->mockery(Task::class, ['/path/to/app', $io, $filesystem, $finder])->makePartial();

        $streamProcessOutput = $task->streamProcessOutput();

        $streamProcessOutput(Process::OUT, 'this is some output');
        $streamProcessOutput(Process::ERR, 'this is an error');
    }
}
