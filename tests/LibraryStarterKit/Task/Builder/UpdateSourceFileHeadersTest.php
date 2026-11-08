<?php

declare(strict_types=1);

namespace Ramsey\Test\Dev\LibraryStarterKit\Task\Builder;

use ArrayObject;
use Closure;
use Composer\Script\Event;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use Ramsey\Dev\LibraryStarterKit\Filesystem;
use Ramsey\Dev\LibraryStarterKit\Project;
use Ramsey\Dev\LibraryStarterKit\Setup;
use Ramsey\Dev\LibraryStarterKit\Task\Build;
use Ramsey\Dev\LibraryStarterKit\Task\Builder\UpdateSourceFileHeaders;
use Ramsey\Test\Dev\LibraryStarterKit\SnapshotsTool;
use Ramsey\Test\Dev\LibraryStarterKit\TestCase;
use Ramsey\Test\Dev\LibraryStarterKit\WindowsSafeTextDriver;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

use function file_get_contents;
use function ini_get;
use function ini_set;

class UpdateSourceFileHeadersTest extends TestCase
{
    use SnapshotsTool;

    #[DataProvider('licenseProvider')]
    public function testBuild(string $license, ?string $copyrightEmail = null, ?string $copyrightUrl = null): void
    {
        $this->answers->packageName = 'fellowship/one-ring';
        $this->answers->copyrightHolder = 'Samwise Gamgee';
        $this->answers->license = $license;
        $this->answers->copyrightEmail = $copyrightEmail;
        $this->answers->copyrightUrl = $copyrightUrl;

        $console = $this->mockery(SymfonyStyle::class);
        $console->expects()->section('Updating source file headers');

        $realSetup = new Setup(
            $this->mockery(Project::class, [
                'getPath' => __DIR__ . '/../../../../.',
            ]),
            $this->mockery(Event::class),
            $this->mockery(Filesystem::class),
            $this->mockery(Finder::class),
            OutputInterface::VERBOSITY_NORMAL,
        );

        $file1 = $this->mockery(SplFileInfo::class, [
            'getRealPath' => '/path/to/app/src/SomeClass.php',
            'getContents' => $this->getFile1OriginalContents(),
        ]);

        $file2 = $this->mockery(SplFileInfo::class, [
            'getRealPath' => '/path/to/app/src/foo/AnotherClass.php',
            'getContents' => $this->getFile2OriginalContents(),
        ]);

        $finder = $this->mockery(Finder::class, [
            'getIterator' => (new ArrayObject([$file1, $file2]))->getIterator(),
        ]);
        $finder->expects()->exclude(['LibraryStarterKit'])->andReturnSelf();
        $finder->expects()->in(['/path/to/app/src'])->andReturnSelf();
        $finder->expects()->files()->andReturnSelf();
        $finder->expects()->name('*.php')->andReturnSelf();

        $filesystem = $this->mockery(Filesystem::class);

        $filesystem
            ->shouldReceive('dumpFile')
            ->times(2)
            ->withArgs(function (string $path, string $contents) {
                $this->assertMatchesSnapshot($contents, new WindowsSafeTextDriver());

                return true;
            });

        $environment = $this->mockery(Setup::class, [
            'getAppPath' => '/path/to/app',
            'getFilesystem' => $filesystem,
            'getFinder' => $finder,
            'getTwigEnvironment' => $realSetup->getTwigEnvironment(),
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

        $builder = new UpdateSourceFileHeaders($task);

        $builder->build();
    }

    public function testBuildUsesEmptyPathWhenRealPathIsUnavailable(): void
    {
        $this->answers->packageName = 'fellowship/one-ring';
        $this->answers->copyrightHolder = 'Samwise Gamgee';
        $this->answers->license = 'MIT';

        $this->runBuildWithOneFile(
            $this->mockery(SplFileInfo::class, [
                'getRealPath' => false,
                'getContents' => $this->getFile1OriginalContents(),
            ]),
            function (string $path, string $contents): bool {
                $this->assertSame('', $path);
                $this->assertNotSame('', $contents);

                return true;
            },
        );
    }

    public function testBuildWritesEmptyContentsWhenHeaderReplacementFails(): void
    {
        $this->answers->packageName = 'fellowship/one-ring';
        $this->answers->copyrightHolder = 'Samwise Gamgee';
        $this->answers->license = 'MIT';

        $file = $this->mockery(SplFileInfo::class, [
            'getRealPath' => '/path/to/app/src/SomeClass.php',
            'getContents' => $this->getFile1OriginalContents(),
        ]);

        $this->runBuildWithOneFile(
            $file,
            function (string $path, string $contents): bool {
                $this->assertSame('/path/to/app/src/SomeClass.php', $path);
                $this->assertSame('', $contents);

                return true;
            },
            true,
        );
    }

    /**
     * @param Closure(string, string): bool $dumpFileArgs
     */
    private function runBuildWithOneFile(
        SplFileInfo & MockInterface $file,
        Closure $dumpFileArgs,
        bool $failEveryPattern = false,
    ): void {
        $console = $this->mockery(SymfonyStyle::class);
        $console->expects()->section('Updating source file headers');

        $realSetup = new Setup(
            $this->mockery(Project::class, [
                'getPath' => __DIR__ . '/../../../../.',
            ]),
            $this->mockery(Event::class),
            $this->mockery(Filesystem::class),
            $this->mockery(Finder::class),
            OutputInterface::VERBOSITY_NORMAL,
        );

        $twig = $realSetup->getTwigEnvironment();

        $finder = $this->mockery(Finder::class, [
            'getIterator' => (new ArrayObject([$file]))->getIterator(),
        ]);
        $finder->expects()->exclude(['LibraryStarterKit'])->andReturnSelf();
        $finder->expects()->in(['/path/to/app/src'])->andReturnSelf();
        $finder->expects()->files()->andReturnSelf();
        $finder->expects()->name('*.php')->andReturnSelf();

        $filesystem = $this->mockery(Filesystem::class);
        $filesystem->shouldReceive('dumpFile')->once()->withArgs($dumpFileArgs);

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

        $builder = new UpdateSourceFileHeaders($task);

        if (!$failEveryPattern) {
            $builder->build();

            return;
        }

        // Twig keeps each compiled template for the rest of the process, so
        // compile it now, while regular expressions still work.
        $twig->render('header/source-file-header.twig', $this->answers->getArrayCopy());

        // A match limit of one makes every regular expression fail with an error.
        $originalLimit = (string) ini_get('pcre.backtrack_limit');
        ini_set('pcre.backtrack_limit', '1');

        try {
            $builder->build();
        } finally {
            ini_set('pcre.backtrack_limit', $originalLimit);
        }
    }

    /**
     * @return list<array{
     *     license: string,
     *     copyrightEmail?: string | null,
     *     copyrightUrl?: string | null,
     * }>
     */
    public static function licenseProvider(): array
    {
        return [
            ['license' => 'AGPL-3.0-or-later'],
            ['license' => 'Apache-2.0'],
            [
                'license' => 'BSD-2-Clause',
                'copyrightEmail' => 'samwise@example.com',
            ],
            ['license' => 'BSD-3-Clause'],
            ['license' => 'GPL-3.0-or-later'],
            ['license' => 'Hippocratic-2.1'],
            ['license' => 'LGPL-3.0-or-later'],
            [
                'license' => 'MIT',
                'copyrightEmail' => null,
                'copyrightUrl' => 'https://example.com/fellowship',
            ],
            ['license' => 'MIT-0'],
            ['license' => 'MPL-2.0'],
            [
                'license' => 'Proprietary',
                'copyrightEmail' => 'fellowship@example.com',
                'copyrightUrl' => 'https://example.com/fellowship',
            ],
            ['license' => 'Unlicense'],
        ];
    }

    private function getFile1OriginalContents(): string
    {
        return (string) file_get_contents(__DIR__ . '/fixtures/update-source-file-headers-test-1.php');
    }

    private function getFile2OriginalContents(): string
    {
        return (string) file_get_contents(__DIR__ . '/fixtures/update-source-file-headers-test-2.php');
    }
}
