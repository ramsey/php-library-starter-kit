<?php

declare(strict_types=1);

namespace Ramsey\Test\Skeleton\Task\Builder;

use ArrayObject;
use Composer\IO\ConsoleIO;
use Mockery\MockInterface;
use Ramsey\Skeleton\Task\Answers;
use Ramsey\Skeleton\Task\Build;
use Ramsey\Skeleton\Task\Builder\UpdateReadme;
use Ramsey\Test\Skeleton\SkeletonTestCase;
use RuntimeException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Twig\Environment as TwigEnvironment;

class UpdateReadmeTest extends SkeletonTestCase
{
    public function testBuild(): void
    {
        $io = $this->mockery(ConsoleIO::class);
        $io->expects()->write('<info>Updating README.md</info>');

        $filesystem = $this->mockery(Filesystem::class);
        $filesystem
            ->shouldReceive('dumpFile')
            ->once()
            ->withArgs(function (string $path, string $contents) {
                $this->assertSame('/path/to/app/README.md', $path);
                $this->assertSame(
                    $this->readmeContentsExpected(),
                    $contents
                );

                return true;
            });

        $finder = $this->mockery(Finder::class, [
            'getIterator' => new ArrayObject(
                [
                    $this->mockery(SplFileInfo::class, ['getContents' => $this->readmeContentsOriginal()]),
                    $this->mockery(SplFileInfo::class, ['getContents' => '']),
                ]
            ),
        ]);
        $finder->expects()->in('/path/to/app')->andReturnSelf();
        $finder->expects()->files()->andReturnSelf();
        $finder->expects()->depth('== 0')->andReturnSelf();
        $finder->expects()->name('README.md');

        $answers = new Answers();
        $answers->codeOfConduct = 'Contributor-1.4';
        $answers->packageName = 'a-vendor/package-name';
        $answers->packageDescription = 'This is a test package.';

        $twig = $this->mockery(TwigEnvironment::class);

        $twig
            ->expects()
            ->render('readme/badges.md.twig', $answers->getArrayCopy())
            ->andReturn('badgesInfo');

        $twig
            ->expects()
            ->render('readme/code-of-conduct.md.twig', $answers->getArrayCopy())
            ->andReturn('codeOfConductInfo');

        $twig
            ->expects()
            ->render('readme/usage.md.twig', $answers->getArrayCopy())
            ->andReturn('usageInfo');

        $twig
            ->expects()
            ->render('readme/copyright.md.twig', $answers->getArrayCopy())
            ->andReturn('copyrightInfo');

        /** @var Build & MockInterface $task */
        $task = $this->mockery(Build::class, [
            'getAnswers' => $answers,
            'getAppPath' => '/path/to/app',
            'getFilesystem' => $filesystem,
            'getFinder' => $finder,
            'getIO' => $io,
            'getTwigEnvironment' => $twig,
        ]);

        $task
            ->shouldReceive('path')
            ->andReturnUsing(fn (string $path): string => '/path/to/app/' . $path);

        $builder = new UpdateReadme($task);

        $builder->build();
    }

    public function testBuildWhenCodeOfConductIsNull(): void
    {
        $io = $this->mockery(ConsoleIO::class);
        $io->expects()->write('<info>Updating README.md</info>');

        $filesystem = $this->mockery(Filesystem::class);
        $filesystem
            ->shouldReceive('dumpFile')
            ->once()
            ->withArgs(function (string $path, string $contents) {
                $this->assertSame('/path/to/app/README.md', $path);
                $this->assertSame(
                    $this->readmeContentsExpectedWithoutCodeOfConduct(),
                    $contents
                );

                return true;
            });

        $finder = $this->mockery(Finder::class, [
            'getIterator' => new ArrayObject(
                [
                    $this->mockery(SplFileInfo::class, ['getContents' => $this->readmeContentsOriginal()]),
                    $this->mockery(SplFileInfo::class, ['getContents' => '']),
                ]
            ),
        ]);
        $finder->expects()->in('/path/to/app')->andReturnSelf();
        $finder->expects()->files()->andReturnSelf();
        $finder->expects()->depth('== 0')->andReturnSelf();
        $finder->expects()->name('README.md');

        $answers = new Answers();
        $answers->codeOfConduct = null;
        $answers->packageName = 'a-vendor/package-name';
        $answers->packageDescription = 'This is a test package.';

        $twig = $this->mockery(TwigEnvironment::class);

        $twig
            ->expects()
            ->render('readme/badges.md.twig', $answers->getArrayCopy())
            ->andReturn('badgesInfo');

        $twig
            ->expects()
            ->render('readme/code-of-conduct.md.twig', $answers->getArrayCopy())
            ->never();

        $twig
            ->expects()
            ->render('readme/usage.md.twig', $answers->getArrayCopy())
            ->andReturn('usageInfo');

        $twig
            ->expects()
            ->render('readme/copyright.md.twig', $answers->getArrayCopy())
            ->andReturn('copyrightInfo');

        /** @var Build & MockInterface $task */
        $task = $this->mockery(Build::class, [
            'getAnswers' => $answers,
            'getAppPath' => '/path/to/app',
            'getFilesystem' => $filesystem,
            'getFinder' => $finder,
            'getIO' => $io,
            'getTwigEnvironment' => $twig,
        ]);

        $task
            ->shouldReceive('path')
            ->andReturnUsing(fn (string $path): string => '/path/to/app/' . $path);

        $builder = new UpdateReadme($task);

        $builder->build();
    }

