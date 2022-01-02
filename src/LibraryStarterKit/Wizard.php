<?php

/**
 * This file is part of ramsey/php-library-starter-kit
 *
 * ramsey/php-library-starter-kit is open source software: you can
 * distribute it and/or modify it under the terms of the MIT License
 * (the "License"). You may not use this file except in
 * compliance with the License.
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or
 * implied. See the License for the specific language governing
 * permissions and limitations under the License.
 *
 * @copyright Copyright (c) Ben Ramsey <ben@benramsey.com>
 * @license https://opensource.org/licenses/MIT MIT License
 */

declare(strict_types=1);

namespace Ramsey\Dev\LibraryStarterKit;

use Composer\Script\Event;
use Ramsey\Dev\LibraryStarterKit\Console\InstallQuestions;
use Ramsey\Dev\LibraryStarterKit\Console\Question\SkippableQuestion;
use Ramsey\Dev\LibraryStarterKit\Console\Question\StarterKitQuestion;
use Ramsey\Dev\LibraryStarterKit\Console\StyleFactory;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Finder\Finder;
use Throwable;

use function basename;
use function dirname;
use function getenv;
use function preg_replace;
use function realpath;
use function sprintf;
use function strtolower;
use function trim;

class Wizard extends Command
{
    private const ANSWERS_FILE = '.starter-kit-answers';

    public static ?Application $application = null;

    private Setup $setup;
    private StyleFactory $styleFactory;
    private Answers $answers;

    public function __construct(Setup $setup, ?StyleFactory $styleFactory = null)
    {
        parent::__construct('starter-kit');

        $this->setup = $setup;
        $this->styleFactory = $styleFactory ?? new StyleFactory();

        $this->answers = new Answers(
            $this->getAnswersFile(),
            $this->setup->getFilesystem(),
        );

        if ($this->answers->projectName === null) {
            $this->answers->projectName = $this->setup->getProject()->getName();
        }

        if ($this->answers->authorName === null) {
            $this->answers->authorName = $this->getGitUserName();
        }

        if ($this->answers->authorEmail === null) {
            $this->answers->authorEmail = $this->getGitUserEmail();
        }
    }

    public function getSetup(): Setup
    {
        return $this->setup;
    }

    public function getAnswersFile(): string
    {
        return getenv('STARTER_KIT_ANSWERS_FILE') ?: $this->setup->path(self::ANSWERS_FILE);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->setVerbosity($this->getSetup()->getVerbosity());

        $console = $this->styleFactory->factory($input, $output);
        $console->title('Welcome to the PHP Library Starter Kit!');

        if ($this->answers->skipPrompts === false) {
            $console->block(
                'I\'ll ask you a series of questions about the library '
                . 'you\'re creating. When finished, I\'ll set up a repository with '
                . 'an initial set of files that you may customize to suit your '
                . 'needs.',
            );
        } else {
            $console->block(
                'You\'ve provided an answers file with \'skipPrompts: true\', '
                . 'so I won\'t ask any questions. Instead, I\'ll go ahead and '
                . 'start setting up a repository with an initial set of files '
                . 'that you may customize to suit your needs.',
            );
        }

        $this->registerInterruptHandler($console);

        try {
            if ($this->answers->skipPrompts === false) {
                if (!$this->confirmStart($console)) {
                    return 0;
                }

                $this->askQuestions($console);
            }

            $this->setup->run($console, $this->answers);
        } catch (Throwable $throwable) {
            return $this->handleException($throwable, $console);
        }

        $console->success([
            sprintf('Congratulations! Your project, %s, is ready!', (string) $this->answers->packageName),
            sprintf('Your project is available at %s.', $this->setup->getProject()->getPath()),
        ]);

        return 0;
    }

    private function getGitUserName(): ?string
    {
        $process = $this->getSetup()->getProcess(
            ['git', 'config', 'user.name'],
        );

        $process->run();

        return trim($process->getOutput()) ?: null;
    }

