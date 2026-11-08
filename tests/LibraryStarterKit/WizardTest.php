<?php

declare(strict_types=1);

namespace Ramsey\Test\Dev\LibraryStarterKit;

use Closure;
use Composer\Script\Event;
use Hamcrest\Core\IsInstanceOf;
use Hamcrest\Core\IsTypeOf;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use Ramsey\Dev\LibraryStarterKit\Answers;
use Ramsey\Dev\LibraryStarterKit\Console\InstallQuestions;
use Ramsey\Dev\LibraryStarterKit\Console\Question\AuthorName;
use Ramsey\Dev\LibraryStarterKit\Console\Question\FundingPlatformValue;
use Ramsey\Dev\LibraryStarterKit\Console\Question\SkippableQuestion;
use Ramsey\Dev\LibraryStarterKit\Console\Question\StarterKitQuestion;
use Ramsey\Dev\LibraryStarterKit\Console\Style;
use Ramsey\Dev\LibraryStarterKit\Console\StyleFactory;
use Ramsey\Dev\LibraryStarterKit\Filesystem;
use Ramsey\Dev\LibraryStarterKit\Project;
use Ramsey\Dev\LibraryStarterKit\Setup;
use Ramsey\Dev\LibraryStarterKit\Wizard;
use ReflectionProperty;
use RuntimeException;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Process\Process;

use function array_filter;
use function array_values;
use function basename;
use function dirname;
use function function_exists;
use function get_class;
use function mkdir;
use function pcntl_signal;
use function pcntl_signal_get_handler;
use function preg_replace;
use function putenv;
use function realpath;
use function rmdir;
use function str_starts_with;
use function strtolower;
use function sys_get_temp_dir;
use function uniqid;

use const SIGINT;
use const SIGTERM;
use const SIG_DFL;

class WizardTest extends TestCase
{
    private const string INTRO_BLOCK = 'I\'ll ask you a series of questions about the library '
        . 'you\'re creating. When finished, I\'ll set up a repository with '
        . 'an initial set of files that you may customize to suit your '
        . 'needs.';

    private const string SKIP_PROMPTS_BLOCK = 'You\'ve provided an answers file with \'skipPrompts: true\', '
        . 'so I won\'t ask any questions. Instead, I\'ll go ahead and '
        . 'start setting up a repository with an initial set of files '
        . 'that you may customize to suit your needs.';

    private const string ERROR_BLOCK_URL = 'Please go here and click the "New issue" button to report this error: '
        . 'https://github.com/ramsey/php-library-starter-kit/issues';

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

        $console->expects()->title('Welcome to the PHP Library Starter Kit!');
        $console->expects()->block(self::INTRO_BLOCK);
        $console->expects()->block([
            'I\'ve saved your progress. When you\'re ready to return to the '
            . 'starter kit wizard, enter:',
        ]);
        $console->expects()->text([
            '    <info>cd /path/to/app</info>',
            '    <info>composer starter-kit</info>',
        ]);
        $console->expects()->newLine();
        $console->shouldReceive('success')->never();

        $console
            ->expects()
            ->askQuestion(new IsInstanceOf(ConfirmationQuestion::class))
            ->andReturnUsing(function (ConfirmationQuestion $question): bool {
                $this->assertSame('Are you ready to get started?', $question->getQuestion());
                $this->assertFalse($question->getDefault());

                return false;
            });

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

        $console->expects()->title('Welcome to the PHP Library Starter Kit!');
        $console->expects()->block(self::INTRO_BLOCK);
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

    public function testRunKeepsTheFundingMapWhenFundingQuestionsAreSkipped(): void
    {
        $map = ['github' => ['ramsey']];
        $asked = [];
        $seen = [];

        $this->runWizardAnswering(
            fn (StarterKitQuestion $question): mixed => $question->getName() === 'funding' ? $map : null,
            function (Answers $answers) use (&$seen): void {
                $seen = $answers->funding;
            },
            $asked,
        );

        $this->assertSame($map, $seen);
        $this->assertSame(
            ['funding', 'funding', 'funding:github'],
            array_values(array_filter($asked, fn (string $name): bool => str_starts_with($name, 'funding'))),
        );
    }

