<?php

declare(strict_types=1);

namespace Ramsey\Test\Dev\LibraryStarterKit;

use Composer\Script\Event;
use Hamcrest\Core\IsInstanceOf;
use Hamcrest\Core\IsTypeOf;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use Ramsey\Dev\LibraryStarterKit\Answers;
use Ramsey\Dev\LibraryStarterKit\Console\InstallQuestions;
use Ramsey\Dev\LibraryStarterKit\Console\Question\SkippableQuestion;
use Ramsey\Dev\LibraryStarterKit\Console\Question\StarterKitQuestion;
use Ramsey\Dev\LibraryStarterKit\Console\Style;
use Ramsey\Dev\LibraryStarterKit\Console\StyleFactory;
use Ramsey\Dev\LibraryStarterKit\Filesystem;
use Ramsey\Dev\LibraryStarterKit\Project;
use Ramsey\Dev\LibraryStarterKit\Setup;
use Ramsey\Dev\LibraryStarterKit\Wizard;
use RuntimeException;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Process\Process;

use function dirname;
use function get_class;
use function putenv;
use function realpath;

class WizardTest extends TestCase
{
    public function testGetSetup(): void
    {
        /** @var Setup & MockInterface $setup */
        $setup = $this->mockery(Setup::class);

        $setup->shouldReceive('getProject->getName')->andReturn('foo-project');

        $setup
            ->expects()
            ->path('.starter-kit-answers')
            ->andReturn('/path/to/.starter-kit-answers');

        $nullProcess = $this->mockery(Process::class);
        $nullProcess->expects()->run()->twice();
        $nullProcess->expects()->getOutput()->twice()->andReturn('');
        $setup->expects()->getProcess(['git', 'config', 'user.name'])->andReturn($nullProcess);
        $setup->expects()->getProcess(['git', 'config', 'user.email'])->andReturn($nullProcess);

        $filesystem = $this->mockery(Filesystem::class);
        $filesystem->expects()->exists('/path/to/.starter-kit-answers')->andReturnFalse();

        $setup->expects()->getFilesystem()->andReturn($filesystem);

        $wizard = new Wizard($setup);

        $this->assertSame($setup, $wizard->getSetup());
    }

    public function testGetAnswersFileReturnsPathToLocalAnswersFile(): void
    {
        /** @var Setup & MockInterface $setup */
        $setup = $this->mockery(Setup::class);

        $setup->shouldReceive('getProject->getName')->andReturn('foo-project');

        $setup
            ->expects()
            ->path('.starter-kit-answers')
            ->twice()
            ->andReturn('/path/to/.starter-kit-answers');

        $nullProcess = $this->mockery(Process::class);
        $nullProcess->expects()->run()->twice();
        $nullProcess->expects()->getOutput()->twice()->andReturn('');
        $setup->expects()->getProcess(['git', 'config', 'user.name'])->andReturn($nullProcess);
        $setup->expects()->getProcess(['git', 'config', 'user.email'])->andReturn($nullProcess);

        $filesystem = $this->mockery(Filesystem::class);
        $filesystem->expects()->exists('/path/to/.starter-kit-answers')->andReturnFalse();

        $setup->expects()->getFilesystem()->andReturn($filesystem);

        $wizard = new Wizard($setup);

        $this->assertSame('/path/to/.starter-kit-answers', $wizard->getAnswersFile());
    }

    public function testGetAnswersFileReturnsPathToEnvironmentAnswersFile(): void
    {
        $answersFile = __DIR__ . '/answers-test.json';

        putenv('STARTER_KIT_ANSWERS_FILE=' . $answersFile);

        $filesystem = new Filesystem();

        /** @var Setup & MockInterface $setup */
        $setup = $this->mockery(Setup::class);
        $setup->expects()->getFilesystem()->andReturn($filesystem);

        $wizard = new Wizard($setup);

        $this->assertSame($answersFile, $wizard->getAnswersFile());

        // Remove the environment variable to avoid affecting other tests.
        putenv('STARTER_KIT_ANSWERS_FILE');
    }

