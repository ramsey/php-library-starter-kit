<?php

declare(strict_types=1);

namespace Ramsey\Test\Skeleton\Task\Builder;

use Composer\IO\ConsoleIO;
use Mockery\MockInterface;
use Ramsey\Skeleton\Task\Answers;
use Ramsey\Skeleton\Task\Build;
use Ramsey\Skeleton\Task\Builder\UpdateCodeOfConduct;
use Ramsey\Test\Skeleton\SkeletonTestCase;
use Symfony\Component\Filesystem\Filesystem;
use Twig\Environment as TwigEnvironment;

use const DIRECTORY_SEPARATOR;

class UpdateCodeOfConductTest extends SkeletonTestCase
{
    public function testBuild(): void
    {
        $io = $this->mockery(ConsoleIO::class);
        $io->expects()->write('<info>Updating CODE_OF_CONDUCT.md</info>');

        $filesystem = $this->mockery(Filesystem::class);
        $filesystem
            ->shouldReceive('dumpFile')
            ->once()
            ->withArgs(function (string $path, string $contents) {
                $this->assertSame(
                    '/path/to/app/CODE_OF_CONDUCT.md',
                    $path
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
                $answers->getArrayCopy()
            )
            ->andReturn('codeOfConductContents');

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

        $builder = new UpdateCodeOfConduct($task);

        $builder->build();
    }

    public function testBuildRemovesCodeOfConductFile(): void
    {
        $io = $this->mockery(ConsoleIO::class);
        $io->expects()->write('<info>Removing CODE_OF_CONDUCT.md</info>');

        $filesystem = $this->mockery(Filesystem::class);
        $filesystem->shouldReceive('dumpFile')->never();
        $filesystem->expects()->remove('/path/to/app/CODE_OF_CONDUCT.md');

        $answers = new Answers();
        $twig = $this->mockery(TwigEnvironment::class);

        $twig->shouldReceive('render')->never();

        /** @var Build & MockInterface $task */
        $task = $this->mockery(
            Build::class,
            [
                'getAnswers' => $answers,
                'getAppPath' => '/path/to/app',
                'getFilesystem' => $filesystem,
                'getIO' => $io,
                'getTwigEnvironment' => $twig,
            ]
        );

        $task
            ->shouldReceive('path')
            ->andReturnUsing(fn(string $path): string => '/path/to/app/' . $path);

        $builder = new UpdateCodeOfConduct($task);

        $builder->build();
    }
}
