<?php

declare(strict_types=1);

namespace Ramsey\Test\Dev\LibraryStarterKit\Task\Builder;

use ArrayObject;
use Mockery\MockInterface;
use Ramsey\Dev\LibraryStarterKit\Filesystem;
use Ramsey\Dev\LibraryStarterKit\Setup;
use Ramsey\Dev\LibraryStarterKit\Task\Build;
use Ramsey\Dev\LibraryStarterKit\Task\Builder\RenameTestCase;
use Ramsey\Test\Dev\LibraryStarterKit\TestCase;
use RuntimeException;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

use const DIRECTORY_SEPARATOR;

class RenameTestCaseTest extends TestCase
{
    public function testBuild(): void
    {
        $console = $this->mockery(SymfonyStyle::class);
        $console->expects()->note('Renaming VendorTestCase');

        $this->answers->packageNamespace = 'Acme\\Foo\\Bar';

        $testCaseFile = $this->mockery(SplFileInfo::class, [
            'getRealPath' => '/path/to/app/tests/VendorTestCase.php',
            'getContents' => $this->getVendorTestCaseFileContents(),
        ]);

        $testFile1 = $this->mockery(SplFileInfo::class, [
            'getRealPath' => '/path/to/app/tests/FooTest.php',
            'getContents' => $this->getTestFileContents('FooTest'),
        ]);

        $testFile2 = $this->mockery(SplFileInfo::class, [
            'getRealPath' => '/path/to/app/tests/BarTest.php',
            'getContents' => $this->getTestFileContents('BarTest'),
        ]);

        $finder1 = $this->mockery(Finder::class, [
            'getIterator' => new ArrayObject([$testCaseFile]),
        ]);
        $finder1->expects()->in(['/path/to/app/tests'])->andReturnSelf();
        $finder1->expects()->name('VendorTestCase.php')->andReturnSelf();

        $finder2 = $this->mockery(Finder::class, [
            'getIterator' => new ArrayObject([$testFile1, $testFile2]),
        ]);
        $finder2->expects()->exclude(['LibraryStarterKit'])->andReturnSelf();
        $finder2->expects()->in(['/path/to/app/tests'])->andReturnSelf();
        $finder2->expects()->files()->andReturnSelf();
        $finder2->expects()->name('*Test.php')->andReturnSelf();

        $filesystem = $this->mockery(Filesystem::class);
        $filesystem->expects()->remove('/path/to/app/tests/VendorTestCase.php');
        $filesystem->expects()->dumpFile(
            '/path/to/app/tests' . DIRECTORY_SEPARATOR . 'AcmeTestCase.php',
            $this->getVendorTestCaseFileContentsExpected('AcmeTestCase'),
        );
        $filesystem->expects()->dumpFile(
            '/path/to/app/tests/FooTest.php',
            $this->getTestFileContentsExpected('FooTest', 'AcmeTestCase'),
        );
        $filesystem->expects()->dumpFile(
            '/path/to/app/tests/BarTest.php',
            $this->getTestFileContentsExpected('BarTest', 'AcmeTestCase'),
        );

        $environment = $this->mockery(Setup::class, [
            'getFilesystem' => $filesystem,
        ]);

        $environment
            ->shouldReceive('getFinder')
            ->twice()
            ->andReturn($finder1, $finder2);

        $environment
            ->shouldReceive('path')
            ->andReturnUsing(fn (string $path): string => '/path/to/app/' . $path);

        /** @var Build & MockInterface $build */
        $build = $this->mockery(Build::class, [
            'getAnswers' => $this->answers,
            'getSetup' => $environment,
            'getConsole' => $console,
        ]);

        $builder = new RenameTestCase($build);

        $builder->build();
    }

    public function testBuildThrowsExceptionWhenUnableToFindVendorTestCase(): void
    {
        $console = $this->mockery(SymfonyStyle::class);
        $console->expects()->note('Renaming VendorTestCase');

        $this->answers->packageNamespace = 'Acme\\Foo\\Bar';

        $finder = $this->mockery(Finder::class, [
            'getIterator' => new ArrayObject(),
        ]);
        $finder->expects()->in(['/path/to/app/tests'])->andReturnSelf();
        $finder->expects()->name('VendorTestCase.php')->andReturnSelf();

        $environment = $this->mockery(Setup::class);

        $environment
            ->shouldReceive('getFinder')
            ->once()
            ->andReturn($finder);

        $environment
            ->shouldReceive('path')
            ->andReturnUsing(fn (string $path): string => '/path/to/app/' . $path);

        /** @var Build & MockInterface $build */
        $build = $this->mockery(Build::class, [
            'getAnswers' => $this->answers,
            'getSetup' => $environment,
            'getConsole' => $console,
        ]);

        $builder = new RenameTestCase($build);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Unable to get contents of tests/VendorTestCase.php');

        $builder->build();
    }

    private function getVendorTestCaseFileContents(): string
    {
        return <<<'EOD'
            <?php

            /**
             * File comment header
             */

            declare(strict_types=1);

            namespace Foo\Test\Bar;

            use PHPUnit\Framework\TestCase;

            class VendorTestCase extends TestCase
            {
            }

            EOD;
    }

    private function getVendorTestCaseFileContentsExpected(string $className): string
    {
        return <<<EOD
            <?php

            /**
             * File comment header
             */

            declare(strict_types=1);

            namespace Foo\\Test\\Bar;

            use PHPUnit\\Framework\\TestCase;

            class {$className} extends TestCase
            {
            }

            EOD;
    }

    private function getTestFileContents(string $className): string
    {
        return <<<EOD
            <?php

            /**
             * File comment header
             */

            declare(strict_types=1);

            namespace Foo\\Test\\Bar;

            use Foo\\Test\\Bar\\VendorTestCase;

            class {$className} extends VendorTestCase
            {
            }

            EOD;
    }

    private function getTestFileContentsExpected(string $className, string $testCaseName): string
    {
        return <<<EOD
            <?php

            /**
             * File comment header
             */

            declare(strict_types=1);

            namespace Foo\\Test\\Bar;

            use Foo\\Test\\Bar\\{$testCaseName};

            class {$className} extends {$testCaseName}
            {
            }

            EOD;
    }
}