    public function testRunSetsSkippedQuestionsToTheirDefaults(): void
    {
        $asked = [];
        $copyrightHolder = null;

        $this->runWizardAnswering(
            fn (Question $question): mixed => $question instanceof AuthorName ? 'Jane Doe' : null,
            function (Answers $answers) use (&$copyrightHolder): void {
                $copyrightHolder = $answers->copyrightHolder;
            },
            $asked,
        );

        $this->assertNotContains('copyrightHolder', $asked);
        $this->assertSame('Jane Doe', $copyrightHolder);
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

        $console->expects()->title('Welcome to the PHP Library Starter Kit!');
        $console->expects()->block(self::SKIP_PROMPTS_BLOCK);
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

    public function testGitUserNameAndEmailAreTrimmed(): void
    {
        /** @var Setup & MockInterface $setup */
        $setup = $this->mockery(Setup::class);
        $setup->shouldReceive('getProject->getName')->andReturn('foo-project');
        $setup->expects()->path('.starter-kit-answers')->andReturn('/path/to/.starter-kit-answers');

        $nameProcess = $this->mockery(Process::class);
        $nameProcess->expects()->run();
        $nameProcess->expects()->getOutput()->andReturn("  Jane Doe \n");
        $emailProcess = $this->mockery(Process::class);
        $emailProcess->expects()->run();
        $emailProcess->expects()->getOutput()->andReturn("\n jane@example.com\n");
        $setup->expects()->getProcess(['git', 'config', 'user.name'])->andReturn($nameProcess);
        $setup->expects()->getProcess(['git', 'config', 'user.email'])->andReturn($emailProcess);

        $filesystem = $this->mockery(Filesystem::class);
        $filesystem->expects()->exists('/path/to/.starter-kit-answers')->andReturnFalse();
        $setup->expects()->getFilesystem()->andReturn($filesystem);

        $wizard = new Wizard($setup);

        $answers = (new ReflectionProperty(Wizard::class, 'answers'))->getValue($wizard);

        $this->assertInstanceOf(Answers::class, $answers);
        $this->assertSame('Jane Doe', $answers->authorName);
        $this->assertSame('jane@example.com', $answers->authorEmail);
    }

    public function testRunRegistersInterruptHandlers(): void
    {
        if (!function_exists('pcntl_signal')) {
            $this->markTestSkipped('The pcntl extension is not available');
        }

        pcntl_signal(SIGINT, SIG_DFL);
        pcntl_signal(SIGTERM, SIG_DFL);

        /** @var InputInterface & MockInterface $input */
        $input = $this->mockery(InputInterface::class)->shouldIgnoreMissing();

        /** @var OutputInterface & MockInterface $output */
        $output = $this->mockery(OutputInterface::class);
        $output->shouldReceive('setVerbosity');

        /** @var Style & MockInterface $console */
        $console = $this->mockery(Style::class);
        $console->shouldReceive('title');
        $console->shouldReceive('block');
        $console->shouldReceive('success');

        $project = new Project('my-project', '/my/project/path');

        /** @var Setup & MockInterface $setup */
        $setup = $this->mockery(Setup::class, ['getProject' => $project]);
        $setup->shouldReceive('getVerbosity')->andReturn(OutputInterface::VERBOSITY_NORMAL);
        $setup->shouldReceive('run');
        $setup->shouldReceive('path')->andReturn('/path/to/.starter-kit-answers');
        $setup->shouldReceive('getFilesystem')->andReturn(new Filesystem());

        $nullProcess = $this->mockery(Process::class);
        $nullProcess->shouldReceive('run');
        $nullProcess->shouldReceive('getOutput')->andReturn('');
        $setup->shouldReceive('getProcess')->andReturn($nullProcess);

        /** @var StyleFactory & MockInterface $styleFactory */
        $styleFactory = $this->mockery(StyleFactory::class);
        $styleFactory->shouldReceive('factory')->andReturn($console);

        putenv('STARTER_KIT_ANSWERS_FILE=' . __DIR__ . '/answers-test.json');

        try {
            $wizard = new Wizard($setup, $styleFactory);
            $wizard->run($input, $output);

            $this->assertInstanceOf(Closure::class, pcntl_signal_get_handler(SIGINT));
            $this->assertInstanceOf(Closure::class, pcntl_signal_get_handler(SIGTERM));
        } finally {
            putenv('STARTER_KIT_ANSWERS_FILE');
            pcntl_signal(SIGINT, SIG_DFL);
            pcntl_signal(SIGTERM, SIG_DFL);
        }
    }

    public function testRunUsesIntegerValueOfStringExceptionCode(): void
    {
        /** @var InputInterface & MockInterface $input */
        $input = $this->mockery(InputInterface::class)->shouldIgnoreMissing();

        /** @var OutputInterface & MockInterface $output */
        $output = $this->mockery(OutputInterface::class);
        $output->shouldReceive('setVerbosity');

        /** @var Style & MockInterface $console */
        $console = $this->mockery(Style::class);
        $console->shouldReceive('title');
        $console->shouldReceive('block');
        $console->shouldReceive('newLine');
        $console->shouldReceive('error');
        $console->shouldReceive('getVerbosity')->andReturn(OutputInterface::VERBOSITY_NORMAL);

        // PDOException is the usual source of a string code.
        $exception = new RuntimeException('a test exception message');
        (new ReflectionProperty(RuntimeException::class, 'code'))->setValue($exception, '7');

        $project = new Project('my-project', '/my/project/path');

        /** @var Setup & MockInterface $setup */
        $setup = $this->mockery(Setup::class, ['getProject' => $project]);
        $setup->shouldReceive('getVerbosity')->andReturn(OutputInterface::VERBOSITY_NORMAL);
        $setup->shouldReceive('run')->andThrow($exception);
        $setup->shouldReceive('path')->andReturn('/path/to/.starter-kit-answers');
        $setup->shouldReceive('getFilesystem')->andReturn(new Filesystem());

        $nullProcess = $this->mockery(Process::class);
        $nullProcess->shouldReceive('run');
        $nullProcess->shouldReceive('getOutput')->andReturn('');
        $setup->shouldReceive('getProcess')->andReturn($nullProcess);

        /** @var StyleFactory & MockInterface $styleFactory */
        $styleFactory = $this->mockery(StyleFactory::class);
        $styleFactory->shouldReceive('factory')->andReturn($console);

        putenv('STARTER_KIT_ANSWERS_FILE=' . __DIR__ . '/answers-test.json');

        try {
            $wizard = new Wizard($setup, $styleFactory);

            $this->assertSame(7, $wizard->run($input, $output));
        } finally {
            putenv('STARTER_KIT_ANSWERS_FILE');
        }
    }

    public function testStartDerivesProjectNameFromDirectoryName(): void
    {
        $appPath = sys_get_temp_dir() . '/Starter_Kit.Test' . uniqid();
        mkdir($appPath . '/vendor', 0o777, true);

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
            ->andReturn($appPath . '/vendor');

        $expectedName = (string) preg_replace('/[^a-z0-9]/', '-', strtolower(basename((string) realpath($appPath))));

        /** @var Application & MockInterface $application */
        $application = $this->mockery(Application::class);
        $application
            ->shouldReceive('addCommand')
            ->once()
            ->withArgs(function (Wizard $command) use ($expectedName): bool {
                $this->assertSame($expectedName, $command->getSetup()->getProject()->getName());
                $this->assertStringStartsWith('starter-kit-test', $command->getSetup()->getProject()->getName());

                return true;
            });
        $application->expects()->setDefaultCommand('starter-kit', true);
        $application->expects()->run(new IsInstanceOf(StringInput::class));

        Wizard::$application = $application;

        try {
            Wizard::start($event);
        } finally {
            Wizard::$application = null;
            rmdir($appPath . '/vendor');
            rmdir($appPath);
        }
    }

    public function testNewApplicationReturnsAnInstanceOfApplication(): void
    {
        /** @phpstan-ignore-next-line */
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
            ->shouldReceive('addCommand')
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

        $console->expects()->block(self::INTRO_BLOCK);
        $console->expects()->block(['Oops! I encountered an error.', self::ERROR_BLOCK_URL]);
        $console->expects()->newLine();
        $console->expects()->title('Welcome to the PHP Library Starter Kit!');
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
     * @return list<array{verbosity: int, exceptionCode: int, expectedReturn: int}>
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
     * @return list<array{isDebug: bool, isVeryVerbose: bool, isVerbose: bool, expectedVerbosity: int}>
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

    /**
     * Runs the wizard with the user choosing to start, answering each question
     * with the value the callback returns for it
     *
     * @param Closure(Question & StarterKitQuestion): mixed $answer Returns null to give the default answer
     * @param Closure(Answers): void $inspect
     * @param list<string> $asked The names of the questions asked, filled in by this method
     */
    private function runWizardAnswering(Closure $answer, Closure $inspect, array &$asked): void
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
        $setup->shouldReceive('run')->once()->withArgs(function (Style $style, Answers $answers) use ($inspect): bool {
            $inspect($answers);

            return true;
        });
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

        $console->expects()->title('Welcome to the PHP Library Starter Kit!');
        $console->expects()->block(self::INTRO_BLOCK);
        $console->expects()->success(new IsTypeOf('array'));

        $console
            ->shouldReceive('askQuestion')
            ->andReturnUsing(function (Question $question) use ($answer, &$asked): mixed {
                if (!$question instanceof StarterKitQuestion) {
                    // The wizard's opening "get started" question.
                    return true;
                }

                $name = $question->getName();

                if ($question instanceof FundingPlatformValue) {
                    $name .= ':' . $question->platform->value;
                }

                $asked[] = $name;

                // A null from the callback means "use the default answer."
                return $answer($question) ?? $this->answers->{$question->getName()};
            });

        $wizard = new Wizard($setup, $styleFactory);

        $this->assertSame(0, $wizard->run($input, $output));
    }
}
