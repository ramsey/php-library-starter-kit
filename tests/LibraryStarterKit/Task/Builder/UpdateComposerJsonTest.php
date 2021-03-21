<?php

declare(strict_types=1);

namespace Ramsey\Test\Dev\LibraryStarterKit\Task\Builder;

use ArrayObject;
use Mockery\MockInterface;
use Ramsey\Dev\LibraryStarterKit\Filesystem;
use Ramsey\Dev\LibraryStarterKit\Setup;
use Ramsey\Dev\LibraryStarterKit\Task\Build;
use Ramsey\Dev\LibraryStarterKit\Task\Builder\UpdateComposerJson;
use Ramsey\Test\Dev\LibraryStarterKit\SnapshotsTool;
use Ramsey\Test\Dev\LibraryStarterKit\TestCase;
use Ramsey\Test\Dev\LibraryStarterKit\WindowsSafeTextDriver;
use RuntimeException;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

use function file_get_contents;

class UpdateComposerJsonTest extends TestCase
{
    use SnapshotsTool;

    public function testBuild(): void
    {
        $console = $this->mockery(SymfonyStyle::class);
        $console->expects()->note('Updating composer.json');

        $filesystem = $this->mockery(Filesystem::class);
        $filesystem
            ->shouldReceive('dumpFile')
            ->once()
            ->withArgs(function (string $path, string $contents) {
                $this->assertSame('/path/to/app/composer.json', $path);
                $this->assertMatchesSnapshot($contents, new WindowsSafeTextDriver());

                return true;
            });

        $finder = $this->mockery(Finder::class, [
            'getIterator' => new ArrayObject(
                [
                    $this->mockery(SplFileInfo::class, ['getContents' => $this->composerContentsOriginal()]),
                    $this->mockery(SplFileInfo::class, ['getContents' => '']),
                ],
            ),
        ]);
        $finder->expects()->in('/path/to/app')->andReturnSelf();
        $finder->expects()->files()->andReturnSelf();
        $finder->expects()->depth('== 0')->andReturnSelf();
        $finder->expects()->name('composer.json');

        $this->answers->packageName = 'a-vendor/package-name';
        $this->answers->packageDescription = 'This is a test package.';
        $this->answers->packageKeywords = ['test', 'package'];
        $this->answers->authorName = 'Jane Doe';
        $this->answers->authorEmail = 'jdoe@example.com';
        $this->answers->authorUrl = 'https://example.com/jane';
        $this->answers->license = 'Apache-2.0';

        $environment = $this->mockery(Setup::class, [
            'getAppPath' => '/path/to/app',
            'getFilesystem' => $filesystem,
            'getFinder' => $finder,
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

        $builder = new UpdateComposerJson($build);

        $builder->build();
    }

    public function testBuildThrowsExceptionWhenComposerContentsContainInvalidJson(): void
    {
        $console = $this->mockery(SymfonyStyle::class);
        $console->expects()->note('Updating composer.json');

        $finder = $this->mockery(Finder::class, [
            'getIterator' => new ArrayObject(
                [
                    $this->mockery(SplFileInfo::class, ['getContents' => 'null']),
                ],
            ),
        ]);
        $finder->expects()->in('/path/to/app')->andReturnSelf();
        $finder->expects()->files()->andReturnSelf();
        $finder->expects()->depth('== 0')->andReturnSelf();
        $finder->expects()->name('composer.json');

        $environment = $this->mockery(Setup::class, [
            'getAppPath' => '/path/to/app',
            'getFinder' => $finder,
        ]);

        /** @var Build & MockInterface $build */
        $build = $this->mockery(Build::class, [
            'getSetup' => $environment,
            'getConsole' => $console,
        ]);

        $builder = new UpdateComposerJson($build);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Unable to decode contents of composer.json');

        $builder->build();
    }

    public function testBuildThrowsExceptionWhenComposerJsonCannotBeFound(): void
    {
        $console = $this->mockery(SymfonyStyle::class);
        $console->expects()->note('Updating composer.json');

        $finder = $this->mockery(Finder::class, [
            'getIterator' => new ArrayObject(),
        ]);
        $finder->expects()->in('/path/to/app')->andReturnSelf();
        $finder->expects()->files()->andReturnSelf();
        $finder->expects()->depth('== 0')->andReturnSelf();
        $finder->expects()->name('composer.json');

        $environment = $this->mockery(Setup::class, [
            'getAppPath' => '/path/to/app',
            'getFinder' => $finder,
        ]);

        /** @var Build & MockInterface $build */
        $build = $this->mockery(Build::class, [
            'getConsole' => $console,
            'getSetup' => $environment,
        ]);

        $builder = new UpdateComposerJson($build);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Unable to get contents of composer.json');

        $builder->build();
    }

    public function testBuildWithMinimalComposerJson(): void
    {
        $console = $this->mockery(SymfonyStyle::class);
        $console->expects()->note('Updating composer.json');

        $filesystem = $this->mockery(Filesystem::class);
        $filesystem
            ->shouldReceive('dumpFile')
            ->once()
            ->withArgs(function (string $path, string $contents) {
                $this->assertSame('/path/to/app/composer.json', $path);
                $this->assertMatchesSnapshot($contents, new WindowsSafeTextDriver());

                return true;
            });

        $finder = $this->mockery(Finder::class, [
            'getIterator' => new ArrayObject([
                $this->mockery(SplFileInfo::class, [
                    'getContents' => $this->composerContentsOriginalMinimal(),
                ]),
            ]),
        ]);
        $finder->expects()->in('/path/to/app')->andReturnSelf();
        $finder->expects()->files()->andReturnSelf();
        $finder->expects()->depth('== 0')->andReturnSelf();
        $finder->expects()->name('composer.json');

        $this->answers->packageName = 'a-vendor/package-name';
        $this->answers->packageDescription = 'This is a test package.';
        $this->answers->authorName = 'Jane Doe';
        $this->answers->license = 'MPL-2.0';

        $environment = $this->mockery(Setup::class, [
            'getAppPath' => '/path/to/app',
            'getFilesystem' => $filesystem,
            'getFinder' => $finder,
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

        $builder = new UpdateComposerJson($build);

        $builder->build();
    }

    private function composerContentsOriginal(): string
    {
        return (string) file_get_contents(__DIR__ . '/fixtures/composer-full.json');
    }

    private function composerContentsOriginalMinimal(): string
    {
        return (string) file_get_contents(__DIR__ . '/fixtures/composer-minimal.json');
    }
}
