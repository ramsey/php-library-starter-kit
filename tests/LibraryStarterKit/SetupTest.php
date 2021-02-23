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
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Process\Process;
use Twig\Environment as TwigEnvironment;

use function dirname;

use const DIRECTORY_SEPARATOR;

class SetupTest extends TestCase
{
    private string $appPath;
    private Setup $setup;

    public function setUp(): void
    {
        parent::setUp();

        $this->appPath = dirname(dirname(dirname(__FILE__)));

        /** @var Event & MockInterface $event */
        $event = $this->mockery(Event::class, [
            'getIO' => $this->mockery(IOInterface::class),
        ]);

        /** @var Filesystem & MockInterface $filesystem */
        $filesystem = $this->mockery(Filesystem::class);

        /** @var Finder & MockInterface $finder */
        $finder = $this->mockery(Finder::class);

        $project = new Project('a-project-name', $this->appPath);

        $this->setup = new Setup(
            $project,
            $event,
            $filesystem,
            $finder,
        );
    }

    public function testGetAppPath(): void
    {
        $this->assertIsString($this->setup->getAppPath());
    }

    public function testGetEvent(): void
    {
        $this->assertInstanceOf(Event::class, $this->setup->getEvent());
    }

    public function testGetFilesystem(): void
    {
        $this->assertInstanceOf(Filesystem::class, $this->setup->getFilesystem());
    }

    public function testGetFinder(): void
    {
        $this->assertInstanceOf(Finder::class, $this->setup->getFinder());
    }

    public function testGetProjectName(): void
    {
        $this->assertSame('a-project-name', $this->setup->getProjectName());
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
        $this->assertInstanceOf(TwigEnvironment::class, $this->setup->getTwigEnvironment());
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