    public function testRunWhenUserChoosesNotToStart(): void
    {
        /** @var InputInterface & MockInterface $input */
        $input = $this->mockery(InputInterface::class)->shouldIgnoreMissing();

        /** @var OutputInterface & MockInterface $output */
        $output = $this->mockery(OutputInterface::class);
        $output->expects()->setVerbosity(OutputInterface::VERBOSITY_NORMAL);

        /** @var Style & MockInterface $console */
        $console = $this->mockery(Style::class);

        /** @var Setup & MockInterface $setup */
        $setup = $this->mockery(Setup::class);
        $setup->expects()->getAppPath()->andReturn('/path/to/app');
        $setup->shouldReceive('getProject->getName')->andReturn('foo-project');
        $setup->expects()->getVerbosity()->andReturn(OutputInterface::VERBOSITY_NORMAL);

        $setup
            ->expects()
            ->path('.starter-kit-answers')
            ->andReturn('/path/to/app/.starter-kit-answers');

        $nullProcess = $this->mockery(Process::class);
        $nullProcess->expects()->run()->twice();
        $nullProcess->expects()->getOutput()->twice()->andReturn('');
        $setup->expects()->getProcess(['git', 'config', 'user.name'])->andReturn($nullProcess);
        $setup->expects()->getProcess(['git', 'config', 'user.email'])->andReturn($nullProcess);

        $filesystem = $this->mockery(Filesystem::class);
        $filesystem->expects()->exists('/path/to/app/.starter-kit-answers')->andReturnFalse();
        $filesystem->expects()->dumpFile('/path/to/app/.starter-kit-answers', new IsTypeOf('string'));

        $setup->expects()->getFilesystem()->andReturn($filesystem);

        /** @var StyleFactory & MockInterface $styleFactory */
        $styleFactory = $this->mockery(StyleFactory::class);
        $styleFactory->expects()->factory($input, $output)->andReturn($console);

        $console->shouldReceive('title')->once();
        $console->shouldReceive('block');
        $console->shouldReceive('text');
        $console->shouldReceive('newLine');
        $console->shouldReceive('success')->never();

        $console
            ->shouldReceive('askQuestion')
            ->with(new IsInstanceOf(ConfirmationQuestion::class))
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
        $output->expects()->setVerbosity(OutputInterface::VERBOSITY_NORMAL);

        /** @var Style & MockInterface $console */
        $console = $this->mockery(Style::class);

        $project = new Project('my-project', '/my/project/path');

        /** @var Setup & MockInterface $setup */
        $setup = $this->mockery(Setup::class, [
            'getProject' => $project,
        ]);

        $setup->expects()->getVerbosity()->andReturn(OutputInterface::VERBOSITY_NORMAL);

        $setup
            ->shouldReceive('run')
            ->once()
            ->withArgs(function (Style $style, Answers $answers) use ($console): bool {
                $answers->packageName = 'my-package/name';
                $this->assertSame($console, $style);

                return true;
            });

        $setup
            ->expects()
            ->path('.starter-kit-answers')
            ->andReturn('/path/to/.starter-kit-answers');

        $nullProcess = $this->mockery(Process::class);
        $nullProcess->expects()->run()->twice();
        $nullProcess->expects()->getOutput()->twice()->andReturn('');
        $setup->expects()->getProcess(['git', 'config', 'user.name'])->andReturn($nullProcess);
        $setup->expects()->getProcess(['git', 'config', 'user.email'])->andReturn($nullProcess);

        $filesystem = $this->mockery(Filesystem::class);
        $filesystem->expects()->exists('/path/to/.starter-kit-answers')->andReturnFalse();

        $setup->expects()->getFilesystem()->andReturn($filesystem);

        /** @var StyleFactory & MockInterface $styleFactory */
        $styleFactory = $this->mockery(StyleFactory::class);
        $styleFactory->expects()->factory($input, $output)->andReturn($console);

        $console->shouldReceive('title')->once();
        $console->shouldReceive('block');
        $console->expects()->success([
            'Congratulations! Your project, my-package/name, is ready!',
            'Your project is available at /my/project/path.',
        ]);

        $console
            ->expects()
            ->askQuestion(new IsInstanceOf(ConfirmationQuestion::class))
            ->andReturnTrue();

        $defaultAnswers = $this->answers;

        /** @var Question & StarterKitQuestion $question */
        foreach ((new InstallQuestions())->getQuestions($defaultAnswers) as $question) {
            if ($question instanceof SkippableQuestion && $question->shouldSkip()) {
                continue;
            }

            $console
                ->expects()
                ->askQuestion(new IsInstanceOf(get_class($question))) // phpcs:ignore
                ->andReturn($defaultAnswers->{$question->getName()});
        }

        $wizard = new Wizard($setup, $styleFactory);

        $this->assertSame(0, $wizard->run($input, $output));
    }

