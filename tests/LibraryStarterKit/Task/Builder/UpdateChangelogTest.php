<?php

declare(strict_types=1);

namespace Ramsey\Test\Dev\LibraryStarterKit\Task\Builder;

use Composer\IO\ConsoleIO;
use Mockery\MockInterface;
use Ramsey\Dev\LibraryStarterKit\Answers;
use Ramsey\Dev\LibraryStarterKit\Task\Build;
use Ramsey\Dev\LibraryStarterKit\Task\Builder\UpdateChangelog;
use Ramsey\Test\Dev\LibraryStarterKit\TestCase;
use Symfony\Component\Filesystem\Filesystem;
use Twig\Environment as TwigEnvironment;

class UpdateChangelogTest extends TestCase
{
    public function testBuild(): void
    {
        $io = $this->mockery(ConsoleIO::class);
        $io->expects()->write('<info>Updating CHANGELOG.md</info>');

        $filesystem = $this->mockery(Filesystem::class);
        $filesystem
            ->shouldReceive('dumpFile')
            ->once()
            ->withArgs(function (string $path, string $contents) {
                $this->assertSame('/path/to/app/CHANGELOG.md', $path);
                $this->assertSame('changelogContents', $contents);

                return true;
            });

        $twig = $this->mockery(TwigEnvironment::class);
        $answers = new Answers();

        $twig
            ->expects()
            ->render('CHANGELOG.md.twig', $answers->getArrayCopy())
            ->andReturn('changelogContents');

        /** @var Build & MockInterface $task */
        $task = $this->mockery(Build::class, [
            'getAnswers' => $answers,
            'getAppPath' => '/path/to/app',
            'getFilesystem' => $filesystem,
            'getIO' => $io,
            'getTwigEnvironment' => $twig,
        ]);

        $task
            ->shouldReceive('path')
            ->andReturnUsing(fn (string $path): string => '/path/to/app/' . $path);

        $builder = new UpdateChangelog($task);

        $builder->build();
    }
}
