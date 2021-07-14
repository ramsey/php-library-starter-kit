<?php

declare(strict_types=1);

namespace Ramsey\Test\Dev\LibraryStarterKit\Task\Builder;

use ArrayObject;
use Composer\Script\Event;
use Mockery\MockInterface;
use Ramsey\Dev\LibraryStarterKit\Filesystem;
use Ramsey\Dev\LibraryStarterKit\Project;
use Ramsey\Dev\LibraryStarterKit\Setup;
use Ramsey\Dev\LibraryStarterKit\Task\Build;
use Ramsey\Dev\LibraryStarterKit\Task\Builder\UpdateSourceFileHeaders;
use Ramsey\Test\Dev\LibraryStarterKit\SnapshotsTool;
use Ramsey\Test\Dev\LibraryStarterKit\TestCase;
use Ramsey\Test\Dev\LibraryStarterKit\WindowsSafeTextDriver;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

use function file_get_contents;

class UpdateSourceFileHeadersTest extends TestCase
{
    use SnapshotsTool;

    /**
     * @dataProvider licenseProvider
     */
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
            'getIterator' => new ArrayObject([$file1, $file2]),
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

    /**
     * @return array<int, array{license: string}>
     */
    public function licenseProvider(): array
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
