<?php

declare(strict_types=1);

namespace Ramsey\Test\Dev\LibraryStarterKit\Task\Builder;

use Mockery\MockInterface;
use Ramsey\Dev\LibraryStarterKit\Filesystem;
use Ramsey\Dev\LibraryStarterKit\Setup;
use Ramsey\Dev\LibraryStarterKit\Task\Build;
use Ramsey\Dev\LibraryStarterKit\Task\Builder\UpdateChangelog;
use Ramsey\Test\Dev\LibraryStarterKit\TestCase;
use Symfony\Component\Console\Style\SymfonyStyle;
use Twig\Environment as TwigEnvironment;

class UpdateChangelogTest extends TestCase
{
    public function testBuild(): void
    {
        $console = $this->mockery(SymfonyStyle::class);
        $console->expects()->note('Updating CHANGELOG.md');

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

        $twig
            ->expects()
            ->render('CHANGELOG.md.twig', $this->answers->getArrayCopy())
            ->andReturn('changelogContents');

        $environment = $this->mockery(Setup::class, [
            'getAppPath' => '/path/to/app',
            'getFilesystem' => $filesystem,
            'getTwigEnvironment' => $twig,
        ]);

        $environment
            ->shouldReceive('path')
            ->andReturnUsing(fn (string $path): string => '/path/to/app/' . $path);

        /** @var Build & MockInterface $build */
        $build = $this->mockery(Build::class, [
            'getAnswers' => $this->answers,
            'getConsole' => $console,
            'getSetup' => $environment,
        ]);

        $builder = new UpdateChangelog($build);

        $builder->build();
    }
}
