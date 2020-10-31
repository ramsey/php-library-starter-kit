<?php

declare(strict_types=1);

namespace Ramsey\Test\Dev\LibraryStarterKit\Task\Builder;

use ArrayObject;
use Composer\IO\ConsoleIO;
use Mockery\MockInterface;
use Ramsey\Dev\LibraryStarterKit\Task\Answers;
use Ramsey\Dev\LibraryStarterKit\Task\Build;
use Ramsey\Dev\LibraryStarterKit\Task\Builder\UpdateSourceFileHeaders;
use Ramsey\Test\Dev\LibraryStarterKit\TestCase;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Twig\Environment as TwigEnvironment;

use const DIRECTORY_SEPARATOR;

class UpdateSourceFileHeadersTest extends TestCase
{
    public function testBuild(): void
    {
        $io = $this->mockery(ConsoleIO::class);
        $io->expects()->write('<info>Updating source file headers</info>');

        $answers = new Answers();

        $twig = $this->mockery(TwigEnvironment::class);
        $twig
            ->expects()
            ->render('source-file-header.twig', $answers->getArrayCopy())
            ->andReturn($this->getTwigGeneratedHeader());

        $file1 = $this->mockery(SplFileInfo::class, [
            'getRealPath' => '/path/to/app/src/SomeClass.php',
            'getContents' => $this->getFile1OriginalContents(),
        ]);

        $file2 = $this->mockery(SplFileInfo::class, [
            'getRealPath' => '/path/to/app/resources/console/AnotherClass.php',
            'getContents' => $this->getFile2OriginalContents(),
        ]);

        $finder = $this->mockery(Finder::class, [
            'getIterator' => new ArrayObject([$file1, $file2]),
        ]);
        $finder->expects()->exclude(['LibraryStarterKit'])->andReturnSelf();
        $finder->expects()->in(
            [
                '/path/to/app/src',
                '/path/to/app/resources' . DIRECTORY_SEPARATOR . 'console',
            ],
        )->andReturnSelf();
        $finder->expects()->files()->andReturnSelf();
        $finder->expects()->name('*.php')->andReturnSelf();

        $filesystem = $this->mockery(Filesystem::class);

        $filesystem->expects()->dumpFile(
            '/path/to/app/src/SomeClass.php',
            $this->getFile1ExpectedContents(),
        );

        $filesystem->expects()->dumpFile(
            '/path/to/app/resources/console/AnotherClass.php',
            $this->getFile2ExpectedContents(),
        );

        /** @var Build & MockInterface $task */
        $task = $this->mockery(Build::class, [
            'getAnswers' => $answers,
            'getFilesystem' => $filesystem,
            'getFinder' => $finder,
            'getIO' => $io,
            'getTwigEnvironment' => $twig,
        ]);

        $task
            ->shouldReceive('path')
            ->andReturnUsing(fn (string $path): string => '/path/to/app/' . $path);

        $builder = new UpdateSourceFileHeaders($task);

        $builder->build();
    }

    private function getTwigGeneratedHeader(): string
    {
        return <<<'EOD'


        /**
         * Lorem ipsum dolor sit amet, consectetur adipiscing elit
         *
         * Aliquam eu lectus et purus sagittis venenatis vel sit amet tortor.
         * Integer vel lectus nec ex ultrices finibus nec et arcu.
         *
         * @copyright Copyright (c) Agatha Porter
         * @license https://example.com/license Example License

         */
        EOD;
    }

    private function getFile1OriginalContents(): string
    {
        return <<<'EOD'
        <?php

        /**
         * This is a test
         *
         * Testing more testing more testing more
         */

        declare(strict_types=1);

        namespace Foo;

        /**
         * Class description
         */
        class Bar
        {
        }
        EOD;
    }

    private function getFile2OriginalContents(): string
    {
        return <<<'EOD'
        <?php

        /**
         * Summary
         *
         * This is a description
         *
         * @copyright Copyright (c) Jane Doe
         */

        declare(strict_types=1);

        namespace Foo;

        /**
         * Class description
         */
        class Baz
        {
            /**
             * Method description
             */
            public function __construct()
            {
            }
        }
        EOD;
    }

    private function getFile1ExpectedContents(): string
    {
        return <<<'EOD'
        <?php

        /**
         * Lorem ipsum dolor sit amet, consectetur adipiscing elit
         *
         * Aliquam eu lectus et purus sagittis venenatis vel sit amet tortor.
         * Integer vel lectus nec ex ultrices finibus nec et arcu.
         *
         * @copyright Copyright (c) Agatha Porter
         * @license https://example.com/license Example License
         */

        declare(strict_types=1);

        namespace Foo;

        /**
         * Class description
         */
        class Bar
        {
        }
        EOD;
    }

    private function getFile2ExpectedContents(): string
    {
        return <<<'EOD'
        <?php

        /**
         * Lorem ipsum dolor sit amet, consectetur adipiscing elit
         *
         * Aliquam eu lectus et purus sagittis venenatis vel sit amet tortor.
         * Integer vel lectus nec ex ultrices finibus nec et arcu.
         *
         * @copyright Copyright (c) Agatha Porter
         * @license https://example.com/license Example License
         */

        declare(strict_types=1);

        namespace Foo;

        /**
         * Class description
         */
        class Baz
        {
            /**
             * Method description
             */
            public function __construct()
            {
            }
        }
        EOD;
    }
}
