<?php

declare(strict_types=1);

namespace Ramsey\Test\Dev\LibraryStarterKit\Task\Builder;

use ArrayObject;
use Generator;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use Ramsey\Dev\LibraryStarterKit\Filesystem;
use Ramsey\Dev\LibraryStarterKit\Setup;
use Ramsey\Dev\LibraryStarterKit\Task\Build;
use Ramsey\Dev\LibraryStarterKit\Task\Builder\UpdateNamespace;
use Ramsey\Test\Dev\LibraryStarterKit\SnapshotsTool;
use Ramsey\Test\Dev\LibraryStarterKit\TestCase;
use Ramsey\Test\Dev\LibraryStarterKit\WindowsSafeTextDriver;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

use function file_get_contents;

class UpdateNamespaceTest extends TestCase
{
    use SnapshotsTool;

    #[DataProvider('provideNamespaceTestValues')]
    public function testBuild(
        string $packageName,
        string $namespace,
        string $testNamespace,
    ): void {
        $console = $this->mockery(SymfonyStyle::class);
        $console->expects()->section('Updating namespace');

        $this->answers->packageName = $packageName;
        $this->answers->packageNamespace = $namespace;

        $file1 = $this->mockery(SplFileInfo::class, [
            'getRealPath' => '/path/to/app/src/Foo.php',
            'getContents' => $this->getFileContents(),
        ]);

        $file2 = $this->mockery(SplFileInfo::class, [
            'getRealPath' => '/path/to/app/src/Bar.php',
            'getContents' => $this->getFileContents(),
        ]);

        $file3 = $this->mockery(SplFileInfo::class, [
            'getRealPath' => '/path/to/app/composer.json',
            'getContents' => $this->getFileContents(),
        ]);

        $finder1 = $this->mockery(Finder::class, [
            'getIterator' => (new ArrayObject([$file1, $file2]))->getIterator(),
        ]);
        $finder1->expects()->exclude(['LibraryStarterKit'])->andReturnSelf();
        $finder1->expects()->in(
            [
                '/path/to/app/bin',
                '/path/to/app/src',
                '/path/to/app/tests',
            ],
        )->andReturnSelf();
        $finder1->expects()->files()->andReturnSelf();
        $finder1->expects()->name('*.php')->andReturnSelf();

        $finder2 = $this->mockery(Finder::class, [
            'getIterator' => (new ArrayObject([$file3]))->getIterator(),
        ]);
        $finder2->expects()->in(['/path/to/app'])->andReturnSelf();
        $finder2->expects()->files()->andReturnSelf();
        $finder2->expects()->depth('== 0')->andReturnSelf();
        $finder2->expects()->name('composer.json')->andReturnSelf();

        $filesystem = $this->mockery(Filesystem::class);

        $filesystem
            ->shouldReceive('dumpFile')
            ->times(3)
            ->withArgs(function (string $path, string $contents) {
                $this->assertMatchesSnapshot($path, new WindowsSafeTextDriver());
                $this->assertMatchesSnapshot($contents, new WindowsSafeTextDriver());

                return true;
            });

        $environment = $this->mockery(Setup::class, [
            'getAppPath' => '/path/to/app',
            'getFilesystem' => $filesystem,
        ]);

        $environment
            ->shouldReceive('path')
            ->andReturnUsing(fn (string $path): string => '/path/to/app/' . $path);

        $environment
            ->shouldReceive('getFinder')
            ->twice()
            ->andReturn($finder1, $finder2);

        /** @var Build & MockInterface $task */
        $task = $this->mockery(Build::class, [
            'getAnswers' => $this->answers,
            'getConsole' => $console,
            'getSetup' => $environment,
        ]);

        $builder = new UpdateNamespace($task);

        $builder->build();
    }

