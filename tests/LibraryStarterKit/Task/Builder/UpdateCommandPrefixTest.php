<?php

declare(strict_types=1);

namespace Ramsey\Test\Dev\LibraryStarterKit\Task\Builder;

use ArrayObject;
use Composer\IO\ConsoleIO;
use Mockery\MockInterface;
use Ramsey\Dev\LibraryStarterKit\Answers;
use Ramsey\Dev\LibraryStarterKit\Task\Build;
use Ramsey\Dev\LibraryStarterKit\Task\Builder\UpdateCommandPrefix;
use Ramsey\Test\Dev\LibraryStarterKit\TestCase;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class UpdateCommandPrefixTest extends TestCase
{
    public function testBuildSkipsUpdateWhenDefaultIsUsed(): void
    {
        $io = $this->mockery(ConsoleIO::class);
        $io->expects()->write('<info>Updating command prefix</info>');

        $answers = new Answers();

        /** @var Build & MockInterface $task */
        $task = $this->mockery(Build::class, [
            'getAnswers' => $answers,
            'getIO' => $io,
        ]);

        $builder = new UpdateCommandPrefix($task);

        $builder->build();
    }

    public function testBuild(): void
    {
        $io = $this->mockery(ConsoleIO::class);
        $io->expects()->write('<info>Updating command prefix</info>');

        $answers = new Answers();
        $answers->commandPrefix = 'br';

        $file1 = $this->mockery(SplFileInfo::class, [
            'getRealPath' => '/path/to/app/file1',
            'getContents' => $this->getFileContents(),
        ]);

        $file2 = $this->mockery(SplFileInfo::class, [
            'getRealPath' => '/path/to/app/file2',
            'getContents' => 'file contents with nothing to replace',
        ]);

        $file3 = $this->mockery(SplFileInfo::class, [
            'getRealPath' => '/path/to/app/file3',
            'getContents' => $this->getFileContents(),
        ]);

        $finder = $this->mockery(Finder::class, [
            'getIterator' => new ArrayObject([$file1, $file2, $file3]),
        ]);
        $finder->expects()->in('/path/to/app')->andReturnSelf();
        $finder->expects()->ignoreDotFiles(false)->andReturnSelf();
        $finder->expects()->exclude(
            [
                'build',
                'resources/templates',
                'src/LibraryStarterKit',
                'tests/LibraryStarterKit',
                'vendor',
            ],
        )->andReturnSelf();
        $finder->expects()->files()->andReturnSelf();

        $filesystem = $this->mockery(Filesystem::class);
        $filesystem->expects()->dumpFile(
            '/path/to/app/file1',
            $this->getFileContentsExpected('br'),
        );
        $filesystem->expects()->dumpFile(
            '/path/to/app/file3',
            $this->getFileContentsExpected('br'),
        );

        /** @var Build & MockInterface $task */
        $task = $this->mockery(Build::class, [
            'getAnswers' => $answers,
            'getAppPath' => '/path/to/app',
            'getFilesystem' => $filesystem,
            'getFinder' => $finder,
            'getIO' => $io,
        ]);

        $builder = new UpdateCommandPrefix($task);

        $builder->build();
    }

    public function getFileContents(): string
    {
        return <<<'EOD'
            {
              "vnd:foo": "this is a test",
              "vnd:bar": "another test",
              "vnd:baz": "you can do `composer list vnd`",
              "vnd:qux": "A good namespace is the `vnd` namespace"
            }
            EOD;
    }

    public function getFileContentsExpected(string $commandPrefix): string
    {
        return <<<EOD
            {
              "{$commandPrefix}:foo": "this is a test",
              "{$commandPrefix}:bar": "another test",
              "{$commandPrefix}:baz": "you can do `composer list {$commandPrefix}`",
              "{$commandPrefix}:qux": "A good namespace is the `{$commandPrefix}` namespace"
            }
            EOD;
    }
}
