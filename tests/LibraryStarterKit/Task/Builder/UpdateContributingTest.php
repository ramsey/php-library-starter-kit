<?php

declare(strict_types=1);

namespace Ramsey\Test\Dev\LibraryStarterKit\Task\Builder;

use ArrayObject;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use Ramsey\Dev\LibraryStarterKit\Filesystem;
use Ramsey\Dev\LibraryStarterKit\Setup;
use Ramsey\Dev\LibraryStarterKit\Task\Build;
use Ramsey\Dev\LibraryStarterKit\Task\Builder\UpdateContributing;
use Ramsey\Test\Dev\LibraryStarterKit\TestCase;
use RuntimeException;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Twig\Environment as TwigEnvironment;

use function file_get_contents;

class UpdateContributingTest extends TestCase
{
    public function testBuild(): void
    {
        $console = $this->mockery(SymfonyStyle::class);
        $console->expects()->section('Updating CONTRIBUTING.md');

        $filesystem = $this->mockery(Filesystem::class);
        $filesystem
            ->shouldReceive('dumpFile')
            ->once()
            ->withArgs(function (string $path, string $contents) {
                $this->assertSame('/path/to/app/CONTRIBUTING.md', $path);
                $this->assertSame('contributingContents', $contents);

                return true;
            });

        $twig = $this->mockery(TwigEnvironment::class);

        $twig
            ->expects()
            ->render('CONTRIBUTING.md.twig', $this->answers->getArrayCopy() + ['phpVersion' => '8.5'])
            ->andReturn('contributingContents');

        $composerContents = (string) file_get_contents(__DIR__ . '/fixtures/composer-full.json');

        $environment = $this->mockery(Setup::class, [
            'getAppPath' => '/path/to/app',
            'getFilesystem' => $filesystem,
            'getFinder' => $this->composerJsonFinder($composerContents),
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

        $builder = new UpdateContributing($task);

        $builder->build();
    }

    #[DataProvider('unusableComposerJson')]
    public function testBuildThrowsWhenTheMinimumPhpVersionIsUnknown(?string $composerContents): void
    {
        $console = $this->mockery(SymfonyStyle::class);
        $console->expects()->section('Updating CONTRIBUTING.md');

        $filesystem = $this->mockery(Filesystem::class);
        $filesystem->shouldNotReceive('dumpFile');

        $twig = $this->mockery(TwigEnvironment::class);
        $twig->shouldNotReceive('render');

        $environment = $this->mockery(Setup::class, [
            'getAppPath' => '/path/to/app',
            'getFilesystem' => $filesystem,
            'getFinder' => $this->composerJsonFinder($composerContents),
            'getTwigEnvironment' => $twig,
        ]);

        /** @var Build & MockInterface $task */
        $task = $this->mockery(Build::class, [
            'getAnswers' => $this->answers,
            'getConsole' => $console,
            'getSetup' => $environment,
        ]);

        $builder = new UpdateContributing($task);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessageIsOrContains('Unable to determine the minimum PHP version from composer.json');

        $builder->build();
    }

    /**
     * @return iterable<string, array{composerContents: string | null}>
     */
    public static function unusableComposerJson(): iterable
    {
        yield 'no composer.json file' => ['composerContents' => null];
        yield 'invalid JSON' => ['composerContents' => '{'];
        yield 'not an object' => ['composerContents' => '42'];
        yield 'no require' => ['composerContents' => '{"name": "a/b"}'];
        yield 'require is not an object' => ['composerContents' => '{"require": "php"}'];
        yield 'empty require' => ['composerContents' => '{"require": {}}'];
        yield 'php is not a string' => ['composerContents' => '{"require": {"php": 8}}'];
        yield 'php has no major.minor' => ['composerContents' => '{"require": {"php": ">=8"}}'];
    }

    /**
     * @param string | null $composerContents The contents of composer.json, or null for no composer.json file
     *
     * @return Finder & MockInterface
     */
    private function composerJsonFinder(?string $composerContents): Finder
    {
        $files = $composerContents === null
            ? []
            : [$this->mockery(SplFileInfo::class, ['getContents' => $composerContents])];

        $finder = $this->mockery(Finder::class, [
            'getIterator' => (new ArrayObject($files))->getIterator(),
        ]);
        $finder->expects()->in('/path/to/app')->andReturnSelf();
        $finder->expects()->files()->andReturnSelf();
        $finder->expects()->depth('== 0')->andReturnSelf();
        $finder->expects()->name('composer.json');

        return $finder;
    }
}
