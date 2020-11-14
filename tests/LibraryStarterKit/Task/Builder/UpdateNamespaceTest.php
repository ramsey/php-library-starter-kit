<?php

declare(strict_types=1);

namespace Ramsey\Test\Dev\LibraryStarterKit\Task\Builder;

use ArrayObject;
use Mockery\MockInterface;
use Ramsey\Dev\LibraryStarterKit\Answers;
use Ramsey\Dev\LibraryStarterKit\Setup;
use Ramsey\Dev\LibraryStarterKit\Task\Build;
use Ramsey\Dev\LibraryStarterKit\Task\Builder\UpdateNamespace;
use Ramsey\Test\Dev\LibraryStarterKit\TestCase;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

use function str_replace;

class UpdateNamespaceTest extends TestCase
{
    /**
     * @dataProvider provideNamespaceTestValues
     */
    public function testBuild(
        string $packageName,
        string $namespace,
        string $testNamespace
    ): void {
        $console = $this->mockery(SymfonyStyle::class);
        $console->expects()->note('Updating namespace');

        $answers = new Answers();
        $answers->packageName = $packageName;
        $answers->packageNamespace = $namespace;

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
            'getIterator' => new ArrayObject([$file1, $file2]),
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
            'getIterator' => new ArrayObject([$file3]),
        ]);
        $finder2->expects()->in(['/path/to/app'])->andReturnSelf();
        $finder2->expects()->files()->andReturnSelf();
        $finder2->expects()->depth('== 0')->andReturnSelf();
        $finder2->expects()->name('composer.json')->andReturnSelf();

        $filesystem = $this->mockery(Filesystem::class);

        $filesystem
            ->expects()
            ->dumpFile(
                '/path/to/app/src/Foo.php',
                $this->getFileContentsExpected($packageName, $namespace, $testNamespace),
            );

        $filesystem
            ->expects()
            ->dumpFile(
                '/path/to/app/src/Bar.php',
                $this->getFileContentsExpected($packageName, $namespace, $testNamespace),
            );

        $filesystem
            ->expects()
            ->dumpFile(
                '/path/to/app/composer.json',
                $this->getFileContentsExpected($packageName, $namespace, $testNamespace),
            );

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
            'getAnswers' => $answers,
            'getConsole' => $console,
            'getSetup' => $environment,
        ]);

        $builder = new UpdateNamespace($task);

        $builder->build();
    }

    /**
     * @return array<array<array-key, string>>
     */
    public function provideNamespaceTestValues(): array
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
        return <<<'EOD'
            <?php

            /**
             * File header comment for ramsey/php-library-starter-kit
             */

            declare(strict_types=1);

            namespace Vendor\SubNamespace;

            use Vendor\Test\SubNamespace\Bar;

            class Foo
            {
                public const CLASS_NAMES = [
                    'Vendor\\SubNamespace',
                    'Vendor\\Test\\SubNamespace',
                ];
            }
            EOD;
    }

    private function getFileContentsExpected(
        string $packageName,
        string $namespace,
        string $testNamespace
    ): string {
        $namespaceEscaped = str_replace('\\', '\\\\', $namespace);
        $testNamespaceEscaped = str_replace('\\', '\\\\', $testNamespace);

        return <<<EOD
            <?php

            /**
             * File header comment for {$packageName}
             */

            declare(strict_types=1);

            namespace {$namespace};

            use {$testNamespace}\\Bar;

            class Foo
            {
                public const CLASS_NAMES = [
                    '{$namespaceEscaped}',
                    '{$testNamespaceEscaped}',
                ];
            }
            EOD;
    }
}
