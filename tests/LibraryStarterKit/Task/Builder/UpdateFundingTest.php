<?php

declare(strict_types=1);

namespace Ramsey\Test\Dev\LibraryStarterKit\Task\Builder;

use Mockery\MockInterface;
use Ramsey\Dev\LibraryStarterKit\Filesystem;
use Ramsey\Dev\LibraryStarterKit\Setup;
use Ramsey\Dev\LibraryStarterKit\Task\Build;
use Ramsey\Dev\LibraryStarterKit\Task\Builder\UpdateFunding;
use Ramsey\Test\Dev\LibraryStarterKit\TestCase;
use Symfony\Component\Console\Style\SymfonyStyle;
use Twig\Environment as TwigEnvironment;

class UpdateFundingTest extends TestCase
{
    public function testBuildWritesTheFileWhenPlatformsWereChosen(): void
    {
        $this->answers->funding = ['github' => ['ramsey']];

        $console = $this->mockery(SymfonyStyle::class);
        $console->expects()->section('Writing .github/FUNDING.yml');

        $filesystem = $this->mockery(Filesystem::class);
        $filesystem
            ->shouldReceive('dumpFile')
            ->once()
            ->withArgs(function (string $path, string $contents) {
                $this->assertSame('/path/to/app/.github/FUNDING.yml', $path);
                $this->assertSame('fundingContents', $contents);

                return true;
            });

        $twig = $this->mockery(TwigEnvironment::class);

        $twig
            ->expects()
            ->render('FUNDING.yml.twig', $this->answers->getArrayCopy())
            ->andReturn('fundingContents');

        $this->buildWith($console, $filesystem, $twig);
    }

    public function testBuildSkipsTheFileWhenFundingIsNull(): void
    {
        $this->assertNull($this->answers->funding);

        $this->assertBuildSkips();
    }

    public function testBuildSkipsTheFileWhenNoPlatformWasChosen(): void
    {
        $this->answers->funding = [];

        $this->assertBuildSkips();
    }

    private function assertBuildSkips(): void
    {
        $console = $this->mockery(SymfonyStyle::class);
        $console->expects()->section('Skipping .github/FUNDING.yml');

        $filesystem = $this->mockery(Filesystem::class);
        $filesystem->shouldReceive('dumpFile')->never();

        $twig = $this->mockery(TwigEnvironment::class);
        $twig->shouldReceive('render')->never();

        $this->buildWith($console, $filesystem, $twig);
    }

    private function buildWith(SymfonyStyle $console, Filesystem $filesystem, TwigEnvironment $twig): void
    {
        $environment = $this->mockery(Setup::class, [
            'getAppPath' => '/path/to/app',
            'getFilesystem' => $filesystem,
            'getTwigEnvironment' => $twig,
        ]);

        $environment
            ->shouldReceive('path')
            ->andReturnUsing(fn (string $path): string => '/path/to/app/' . $path);

        /** @var Build & MockInterface $task */
        $task = $this->mockery(Build::class, [
            'getAnswers' => $this->answers,
            'getConsole' => $console,
            'getSetup' => $environment,
        ]);

        $builder = new UpdateFunding($task);

        $builder->build();
    }
}
