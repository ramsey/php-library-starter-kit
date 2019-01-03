<?php
declare(strict_types=1);

namespace Ramsey\Skeleton\Test\Task;

use Composer\IO\IOInterface;
use Ramsey\Skeleton\Task\AbstractTask;
use Ramsey\Skeleton\Test\TestCase;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

class AbstractTaskTest extends TestCase
{
    public function testAbstractTestGetters()
    {
        $io = \Mockery::mock(IOInterface::class);
        $filesystem = \Mockery::mock(Filesystem::class);
        $finder = \Mockery::Mock(Finder::class);

        /**
         * @var AbstractTask
         */
        $task = \Mockery::mock(AbstractTask::class, [$io, $filesystem, $finder])->makePartial();

        $this->assertSame($io, $task->getIO());
        $this->assertSame($filesystem, $task->getFilesystem());
        $this->assertSame($finder, $task->getFinder());
    }
}
