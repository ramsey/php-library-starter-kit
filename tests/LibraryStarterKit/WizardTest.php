<?php

declare(strict_types=1);

namespace Ramsey\Test\Dev\LibraryStarterKit;

use Composer\Script\Event;
use Mockery\MockInterface;
use Ramsey\Dev\LibraryStarterKit\Answers;
use Ramsey\Dev\LibraryStarterKit\Console\SymfonyStyleFactory;
use Ramsey\Dev\LibraryStarterKit\Project;
use Ramsey\Dev\LibraryStarterKit\Setup;
use Ramsey\Dev\LibraryStarterKit\Wizard;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Style\SymfonyStyle;

use function dirname;
use function realpath;

class WizardTest extends TestCase
{
    public function testGetSetup(): void
    {
        /** @var Setup & MockInterface $setup */
        $setup = $this->mockery(Setup::class);

        $wizard = new Wizard($setup);

        $this->assertSame($setup, $wizard->getSetup());
    }

    public function testRunWhenUserChoosesNotToStart(): void
    {
        /** @var InputInterface & MockInterface $input */
        $input = $this->mockery(InputInterface::class)->shouldIgnoreMissing();

        /** @var OutputInterface & MockInterface $output */
        $output = $this->mockery(OutputInterface::class);

        /** @var SymfonyStyle & MockInterface $console */
        $console = $this->mockery(SymfonyStyle::class);

        /** @var Setup & MockInterface $setup */
        $setup = $this->mockery(Setup::class);

        /** @var SymfonyStyleFactory & MockInterface $styleFactory */
        $styleFactory = $this->mockery(SymfonyStyleFactory::class);
        $styleFactory->expects()->factory($input, $output)->andReturn($console);

        $console->shouldReceive('title')->once();
        $console->shouldReceive('block');
        $console->shouldReceive('text');
        $console->shouldReceive('newLine');
        $console->shouldReceive('success')->never();

        $console
            ->shouldReceive('askQuestion')
            ->with(anInstanceOf(ConfirmationQuestion::class))
            ->andReturnFalse();

        $wizard = new Wizard($setup, $styleFactory);

        $this->assertSame(0, $wizard->run($input, $output));
    }

    public function testRunWhenUserConfirmsStart(): void
    {
        /** @var InputInterface & MockInterface $input */
        $input = $this->mockery(InputInterface::class)->shouldIgnoreMissing();

        /** @var OutputInterface & MockInterface $output */
        $output = $this->mockery(OutputInterface::class);

        /** @var SymfonyStyle & MockInterface $console */
        $console = $this->mockery(SymfonyStyle::class);

        $project = new Project('my-project', '/my/project/path');

        /** @var Setup & MockInterface $setup */
        $setup = $this->mockery(Setup::class, [
            'getProject' => $project,
        ]);

        $setup
            ->shouldReceive('run')
            ->once()
            ->withArgs(function (SymfonyStyle $style, Answers $answers) use ($console): bool {
                $answers->packageName = 'my-package/name';
                $this->assertSame($console, $style);

                return true;
            });

        /** @var SymfonyStyleFactory & MockInterface $styleFactory */
        $styleFactory = $this->mockery(SymfonyStyleFactory::class);
        $styleFactory->expects()->factory($input, $output)->andReturn($console);

        $console->shouldReceive('title')->once();
        $console->shouldReceive('block');
        $console->expects()->success([
            'Congratulations! Your project, my-package/name, is ready!',
            'Your project is available at /my/project/path.',
        ]);

        $console
            ->shouldReceive('askQuestion')
            ->with(anInstanceOf(ConfirmationQuestion::class))
            ->andReturnTrue();

        $wizard = new Wizard($setup, $styleFactory);

        $this->assertSame(0, $wizard->run($input, $output));
    }

    public function testNewApplicationReturnsAnInstanceOfApplication(): void
    {
        $this->assertInstanceOf(Application::class, Wizard::newApplication());
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testStart(): void
    {
        $vendorDir = (string) realpath(__DIR__ . '/../../vendor');

        /** @var Event & MockInterface $event */
        $event = $this->mockery(Event::class);
        $event
            ->shouldReceive('getComposer->getConfig->get')
            ->with('vendor-dir')
            ->once()
            ->andReturn($vendorDir);

        /** @var Application & MockInterface $application */
        $application = $this->mockery(Application::class);

        $application
            ->shouldReceive('add')
            ->once()
            ->withArgs(function (Wizard $command) use ($vendorDir): bool {
                $this->assertSame('php-library-starter-kit', $command->getSetup()->getProject()->getName());
                $this->assertSame(dirname($vendorDir), $command->getSetup()->getProject()->getPath());

                return true;
            });

        $application->expects()->setDefaultCommand('starter-kit', true);
        $application->expects()->run();

        Wizard::$application = $application;
        Wizard::start($event);
    }
}