    public function testRunWhenUsingAnswersFileWhenSkipPromptsIsTrue(): void
    {
        $answersFile = __DIR__ . '/answers-test.json';

        putenv('STARTER_KIT_ANSWERS_FILE=' . $answersFile);

        /** @var InputInterface & MockInterface $input */
        $input = $this->mockery(InputInterface::class)->shouldIgnoreMissing();

        /** @var OutputInterface & MockInterface $output */
        $output = $this->mockery(OutputInterface::class);
        $output->expects()->setVerbosity(OutputInterface::VERBOSITY_NORMAL);

        /** @var Style & MockInterface $console */
        $console = $this->mockery(Style::class);

        $project = new Project('my-project', '/my/project/path');

        /** @var Setup & MockInterface $setup */
        $setup = $this->mockery(Setup::class, [
            'getProject' => $project,
        ]);

        $setup->expects()->getVerbosity()->andReturn(OutputInterface::VERBOSITY_NORMAL);

        $setup
            ->shouldReceive('run')
            ->once()
            ->withArgs(function (Style $style, Answers $answers) use ($console): bool {
                $this->assertSame($console, $style);

                return true;
            });

        $setup->expects()->getFilesystem()->andReturn(new Filesystem());

        /** @var StyleFactory & MockInterface $styleFactory */
        $styleFactory = $this->mockery(StyleFactory::class);
        $styleFactory->expects()->factory($input, $output)->andReturn($console);

        $console->shouldReceive('title')->once();
        $console->shouldReceive('block');
        $console->expects()->success([
            'Congratulations! Your project, fellowship/ring, is ready!',
            'Your project is available at /my/project/path.',
        ]);

        $console->shouldNotReceive('askQuestion');

        $wizard = new Wizard($setup, $styleFactory);

        $this->assertSame(0, $wizard->run($input, $output));

        // Remove the environment variable to avoid affecting other tests.
        putenv('STARTER_KIT_ANSWERS_FILE');
    }

    public function testNewApplicationReturnsAnInstanceOfApplication(): void
    {
        $this->assertInstanceOf(Application::class, Wizard::newApplication());
    }

    public function testStart(): void
    {
        $vendorDir = (string) realpath(__DIR__ . '/../../vendor');

        /** @var Event & MockInterface $event */
        $event = $this->mockery(Event::class, [
            'getIO->isDebug' => false,
            'getIO->isVeryVerbose' => false,
            'getIO->isVerbose' => false,
        ]);
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
        $application->expects()->run(new IsInstanceOf(StringInput::class));

        Wizard::$application = $application;
        Wizard::start($event);

        // Restore static property to null to avoid conflicts with other tests.
        Wizard::$application = null;
    }

