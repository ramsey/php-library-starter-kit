<?php

declare(strict_types=1);

namespace Ramsey\Test\Skeleton\Task\Builder;

use ArrayObject;
use Composer\IO\ConsoleIO;
use Mockery\MockInterface;
use Ramsey\Skeleton\Task\Answers;
use Ramsey\Skeleton\Task\Build;
use Ramsey\Skeleton\Task\Builder\RenameTestCase;
use Ramsey\Test\Skeleton\SkeletonTestCase;
use RuntimeException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

use const DIRECTORY_SEPARATOR;

class RenameTestCaseTest extends SkeletonTestCase
{
    public function testBuild(): void
    {
        $io = $this->mockery(ConsoleIO::class);
        $io->expects()->write('<info>Renaming VendorTestCase</info>');

        $answers = new Answers();
        $answers->packageNamespace = 'Acme\\Foo\\Bar';

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
        $finder2->expects()->exclude(['Skeleton'])->andReturnSelf();
        $finder2->expects()->in(['/path/to/app/tests'])->andReturnSelf();
        $finder2->expects()->files()->andReturnSelf();
        $finder2->expects()->name('*Test.php')->andReturnSelf();

        $filesystem = $this->mockery(Filesystem::class);
        $filesystem->expects()->remove('/path/to/app/tests/VendorTestCase.php');
        $filesystem->expects()->dumpFile(
            '/path/to/app/tests' . DIRECTORY_SEPARATOR . 'AcmeTestCase.php',
            $this->getVendorTestCaseFileContentsExpected('AcmeTestCase')
        );
        $filesystem->expects()->dumpFile(
            '/path/to/app/tests/FooTest.php',
            $this->getTestFileContentsExpected('FooTest', 'AcmeTestCase'),
        );
        $filesystem->expects()->dumpFile(
            '/path/to/app/tests/BarTest.php',
            $this->getTestFileContentsExpected('BarTest', 'AcmeTestCase'),
        );

        /** @var Build & MockInterface $task */
        $task = $this->mockery(Build::class, [
            'getAnswers' => $answers,
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

        $builder = new RenameTestCase($task);

        $builder->build();
    }

    public function testBuildThrowsExceptionWhenUnableToFindVendorTestCase(): void
    {
        $io = $this->mockery(ConsoleIO::class);
        $io->expects()->write('<info>Renaming VendorTestCase</info>');

        $answers = new Answers();
        $answers->packageNamespace = 'Acme\\Foo\\Bar';

        $finder = $this->mockery(Finder::class, [
            'getIterator' => new ArrayObject(),
        ]);
        $finder->expects()->in(['/path/to/app/tests'])->andReturnSelf();
        $finder->expects()->name('VendorTestCase.php')->andReturnSelf();

        /** @var Build & MockInterface $task */
        $task = $this->mockery(Build::class, [
            'getAnswers' => $answers,
            'getIO' => $io,
        ]);

        $task
            ->shouldReceive('getFinder')
            ->once()
            ->andReturn($finder);

        $task
            ->shouldReceive('path')
            ->andReturnUsing(fn (string $path): string => '/path/to/app/' . $path);

        $builder = new RenameTestCase($task);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Unable to get contents of tests/VendorTestCase.php');

        $builder->build();
    }

    private function getVendorTestCaseFileContents(): string
    {
        return <<<EOD
            <?php
            
            /**
             * File comment header
             */
            
            declare(strict_types=1);
            
            namespace Foo\\Test\\Bar;
            
            use PHPUnit\\Framework\\TestCase;
            
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