    public function testBuildThrowsExceptionWhenReadmeCannotBeFound(): void
    {
        $io = $this->mockery(ConsoleIO::class);
        $io->expects()->write('<info>Updating README.md</info>');

        $finder = $this->mockery(Finder::class, [
            'getIterator' => new ArrayObject(
                [
                    $this->mockery(SplFileInfo::class, ['getContents' => null]),
                ]
            ),
        ]);
        $finder->expects()->in('/path/to/app')->andReturnSelf();
        $finder->expects()->files()->andReturnSelf();
        $finder->expects()->depth('== 0')->andReturnSelf();
        $finder->expects()->name('README.md');

        /** @var Build & MockInterface $task */
        $task = $this->mockery(Build::class, [
            'getAppPath' => '/path/to/app',
            'getFinder' => $finder,
            'getIO' => $io,
        ]);

        $builder = new UpdateReadme($task);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Unable to get contents of README.md');

        $builder->build();
    }

    private function readmeContentsOriginal(): string
    {
        return <<<'EOD'
            # <!-- NAME_START -->ramsey/php-library-skeleton<!-- NAME_END -->

            <!-- BADGES_START -->
            [![Source Code][badge-source]][source]

            [badge-source]: http://img.shields.io/badge/source-ramsey/php--library--skeleton-blue.svg?style=flat-square

            [source]: https://github.com/ramsey/php-library-skeleton
            <!-- BADGES_END -->

            <!-- DESC_START -->
            ramsey/php-library-skeleton is a package that may be used to generate a basic
            PHP library project directory structure, complete with many of the starting
            files (i.e. README, LICENSE, GitHub issue templates, PHPUnit configuration,
            etc.) that are commonly found in PHP libraries. You may use the project
            directory that's created as a starting point for creating your own PHP libraries.
            <!-- DESC_END -->

            <!-- COC_START -->
            This project adheres to a [Contributor Code of Conduct](CODE_OF_CONDUCT.md).
            By participating in this project and its community, you are expected to uphold
            this code.
            <!-- COC_END -->

            <!-- USAGE_START -->
            ## Usage

            ``` bash
            composer create-project ramsey/php-library-skeleton YOUR-PROJECT-NAME
            ```
            <!-- USAGE_END -->

            ## Contributing

            Contributions are welcome! Before contributing to this project, familiarize
            yourself with [CONTRIBUTING.md](CONTRIBUTING.md).

            <!-- FAQ_START -->
            ## FAQs

            ### Wait, what, why?

            Because.
            <!-- FAQ_END -->

            <!-- COPYRIGHT_START -->
            ## Copyright and License

            The ramsey/php-library-skeleton library is copyright © [Ben Ramsey](https://benramsey.com)
            and licensed for use under the MIT License (MIT). Please see [LICENSE](LICENSE)
            for more information.
            <!-- COPYRIGHT_END -->
        EOD;
    }

    private function readmeContentsExpected(): string
    {
        return <<<'EOD'
            # a-vendor/package-name

            badgesInfo

            This is a test package.

            codeOfConductInfo

            usageInfo

            ## Contributing

            Contributions are welcome! Before contributing to this project, familiarize
            yourself with [CONTRIBUTING.md](CONTRIBUTING.md).

            

            copyrightInfo
        EOD;
    }

    private function readmeContentsExpectedWithoutCodeOfConduct(): string
    {
        return <<<'EOD'
            # a-vendor/package-name

            badgesInfo

            This is a test package.

            

            usageInfo

            ## Contributing

            Contributions are welcome! Before contributing to this project, familiarize
            yourself with [CONTRIBUTING.md](CONTRIBUTING.md).

            

            copyrightInfo
        EOD;
    }
}
