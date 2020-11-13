<?php

declare(strict_types=1);

namespace Ramsey\Test\Dev\LibraryStarterKit\Task\Builder;

use ArrayObject;
use Composer\IO\ConsoleIO;
use Mockery\MockInterface;
use Ramsey\Dev\LibraryStarterKit\Answers;
use Ramsey\Dev\LibraryStarterKit\Task\Build;
use Ramsey\Dev\LibraryStarterKit\Task\Builder\UpdateNamespace;
use Ramsey\Test\Dev\LibraryStarterKit\TestCase;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

use function str_replace;

use const DIRECTORY_SEPARATOR;

class UpdateNamespaceTest extends TestCase
{
    /**
     * @dataProvider provideNamespaceTestValues
     */
    public function testBuild(
        string $packageName,
        string $namespace,
        string $testNamespace,
        string $consoleNamespace
    ): void {
        $io = $this->mockery(ConsoleIO::class);
        $io->expects()->write('<info>Updating namespace</info>');

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
                '/path/to/app/resources' . DIRECTORY_SEPARATOR . 'console',
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
                $this->getFileContentsExpected($packageName, $namespace, $testNamespace, $consoleNamespace),
            );

        $filesystem
            ->expects()
            ->dumpFile(
                '/path/to/app/src/Bar.php',
                $this->getFileContentsExpected($packageName, $namespace, $testNamespace, $consoleNamespace),
            );

        $filesystem
            ->expects()
            ->dumpFile(
                '/path/to/app/composer.json',
                $this->getFileContentsExpected($packageName, $namespace, $testNamespace, $consoleNamespace),
            );

        /** @var Build & MockInterface $task */
        $task = $this->mockery(Build::class, [
            'getAnswers' => $answers,
            'getAppPath' => '/path/to/app',
            'getFilesystem' => $filesystem,
            'getIO' => $io,
        ]);

        $task
            ->shouldReceive('getFinder')
            ->twice()
            ->andReturn($finder1, $finder2);

        $task
            ->shouldReceive('path')
            ->andReturnUsing(fn (string $path): string => '/path/to/app/' . $path);

        $builder = new UpdateNamespace($task);

        $builder->build();
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function provideNamespaceTestValues(): array
    {
        return [
            [
                'packageName' => 'acme/foo-bar',
                'namespace' => 'Acme\\Foo\\Bar',
                'testNamespace' => 'Acme\\Test\\Foo\\Bar',
                'consoleNamespace' => 'Acme\\Console',
            ],
            [
                'packageName' => 'acme/foo',
                'namespace' => 'Acme',
                'testNamespace' => 'Acme\\Test',
                'consoleNamespace' => 'Acme\\Console',
            ],
            [
                'packageName' => 'another/package',
                'namespace' => 'Another\\Package\\With\\Long\\Namespace',
                'testNamespace' => 'Another\\Test\\Package\\With\\Long\\Namespace',
                'consoleNamespace' => 'Another\\Console',
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
            use Vendor\Console\Baz;

            class Foo
            {
                public const CLASS_NAMES = [
                    'Vendor\\SubNamespace',
                    'Vendor\\Test\\SubNamespace',
                    'Vendor\\Console',
                ];
            }
            EOD;
    }

    private function getFileContentsExpected(
        string $packageName,
        string $namespace,
        string $testNamespace,
        string $consoleNamespace
    ): string {
        $namespaceEscaped = str_replace('\\', '\\\\', $namespace);
        $testNamespaceEscaped = str_replace('\\', '\\\\', $testNamespace);
        $consoleNamespaceEscaped = str_replace('\\', '\\\\', $consoleNamespace);

        return <<<EOD
            <?php

            /**
             * File header comment for {$packageName}
             */

            declare(strict_types=1);

            namespace {$namespace};

            use {$testNamespace}\\Bar;
            use {$consoleNamespace}\\Baz;

            class Foo
            {
                public const CLASS_NAMES = [
                    '{$namespaceEscaped}',
                    '{$testNamespaceEscaped}',
                    '{$consoleNamespaceEscaped}',
                ];
            }
            EOD;
    }
}
