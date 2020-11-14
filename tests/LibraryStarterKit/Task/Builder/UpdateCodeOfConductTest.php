<?php

declare(strict_types=1);

namespace Ramsey\Test\Dev\LibraryStarterKit\Task\Builder;

use Mockery\MockInterface;
use Ramsey\Dev\LibraryStarterKit\Answers;
use Ramsey\Dev\LibraryStarterKit\Setup;
use Ramsey\Dev\LibraryStarterKit\Task\Build;
use Ramsey\Dev\LibraryStarterKit\Task\Builder\UpdateCodeOfConduct;
use Ramsey\Test\Dev\LibraryStarterKit\TestCase;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;
use Twig\Environment as TwigEnvironment;

use const DIRECTORY_SEPARATOR;

class UpdateCodeOfConductTest extends TestCase
{
    public function testBuild(): void
    {
        $console = $this->mockery(SymfonyStyle::class);
        $console->expects()->note('Updating CODE_OF_CONDUCT.md');

        $filesystem = $this->mockery(Filesystem::class);
        $filesystem
            ->shouldReceive('dumpFile')
            ->once()
            ->withArgs(function (string $path, string $contents) {
                $this->assertSame(
                    '/path/to/app/CODE_OF_CONDUCT.md',
                    $path,
                );
                $this->assertSame('codeOfConductContents', $contents);

                return true;
            });

        $answers = new Answers();
        $answers->codeOfConduct = 'Contributor-1.4';

        $twig = $this->mockery(TwigEnvironment::class);

        $twig
            ->expects()
            ->render(
                'code-of-conduct' . DIRECTORY_SEPARATOR . 'Contributor-1.4.md.twig',
                $answers->getArrayCopy(),
            )
            ->andReturn('codeOfConductContents');

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
            'getAnswers' => $answers,
            'getConsole' => $console,
            'getSetup' => $environment,
        ]);

        $builder = new UpdateCodeOfConduct($build);

        $builder->build();
    }

    public function testBuildRemovesCodeOfConductFile(): void
    {
        $console = $this->mockery(SymfonyStyle::class);
        $console->expects()->note('Removing CODE_OF_CONDUCT.md');

        $filesystem = $this->mockery(Filesystem::class);
        $filesystem->shouldReceive('dumpFile')->never();
        $filesystem->expects()->remove('/path/to/app/CODE_OF_CONDUCT.md');

        $answers = new Answers();
        $twig = $this->mockery(TwigEnvironment::class);

        $twig->shouldReceive('render')->never();

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
            'getAnswers' => $answers,
            'getConsole' => $console,
            'getSetup' => $environment,
        ]);

        $builder = new UpdateCodeOfConduct($build);

        $builder->build();
    }
}