    private function getGitUserEmail(): ?string
    {
        $process = $this->getSetup()->getProcess(
            ['git', 'config', 'user.email'],
        );

        $process->run();

        return trim($process->getOutput()) ?: null;
    }

    private function confirmStart(SymfonyStyle $console): bool
    {
        $getStarted = new ConfirmationQuestion('Are you ready to get started?', false);

        /** @var bool $confirmStart */
        $confirmStart = $console->askQuestion($getStarted);

        if ($confirmStart) {
            return true;
        }

        $this->exitEarly($console);

        return false;
    }

    private function askQuestions(SymfonyStyle $console): void
    {
        /**
         * @var Question & StarterKitQuestion $question
         */
        foreach ((new InstallQuestions())->getQuestions($this->answers) as $question) {
            if ($question instanceof SkippableQuestion && $question->shouldSkip()) {
                $this->answers->{$question->getName()} = $question->getDefault();

                continue;
            }

            $this->answers->{$question->getName()} = $console->askQuestion($question);
        }
    }

    private function exitEarly(SymfonyStyle $console): void
    {
        $this->answers->saveToFile();

        $console->block([
            'I\'ve saved your progress. When you\'re ready to return to the '
            . 'starter kit wizard, enter:',
        ]);

        $console->text([
            '    <info>cd ' . $this->setup->getAppPath() . '</info>',
            '    <info>composer starter-kit</info>',
        ]);

        $console->newLine();
    }

    /**
     * @codeCoverageIgnore
     */
    private function registerInterruptHandler(SymfonyStyle $console): void
    {
        $interruptHandler = function () use ($console): void {
            $this->exitEarly($console);
            exit(0);
        };

        // phpcs:disable
        if (\function_exists('pcntl_signal')) {
            \pcntl_signal(\SIGINT, $interruptHandler);
            \pcntl_signal(\SIGTERM, $interruptHandler);
        } elseif (\function_exists('sapi_windows_set_ctrl_handler')) {
            \sapi_windows_set_ctrl_handler($interruptHandler);
        }
        // phpcs:enable
    }

    private function handleException(Throwable $throwable, SymfonyStyle $console): int
    {
        $errorMessages = [
            $throwable->getMessage(),
            sprintf('At line %d in %s', $throwable->getLine(), $throwable->getFile()),
        ];

        if ($console->getVerbosity() === OutputInterface::VERBOSITY_DEBUG) {
            $errorMessages[] = $throwable->getTraceAsString();
        }

        $console->error($errorMessages);

        $console->block([
            'Oops! I encountered an error.',
            'Please go here and click the "New issue" button to report this error: '
                . 'https://github.com/ramsey/php-library-starter-kit/issues',
        ]);

        $console->newLine();

        return (int) $throwable->getCode() ?: 1;
    }

    public static function newApplication(): Application
    {
        return self::$application ?? new Application();
    }

    public static function start(Event $event): void
    {
        /** @var string $vendorDir */
        $vendorDir = $event->getComposer()->getConfig()->get('vendor-dir');

        $appPath = dirname($vendorDir);

        $projectName = strtolower(basename((string) realpath($appPath)));
        $projectName = (string) preg_replace('/[^a-z0-9]/', '-', $projectName);

        $project = new Project($projectName, $appPath);
        $setup = new Setup($project, $event, new Filesystem(), new Finder(), self::determineVerbosityLevel($event));

        $command = new self($setup);

        $application = self::newApplication();
        $application->add($command);
        $application->setDefaultCommand((string) $command->getName(), true);

        $application->run(new StringInput('starter-kit'));
    }

    public static function determineVerbosityLevel(Event $event): int
    {
        if ($event->getIO()->isDebug()) {
            return OutputInterface::VERBOSITY_DEBUG;
        } elseif ($event->getIO()->isVeryVerbose()) {
            return OutputInterface::VERBOSITY_VERY_VERBOSE;
        } elseif ($event->getIO()->isVerbose()) {
            return OutputInterface::VERBOSITY_VERBOSE;
        }

        return OutputInterface::VERBOSITY_NORMAL;
    }
}