    #[DataProvider('runWhenExceptionIsThrownWithVerbosityProvider')]
    public function testRunWhenExceptionIsThrownWithVerbosity(
        int $verbosity,
        int $exceptionCode,
        int $expectedReturn,
    ): void {
        /** @var InputInterface & MockInterface $input */
        $input = $this->mockery(InputInterface::class)->shouldIgnoreMissing();

        /** @var OutputInterface & MockInterface $output */
        $output = $this->mockery(OutputInterface::class);
        $output->expects()->setVerbosity($verbosity);

        /** @var Style & MockInterface $console */
        $console = $this->mockery(Style::class);

        $project = new Project('my-project', '/my/project/path');

        // phpcs:disable
        $exceptionLine = __LINE__; $exception = new RuntimeException('a test exception message', $exceptionCode);
        // phpcs:enable

        /** @var Setup & MockInterface $setup */
        $setup = $this->mockery(Setup::class, ['getProject' => $project]);
        $setup->expects()->getVerbosity()->andReturn($verbosity);
        $setup->shouldReceive('run')->once()->andThrow($exception);
        $setup->expects()->path('.starter-kit-answers')->andReturn('/path/to/.starter-kit-answers');

        $nullProcess = $this->mockery(Process::class);
        $nullProcess->expects()->run()->twice();
        $nullProcess->expects()->getOutput()->twice()->andReturn('');
        $setup->expects()->getProcess(['git', 'config', 'user.name'])->andReturn($nullProcess);
        $setup->expects()->getProcess(['git', 'config', 'user.email'])->andReturn($nullProcess);

        $filesystem = $this->mockery(Filesystem::class);
        $filesystem->expects()->exists('/path/to/.starter-kit-answers')->andReturnFalse();

        $setup->expects()->getFilesystem()->andReturn($filesystem);

        /** @var StyleFactory & MockInterface $styleFactory */
        $styleFactory = $this->mockery(StyleFactory::class);
        $styleFactory->expects()->factory($input, $output)->andReturn($console);

        $console->shouldReceive('block');
        $console->shouldReceive('newLine');
        $console->shouldReceive('title')->once();
        $console->shouldReceive('success')->never();
        $console->shouldReceive('getVerbosity')->andReturn($verbosity);

        $console
            ->expects()
            ->askQuestion(new IsInstanceOf(ConfirmationQuestion::class))
            ->andReturnTrue();

        $defaultAnswers = $this->answers;

        /** @var Question & StarterKitQuestion $question */
        foreach ((new InstallQuestions())->getQuestions($defaultAnswers) as $question) {
            if ($question instanceof SkippableQuestion && $question->shouldSkip()) {
                continue;
            }

            $console
                ->expects()
                ->askQuestion(new IsInstanceOf(get_class($question))) // phpcs:ignore
                ->andReturn($defaultAnswers->{$question->getName()});
        }

        $expectedErrorMessages = [
            'a test exception message',
            'At line ' . $exceptionLine . ' in ' . __FILE__,
        ];

        if ($verbosity === OutputInterface::VERBOSITY_DEBUG) {
            $expectedErrorMessages[] = $exception->getTraceAsString();
        }

        $console->expects()->error($expectedErrorMessages);

        $wizard = new Wizard($setup, $styleFactory);

        $this->assertSame($expectedReturn, $wizard->run($input, $output));
    }

    /**
     * @return array<array{verbosity: int, exceptionCode: int, expectedReturn: int}>
     */
    public static function runWhenExceptionIsThrownWithVerbosityProvider(): array
    {
        return [
            [
                'verbosity' => OutputInterface::VERBOSITY_NORMAL,
                'exceptionCode' => 0,
                'expectedReturn' => 1,
            ],
            [
                'verbosity' => OutputInterface::VERBOSITY_VERBOSE,
                'exceptionCode' => 2,
                'expectedReturn' => 2,
            ],
            [
                'verbosity' => OutputInterface::VERBOSITY_VERY_VERBOSE,
                'exceptionCode' => 3,
                'expectedReturn' => 3,
            ],
            [
                'verbosity' => OutputInterface::VERBOSITY_DEBUG,
                'exceptionCode' => 4,
                'expectedReturn' => 4,
            ],
        ];
    }

    #[DataProvider('determineVerbosityLevelProvider')]
    public function testDetermineVerbosityLevel(
        bool $isDebug,
        bool $isVeryVerbose,
        bool $isVerbose,
        int $expectedVerbosity,
    ): void {
        /** @var Event & MockInterface $event */
        $event = $this->mockery(Event::class, [
            'getIO->isDebug' => $isDebug,
            'getIO->isVeryVerbose' => $isVeryVerbose,
            'getIO->isVerbose' => $isVerbose,
        ]);

        $this->assertSame($expectedVerbosity, Wizard::determineVerbosityLevel($event));
    }

    /**
     * @return array<array{isDebug: bool, isVeryVerbose: bool, isVerbose: bool, expectedVerbosity: int}>
     */
    public static function determineVerbosityLevelProvider(): array
    {
        return [
            [
                'isDebug' => false,
                'isVeryVerbose' => false,
                'isVerbose' => false,
                'expectedVerbosity' => OutputInterface::VERBOSITY_NORMAL,
            ],
            [
                'isDebug' => true,
                'isVeryVerbose' => false,
                'isVerbose' => false,
                'expectedVerbosity' => OutputInterface::VERBOSITY_DEBUG,
            ],
            [
                'isDebug' => false,
                'isVeryVerbose' => true,
                'isVerbose' => false,
                'expectedVerbosity' => OutputInterface::VERBOSITY_VERY_VERBOSE,
            ],
            [
                'isDebug' => false,
                'isVeryVerbose' => false,
                'isVerbose' => true,
                'expectedVerbosity' => OutputInterface::VERBOSITY_VERBOSE,
            ],
        ];
    }
}