    public function testBuildUpdatesEveryFileEvenWhenTheFinderRepeatsKeysAndOnlyFirstComposerJson(): void
    {
        $console = $this->mockery(SymfonyStyle::class);
        $console->expects()->section('Updating namespace');

        $this->answers->packageName = 'acme/foo-bar';
        $this->answers->packageNamespace = 'Acme\\Foo\\Bar';

        $contents = 'Vendor\\SubNamespace ramsey/php-library-starter-kit';
        $expected = 'Acme\\Foo\\Bar acme/foo-bar';

        $sourceFiles = (function () use ($contents): Generator {
            yield 'same-key' => $this->mockery(SplFileInfo::class, [
                'getRealPath' => '/path/to/app/src/Foo.php',
                'getContents' => $contents,
            ]);
            yield 'same-key' => $this->mockery(SplFileInfo::class, [
                'getRealPath' => '/path/to/app/src/Bar.php',
                'getContents' => $contents,
            ]);
        })();

        $composerFiles = (function () use ($contents): Generator {
            yield $this->mockery(SplFileInfo::class, [
                'getRealPath' => '/path/to/app/composer.json',
                'getContents' => $contents,
            ]);
            yield $this->mockery(SplFileInfo::class, [
                'getRealPath' => '/path/to/app/other/composer.json',
                'getContents' => $contents,
            ]);
        })();

        $finder1 = $this->mockery(Finder::class, ['getIterator' => $sourceFiles]);
        $finder1->expects()->exclude(['LibraryStarterKit'])->andReturnSelf();
        $finder1->expects()->in(['/path/to/app/bin', '/path/to/app/src', '/path/to/app/tests'])->andReturnSelf();
        $finder1->expects()->files()->andReturnSelf();
        $finder1->expects()->name('*.php')->andReturnSelf();

        $finder2 = $this->mockery(Finder::class, ['getIterator' => $composerFiles]);
        $finder2->expects()->in(['/path/to/app'])->andReturnSelf();
        $finder2->expects()->files()->andReturnSelf();
        $finder2->expects()->depth('== 0')->andReturnSelf();
        $finder2->expects()->name('composer.json')->andReturnSelf();

        $filesystem = $this->mockery(Filesystem::class);
        $filesystem->expects()->dumpFile('/path/to/app/src/Foo.php', $expected);
        $filesystem->expects()->dumpFile('/path/to/app/src/Bar.php', $expected);
        $filesystem->expects()->dumpFile('/path/to/app/composer.json', $expected);
        $filesystem->shouldNotReceive('dumpFile')->with('/path/to/app/other/composer.json', Mockery::any());

        $environment = $this->mockery(Setup::class, [
            'getAppPath' => '/path/to/app',
            'getFilesystem' => $filesystem,
        ]);

        $environment
            ->shouldReceive('path')
            ->andReturnUsing(fn (string $path): string => '/path/to/app/' . $path);

        $environment
            ->shouldReceive('getFinder')
            ->twice()
            ->andReturn($finder1, $finder2);

        /** @var Build & MockInterface $task */
        $task = $this->mockery(Build::class, [
            'getAnswers' => $this->answers,
            'getConsole' => $console,
            'getSetup' => $environment,
        ]);

        $builder = new UpdateNamespace($task);

        $builder->build();
    }

    public function testBuildUsesEmptyPathWhenRealPathIsUnavailable(): void
    {
        $console = $this->mockery(SymfonyStyle::class);
        $console->expects()->section('Updating namespace');

        $this->answers->packageName = 'acme/foo-bar';
        $this->answers->packageNamespace = 'Acme\\Foo\\Bar';

        $file = $this->mockery(SplFileInfo::class, [
            'getRealPath' => false,
            'getContents' => 'ramsey/php-library-starter-kit',
        ]);

        $finder1 = $this->mockery(Finder::class, ['getIterator' => (new ArrayObject([$file]))->getIterator()]);
        $finder1->expects()->exclude(['LibraryStarterKit'])->andReturnSelf();
        $finder1->expects()->in(['/path/to/app/bin', '/path/to/app/src', '/path/to/app/tests'])->andReturnSelf();
        $finder1->expects()->files()->andReturnSelf();
        $finder1->expects()->name('*.php')->andReturnSelf();

        $finder2 = $this->mockery(Finder::class, ['getIterator' => (new ArrayObject([]))->getIterator()]);
        $finder2->expects()->in(['/path/to/app'])->andReturnSelf();
        $finder2->expects()->files()->andReturnSelf();
        $finder2->expects()->depth('== 0')->andReturnSelf();
        $finder2->expects()->name('composer.json')->andReturnSelf();

        $filesystem = $this->mockery(Filesystem::class);
        $filesystem->expects()->dumpFile('', 'acme/foo-bar');

        $environment = $this->mockery(Setup::class, [
            'getAppPath' => '/path/to/app',
            'getFilesystem' => $filesystem,
        ]);

        $environment
            ->shouldReceive('path')
            ->andReturnUsing(fn (string $path): string => '/path/to/app/' . $path);

        $environment
            ->shouldReceive('getFinder')
            ->twice()
            ->andReturn($finder1, $finder2);

        /** @var Build & MockInterface $task */
        $task = $this->mockery(Build::class, [
            'getAnswers' => $this->answers,
            'getConsole' => $console,
            'getSetup' => $environment,
        ]);

        $builder = new UpdateNamespace($task);

        $builder->build();
    }

    /**
     * @return list<array{packageName: string, namespace: string, testNamespace: string}>
     */
    public static function provideNamespaceTestValues(): array
    {
        return [
            [
                'packageName' => 'acme/foo-bar',
                'namespace' => 'Acme\\Foo\\Bar',
                'testNamespace' => 'Acme\\Test\\Foo\\Bar',
            ],
            [
                'packageName' => 'acme/foo',
                'namespace' => 'Acme',
                'testNamespace' => 'Acme\\Test',
            ],
            [
                'packageName' => 'another/package',
                'namespace' => 'Another\\Package\\With\\Long\\Namespace',
                'testNamespace' => 'Another\\Test\\Package\\With\\Long\\Namespace',
            ],
        ];
    }

    private function getFileContents(): string
    {
        return (string) file_get_contents(__DIR__ . '/fixtures/update-namespace-test.php');
    }
}
