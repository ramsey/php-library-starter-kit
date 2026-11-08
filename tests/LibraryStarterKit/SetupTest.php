<?php

declare(strict_types=1);

namespace Ramsey\Test\Dev\LibraryStarterKit;

use Composer\IO\IOInterface;
use Composer\Script\Event;
use Mockery\MockInterface;
use Ramsey\Dev\LibraryStarterKit\Filesystem;
use Ramsey\Dev\LibraryStarterKit\Project;
use Ramsey\Dev\LibraryStarterKit\Setup;
use Ramsey\Dev\LibraryStarterKit\Task\Build;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Process\Process;
use Twig\Environment as TwigEnvironment;
use Twig\Error\RuntimeError;

use function dirname;
use function implode;
use function str_repeat;

use const DIRECTORY_SEPARATOR;

class SetupTest extends TestCase
{
    private string $appPath;
    private Setup $setup;

    public function setUp(): void
    {
        parent::setUp();

        $this->appPath = dirname(__FILE__, 3);

        /** @var Event & MockInterface $event */
        $event = $this->mockery(Event::class, [
            'getIO' => $this->mockery(IOInterface::class),
        ]);

        /** @var Filesystem & MockInterface $filesystem */
        $filesystem = $this->mockery(Filesystem::class);

        /** @var Finder & MockInterface $finder */
        $finder = $this->mockery(Finder::class);
        $finder->shouldReceive('create')->andReturn(clone $finder);

        $project = new Project('a-project-name', $this->appPath);

        $this->setup = new Setup(
            $project,
            $event,
            $filesystem,
            $finder,
            OutputInterface::VERBOSITY_NORMAL,
        );
    }

    public function testGetAppPath(): void
    {
        $this->assertIsString($this->setup->getAppPath());
    }

    public function testGetEvent(): void
    {
        /** @phpstan-ignore-next-line */
        $this->assertInstanceOf(Event::class, $this->setup->getEvent());
    }

    public function testGetFilesystem(): void
    {
        /** @phpstan-ignore-next-line */
        $this->assertInstanceOf(Filesystem::class, $this->setup->getFilesystem());
    }

    public function testGetFinder(): void
    {
        /** @phpstan-ignore-next-line */
        $this->assertInstanceOf(Finder::class, $this->setup->getFinder());
    }

    public function testGetProjectName(): void
    {
        $this->assertSame('a-project-name', $this->setup->getProjectName());
    }

    public function testGetVerbosity(): void
    {
        $this->assertSame(OutputInterface::VERBOSITY_NORMAL, $this->setup->getVerbosity());
    }

    public function testGetBuild(): void
    {
        /** @var SymfonyStyle & MockInterface $console */
        $console = $this->mockery(SymfonyStyle::class);

        $build = $this->setup->getBuild($console, $this->answers);

        $this->assertSame($this->answers, $build->getAnswers());
        $this->assertSame($console, $build->getConsole());
        $this->assertSame($this->setup, $build->getSetup());
    }

    public function testGetTwigEnvironment(): void
    {
        /** @phpstan-ignore-next-line */
        $this->assertInstanceOf(TwigEnvironment::class, $this->setup->getTwigEnvironment());
    }

    public function testTwigEnvironmentRunsInDebugMode(): void
    {
        $this->assertTrue($this->setup->getTwigEnvironment()->isDebug());
    }

    public function testTwigEnvironmentThrowsForUndefinedVariables(): void
    {
        $twig = $this->setup->getTwigEnvironment();

        $this->expectException(RuntimeError::class);
        $this->expectExceptionMessage('Variable "missing" does not exist');

        $twig->createTemplate('{{ missing }}')->render([]);
    }

    public function testTwigEnvironmentDoesNotEscapeOutput(): void
    {
        $twig = $this->setup->getTwigEnvironment();

        $this->assertSame(
            '<a href="x">&</a>',
            $twig->createTemplate('{{ value }}')->render(['value' => '<a href="x">&</a>']),
        );
    }

    public function testPregReplaceFilterReplacesMatches(): void
    {
        $twig = $this->setup->getTwigEnvironment();

        $this->assertSame(
            'a-b-c',
            $twig->createTemplate("{{ 'a b c' | preg_replace('/\\\\s/', '-') }}")->render([]),
        );
    }

    public function testPregReplaceFilterReturnsEmptyStringWhenReplacementFails(): void
    {
        $twig = $this->setup->getTwigEnvironment();

        // An invalid UTF-8 subject makes preg_replace() fail with a pattern that uses the "u" modifier.
        $this->assertSame(
            '',
            $twig->createTemplate("{{ value | preg_replace('/a/u', 'b') }}")->render(['value' => "a\xB1"]),
        );
    }

    public function testWordwrapFilterWrapsAtSeventyFiveCharactersByDefault(): void
    {
        $twig = $this->setup->getTwigEnvironment();

        // Two words that fill 75 characters, including the space between them, stay on one line.
        $words = [str_repeat('a', 37), str_repeat('b', 37), str_repeat('c', 10)];

        $this->assertSame(
            $words[0] . ' ' . $words[1] . "\n" . $words[2],
            $twig->createTemplate('{{ value | wordwrap }}')->render(['value' => implode(' ', $words)]),
        );

        // A line of 76 characters does not fit, so the last word moves to the next line.
        $words = [str_repeat('a', 37), str_repeat('b', 38)];

        $this->assertSame(
            $words[0] . "\n" . $words[1],
            $twig->createTemplate('{{ value | wordwrap }}')->render(['value' => implode(' ', $words)]),
        );

        // A line of 74 characters fits.
        $words = [str_repeat('a', 37), str_repeat('b', 36)];

        $this->assertSame(
            implode(' ', $words),
            $twig->createTemplate('{{ value | wordwrap }}')->render(['value' => implode(' ', $words)]),
        );
    }

    public function testWordwrapFilterAcceptsWidth(): void
    {
        $twig = $this->setup->getTwigEnvironment();

        $this->assertSame(
            "aaa\nbbb",
            $twig->createTemplate('{{ value | wordwrap(5) }}')->render(['value' => 'aaa bbb']),
        );
    }

    public function testWordwrapFilterDoesNotBreakLongWords(): void
    {
        $twig = $this->setup->getTwigEnvironment();

        $longWord = str_repeat('a', 100);

        $this->assertSame(
            "short\n" . $longWord . "\nshort",
            $twig->createTemplate('{{ value | wordwrap }}')->render(['value' => "short {$longWord} short"]),
        );
    }

    public function testRun(): void
    {
        /** @var SymfonyStyle & MockInterface $console */
        $console = $this->mockery(SymfonyStyle::class);

        $build = $this->mockery(Build::class);
        $build->expects()->run();

        /** @var Setup & MockInterface $setup */
        $setup = $this->mockery(Setup::class, [
            'getBuild' => $build,
        ]);
        $setup->shouldReceive('run')->passthru();

        $setup->run($console, $this->answers);
    }

    public function testGetProcessForCommand(): void
    {
        $process = $this->setup->getProcess(['ls', '-la']);

        /** @phpstan-ignore-next-line */
        $this->assertInstanceOf(Process::class, $process);
    }

    public function testPath(): void
    {
        $this->assertSame(
            $this->appPath . DIRECTORY_SEPARATOR . 'foobar',
            $this->setup->path('foobar'),
        );
    }
}
