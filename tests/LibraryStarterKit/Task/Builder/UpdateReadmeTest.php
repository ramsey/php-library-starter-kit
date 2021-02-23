<?php

declare(strict_types=1);

namespace Ramsey\Test\Dev\LibraryStarterKit\Task\Builder;

use ArrayObject;
use Mockery\MockInterface;
use Ramsey\Dev\LibraryStarterKit\Filesystem;
use Ramsey\Dev\LibraryStarterKit\Setup;
use Ramsey\Dev\LibraryStarterKit\Task\Build;
use Ramsey\Dev\LibraryStarterKit\Task\Builder\UpdateReadme;
use Ramsey\Test\Dev\LibraryStarterKit\TestCase;
use RuntimeException;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Twig\Environment as TwigEnvironment;

class UpdateReadmeTest extends TestCase
{
    public function testBuild(): void
    {
        $console = $this->mockery(SymfonyStyle::class);
        $console->expects()->note('Updating README.md');

        $filesystem = $this->mockery(Filesystem::class);
        $filesystem
            ->shouldReceive('dumpFile')
            ->once()
            ->withArgs(function (string $path, string $contents) {
                $this->assertSame('/path/to/app/README.md', $path);
                $this->assertSame(
                    $this->readmeContentsExpected(),
                    $contents,
                );

                return true;
            });

        $finder = $this->mockery(Finder::class, [
            'getIterator' => new ArrayObject(
                [
                    $this->mockery(SplFileInfo::class, ['getContents' => $this->readmeContentsOriginal()]),
                    $this->mockery(SplFileInfo::class, ['getContents' => '']),
                ],
            ),
        ]);
        $finder->expects()->in('/path/to/app')->andReturnSelf();
        $finder->expects()->files()->andReturnSelf();
        $finder->expects()->depth('== 0')->andReturnSelf();
        $finder->expects()->name('README.md');

        $this->answers->codeOfConduct = 'Contributor-1.4';
        $this->answers->packageName = 'a-vendor/package-name';
        $this->answers->packageDescription = 'This is a test package.';

        $twig = $this->mockery(TwigEnvironment::class);

        $twig
            ->expects()
            ->render('readme/badges.md.twig', $this->answers->getArrayCopy())
            ->andReturn('badgesInfo');

        $twig
            ->expects()
            ->render('readme/description.md.twig', $this->answers->getArrayCopy())
            ->andReturn('descriptionInfo');

        $twig
            ->expects()
            ->render('readme/code-of-conduct.md.twig', $this->answers->getArrayCopy())
            ->andReturn('codeOfConductInfo');

        $twig
            ->expects()
            ->render('readme/usage.md.twig', $this->answers->getArrayCopy())
            ->andReturn('usageInfo');

        $twig
            ->expects()
            ->render('readme/copyright.md.twig', $this->answers->getArrayCopy())
            ->andReturn('copyrightInfo');

        $environment = $this->mockery(Setup::class, [
            'getAppPath' => '/path/to/app',
            'getFilesystem' => $filesystem,
            'getFinder' => $finder,
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

        $builder = new UpdateReadme($task);

        $builder->build();
    }

    public function testBuildWhenCodeOfConductIsNull(): void
    {
        $console = $this->mockery(SymfonyStyle::class);
        $console->expects()->note('Updating README.md');

        $filesystem = $this->mockery(Filesystem::class);
        $filesystem
            ->shouldReceive('dumpFile')
            ->once()
            ->withArgs(function (string $path, string $contents) {
                $this->assertSame('/path/to/app/README.md', $path);
                $this->assertSame(
                    $this->readmeContentsExpectedWithoutCodeOfConduct(),
                    $contents,
                );

                return true;
            });

        $finder = $this->mockery(Finder::class, [
            'getIterator' => new ArrayObject(
                [
                    $this->mockery(SplFileInfo::class, ['getContents' => $this->readmeContentsOriginal()]),
                    $this->mockery(SplFileInfo::class, ['getContents' => '']),
                ],
            ),
        ]);
        $finder->expects()->in('/path/to/app')->andReturnSelf();
        $finder->expects()->files()->andReturnSelf();
        $finder->expects()->depth('== 0')->andReturnSelf();
        $finder->expects()->name('README.md');

        $this->answers->codeOfConduct = null;
        $this->answers->packageName = 'a-vendor/package-name';
        $this->answers->packageDescription = 'This is a test package.';

        $twig = $this->mockery(TwigEnvironment::class);

        $twig
            ->expects()
            ->render('readme/badges.md.twig', $this->answers->getArrayCopy())
            ->andReturn('badgesInfo');

        $twig
            ->expects()
            ->render('readme/description.md.twig', $this->answers->getArrayCopy())
            ->andReturn('descriptionInfo');

        $twig
            ->expects()
            ->render('readme/code-of-conduct.md.twig', $this->answers->getArrayCopy())
            ->never();

        $twig
            ->expects()
            ->render('readme/usage.md.twig', $this->answers->getArrayCopy())
            ->andReturn('usageInfo');

        $twig
            ->expects()
            ->render('readme/copyright.md.twig', $this->answers->getArrayCopy())
            ->andReturn('copyrightInfo');

        $environment = $this->mockery(Setup::class, [
            'getAppPath' => '/path/to/app',
            'getFilesystem' => $filesystem,
            'getFinder' => $finder,
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

        $builder = new UpdateReadme($task);

        $builder->build();
    }

    public function testBuildThrowsExceptionWhenReadmeCannotBeFound(): void
    {
        $console = $this->mockery(SymfonyStyle::class);
        $console->expects()->note('Updating README.md');

        $finder = $this->mockery(Finder::class, [
            'getIterator' => new ArrayObject(
                [
                    $this->mockery(SplFileInfo::class, ['getContents' => null]),
                ],
            ),
        ]);
        $finder->expects()->in('/path/to/app')->andReturnSelf();
        $finder->expects()->files()->andReturnSelf();
        $finder->expects()->depth('== 0')->andReturnSelf();
        $finder->expects()->name('README.md');

        $environment = $this->mockery(Setup::class, [
            'getAppPath' => '/path/to/app',
            'getFinder' => $finder,
        ]);

        /** @var Build & MockInterface $task */
        $task = $this->mockery(Build::class, [
            'getAppPath' => '/path/to/app',
            'getConsole' => $console,
            'getSetup' => $environment,
        ]);

        $builder = new UpdateReadme($task);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Unable to get contents of README.md');

        $builder->build();
    }

    private function readmeContentsOriginal(): string
    {
        return <<<'EOD'
        # <!-- NAME_START -->ramsey/php-library-starter-kit<!-- NAME_END -->

        <!-- BADGES_START -->
        [![Source Code][badge-source]][source]

        [badge-source]: http://img.shields.io/badge/source-ramsey/php--library--starter--kit-blue.svg?style=flat-square

        [source]: https://github.com/ramsey/php-library-starter-kit
        <!-- BADGES_END -->

        <!-- DESC_START -->
        ramsey/php-library-starter-kit is a package that may be used to generate a basic
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
        composer create-project ramsey/php-library-starter-kit YOUR-PROJECT-NAME
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

        The ramsey/php-library-starter-kit library is copyright © [Ben Ramsey](https://benramsey.com)
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

        descriptionInfo

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

        descriptionInfo



        usageInfo

        ## Contributing

        Contributions are welcome! Before contributing to this project, familiarize
        yourself with [CONTRIBUTING.md](CONTRIBUTING.md).



        copyrightInfo
        EOD;
    }
}
