<?php

declare(strict_types=1);

namespace Ramsey\Test\Dev\LibraryStarterKit;

use Composer\IO\IOInterface;
use Composer\Script\Event;
use Mockery\MockInterface;
use Ramsey\Dev\LibraryStarterKit\Answers;
use Ramsey\Dev\LibraryStarterKit\Project;
use Ramsey\Dev\LibraryStarterKit\Setup;
use Ramsey\Dev\LibraryStarterKit\Task\Build;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Twig\Environment as TwigEnvironment;

use function dirname;

class SetupTest extends TestCase
{
    private Setup $setup;

    public function setUp(): void
    {
        $appPath = dirname(dirname(dirname(__FILE__)));

        /** @var Event & MockInterface $event */
        $event = $this->mockery(Event::class, [
            'getIO' => $this->mockery(IOInterface::class),
        ]);

        /** @var Filesystem & MockInterface $filesystem */
        $filesystem = $this->mockery(Filesystem::class);

        /** @var Finder & MockInterface $finder */
        $finder = $this->mockery(Finder::class);

        $project = new Project('a-project-name', $appPath);

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

        $answers = new Answers();
        $build = $this->setup->getBuild($console, $answers);

        $this->assertSame($answers, $build->getAnswers());
        $this->assertSame($console, $build->getConsole());
        $this->assertSame($this->setup, $build->getSetup());
    }

    public function testGetTwigEnvironment(): void
    {
        $this->assertInstanceOf(TwigEnvironment::class, $this->setup->getTwigEnvironment());
    }

    public function testRun(): void
    {
        $answers = new Answers();
        $answers->projectName = 'project-name';
        $answers->packageName = 'vendor/package-name';

        /** @var SymfonyStyle & MockInterface $console */
        $console = $this->mockery(SymfonyStyle::class);

        $io = $this->mockery(IOInterface::class);

        $build = $this->mockery(Build::class);
        $build->expects()->run();

        /** @var Setup & MockInterface $setup */
        $setup = $this->mockery(Setup::class, [
            'getIO' => $io,
            'getBuild' => $build,
            'getProject' => new Project('project-name', '/path/to/app'),
        ]);
        $setup->shouldReceive('run')->passthru();

        $setup->run($console, $answers);
    }
}
