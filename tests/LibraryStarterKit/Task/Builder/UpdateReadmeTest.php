<?php

declare(strict_types=1);

namespace Ramsey\Test\Dev\LibraryStarterKit\Task\Builder;

use ArrayObject;
use Mockery\MockInterface;
use Ramsey\Dev\LibraryStarterKit\Filesystem;
use Ramsey\Dev\LibraryStarterKit\Setup;
use Ramsey\Dev\LibraryStarterKit\Task\Build;
use Ramsey\Dev\LibraryStarterKit\Task\Builder\UpdateReadme;
use Ramsey\Test\Dev\LibraryStarterKit\SnapshotsTool;
use Ramsey\Test\Dev\LibraryStarterKit\TestCase;
use Ramsey\Test\Dev\LibraryStarterKit\WindowsSafeTextDriver;
use RuntimeException;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Twig\Environment as TwigEnvironment;

use function file_get_contents;

class UpdateReadmeTest extends TestCase
{
    use SnapshotsTool;

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
                $this->assertMatchesSnapshot($contents, new WindowsSafeTextDriver());

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
            ->andReturn('badges info');

        $twig
            ->expects()
            ->render('readme/description.md.twig', $this->answers->getArrayCopy())
            ->andReturn('description info');

        $twig
            ->expects()
            ->render('readme/code-of-conduct.md.twig', $this->answers->getArrayCopy())
            ->andReturn('code of conduct info');

        $twig
            ->expects()
            ->render('readme/usage.md.twig', $this->answers->getArrayCopy())
            ->andReturn('usage info');

        $twig
            ->expects()
            ->render('readme/copyright.md.twig', $this->answers->getArrayCopy())
            ->andReturn('copyright info');

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
                $this->assertMatchesSnapshot($contents, new WindowsSafeTextDriver());

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
            ->andReturn('badges info');

        $twig
            ->expects()
            ->render('readme/description.md.twig', $this->answers->getArrayCopy())
            ->andReturn('description info');

        $twig
            ->expects()
            ->render('readme/code-of-conduct.md.twig', $this->answers->getArrayCopy())
            ->never();

        $twig
            ->expects()
            ->render('readme/usage.md.twig', $this->answers->getArrayCopy())
            ->andReturn('usage info');

        $twig
            ->expects()
            ->render('readme/copyright.md.twig', $this->answers->getArrayCopy())
            ->andReturn('copyright info');

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
        return (string) file_get_contents(__DIR__ . '/fixtures/readme-full.md');
    }
}
