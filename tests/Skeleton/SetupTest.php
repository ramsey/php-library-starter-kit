<?php

declare(strict_types=1);

namespace Ramsey\Test\Skeleton;

use Composer\Config;
use Composer\IO\IOInterface;
use Composer\Script\Event;
use Mockery\MockInterface;
use Ramsey\Skeleton\Setup;
use Ramsey\Skeleton\Task\Answers;
use Ramsey\Skeleton\Task\Build;
use Ramsey\Skeleton\Task\InstallQuestions;
use Ramsey\Skeleton\Task\Prompt;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Twig\Environment as TwigEnvironment;

use function anInstanceOf;
use function dirname;

use const PHP_EOL;

class SetupTest extends SkeletonTestCase
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

        $this->setup = new Setup(
            'a-project-name',
            $appPath,
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

    public function testGetIO(): void
    {
        $this->assertInstanceOf(IOInterface::class, $this->setup->getIO());
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

    public function testGetPrompt(): void
    {
        $prompt = $this->setup->getPrompt(new Answers());

        $this->assertInstanceOf(InstallQuestions::class, $prompt->getQuestions());
        $this->assertInstanceOf(Answers::class, $prompt->getAnswers());
    }

    public function testGetBuild(): void
    {
        $answers = new Answers();
        $build = $this->setup->getBuild($answers);

        $this->assertSame($answers, $build->getAnswers());
        $this->assertIsString($build->getAppPath());
        $this->assertInstanceOf(TwigEnvironment::class, $build->getTwigEnvironment());
    }

    public function testGetTwigEnvironment(): void
    {
        $this->assertInstanceOf(TwigEnvironment::class, $this->setup->getTwigEnvironment());
    }

    public function testRun(): void
    {
        $answers = new Answers();
        $answers->packageName = 'vendor/package-name';

        $io = $this->mockery(IOInterface::class);
        $io->expects()->write('')->times(4);
        $io->expects()->write('<info>Welcome to the ramsey/php-library-skeleton wizard!</info>');
        $io->expects()->write('<info>Congratulations! Your project, vendor/package-name, is ready!</info>');
        $io->expects()->write('<comment>Your project is available at /path/to/app.</comment>');
        $io->expects()->write(
            '<comment>'
            . 'This wizard will take you through a series of questions' . PHP_EOL
            . 'about the library you are creating. When it is finished,' . PHP_EOL
            . 'it will set up a repository with an initial set of files' . PHP_EOL
            . 'that you may customize to suit your needs.'
            . '</comment>',
        );

        $prompt = $this->mockery(Prompt::class);
        $prompt->expects()->run();

        $build = $this->mockery(Build::class);
        $build->expects()->run();

        /** @var Setup & MockInterface $setup */
        $setup = $this->mockery(Setup::class, [
            'getAppPath' => '/path/to/app',
            'getIO' => $io,
            'getPrompt' => $prompt,
            'getBuild' => $build,
        ]);
        $setup->shouldReceive('run')->passthru();

        $setup->run($answers);
    }

    public function testNewSelf(): void
    {
        /** @var Event & MockInterface $event */
        $event = $this->mockery(Event::class, [
            'getIO' => $this->mockery(IOInterface::class),
        ]);

        $setup = Setup::newSelf('a-project-name', '/path/to/project', $event);

        $this->assertInstanceOf(Setup::class, $setup);
    }

    public function testWizard(): void
    {
        $vendorDir = dirname(dirname(dirname(__FILE__))) . '/vendor';
        $appPath = dirname($vendorDir);

        $config = $this->mockery(Config::class);
        $config->expects()->get('vendor-dir')->andReturn($vendorDir);

        /** @var Event & MockInterface $event */
        $event = $this->mockery(Event::class);
        $event->shouldReceive('getComposer->getConfig')->andReturn($config);

        /** @var Setup & MockInterface $setup */
        $setup = $this->mockery(Setup::class);
        $setup->shouldReceive('wizard')->passthru();

        $setup
            ->expects()
            ->newSelf('php-library-skeleton', $appPath, $event)
            ->andReturn($setup);

        $setup->expects()->run(anInstanceOf(Answers::class));

        $setup::wizard($event);
    }
}
