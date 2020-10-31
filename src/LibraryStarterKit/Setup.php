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

use Composer\IO\IOInterface;
use Composer\Script\Event;
use Ramsey\Dev\LibraryStarterKit\Task\Answers;
use Ramsey\Dev\LibraryStarterKit\Task\Build;
use Ramsey\Dev\LibraryStarterKit\Task\InstallQuestions;
use Ramsey\Dev\LibraryStarterKit\Task\Prompt;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Twig\Environment as TwigEnvironment;
use Twig\Loader\FilesystemLoader;

use function basename;
use function dirname;
use function preg_replace;
use function realpath;
use function sprintf;
use function strtolower;

use const PHP_EOL;

/**
 * Composer's post-create-project-cmd uses Setup::wizard() to walk you through
 * a series of questions to set up a repository to use for developing a PHP
 * library
 */
class Setup
{
    private Event $event;
    private Filesystem $filesystem;
    private Finder $finder;
    private IOInterface $io;
    private string $appPath;
    private string $projectName;

    public function __construct(
        string $projectName,
        string $appPath,
        Event $event,
        Filesystem $filesystem,
        Finder $finder
    ) {
        $this->appPath = $appPath;
        $this->event = $event;
        $this->filesystem = $filesystem;
        $this->finder = $finder;
        $this->io = $event->getIO();
        $this->projectName = $projectName;
    }

    /**
     * Returns the absolute path to the directory for the application
     */
    public function getAppPath(): string
    {
        return $this->appPath;
    }

    /**
     * Returns the Composer event that triggered this action
     */
    public function getEvent(): Event
    {
        return $this->event;
    }

    /**
     * Returns the IO object from the event triggering this action
     */
    public function getIO(): IOInterface
    {
        return $this->io;
    }

    /**
     * Returns a filesystem object to use when executing filesystem commands
     */
    public function getFilesystem(): Filesystem
    {
        return $this->filesystem;
    }

    /**
     * Returns an object used to search for files or directories
     */
    public function getFinder(): Finder
    {
        return $this->finder;
    }

    /**
     * Returns the project name, based on the directory name
     */
    public function getProjectName(): string
    {
        return $this->projectName;
    }

    /**
     * Returns a prompt to ask the user a question for input
     */
    public function getPrompt(Answers $answers): Prompt
    {
        $prompt = new Prompt(
            $this->getAppPath(),
            $this->getIO(),
            $this->getFilesystem(),
            $this->getFinder(),
        );

        $prompt->setQuestions(new InstallQuestions([
            'projectName' => $this->getProjectName(),
        ]));

        $prompt->setAnswers($answers);

        return $prompt;
    }

    /**
     * Returns a Build object used to process all the user inputs
     */
    public function getBuild(Answers $answers): Build
    {
        $build = new Build(
            $this->getAppPath(),
            $this->getIO(),
            $this->getFilesystem(),
            $this->getFinder(),
        );

        $build->setAnswers($answers);
        $build->setTwigEnvironment($this->getTwigEnvironment());

        return $build;
    }

    /**
     * Returns a Twig environment object for rendering Twig templates
     */
    public function getTwigEnvironment(): TwigEnvironment
    {
        return new TwigEnvironment(
            new FilesystemLoader($this->getAppPath() . '/resources/templates'),
            [
                'debug' => true,
                'strict_variables' => true,
                'autoescape' => false,
            ],
        );
    }

    /**
     * Runs the steps to set up the project
     */
    public function run(Answers $answers): void
    {
        $this->getIO()->write('');
        $this->getIO()->write(
            '<info>Welcome to the ramsey/php-library-starter-kit wizard!</info>',
        );
        $this->getIO()->write('');
        $this->getIO()->write(
            '<comment>'
            . 'This wizard will take you through a series of questions' . PHP_EOL
            . 'about the library you are creating. When it is finished,' . PHP_EOL
            . 'it will set up a repository with an initial set of files' . PHP_EOL
            . 'that you may customize to suit your needs.'
            . '</comment>',
        );

        $this->getPrompt($answers)->run();
        $this->getBuild($answers)->run();

        $successMessage = sprintf(
            '<info>Congratulations! Your project, %s, is ready!</info>',
            (string) $answers->packageName,
        );

        $locationMessage = sprintf(
            '<comment>Your project is available at %s.</comment>',
            $this->getAppPath(),
        );

        $this->getIO()->write('');
        $this->getIO()->write($successMessage);
        $this->getIO()->write($locationMessage);
        $this->getIO()->write('');
    }

    /**
     * Executes the setup wizard
     */
    public static function wizard(Event $event): void
    {
        $appPath = dirname((string) $event->getComposer()->getConfig()->get('vendor-dir'));

        $projectName = strtolower(basename((string) realpath($appPath)));
        $projectName = (string) preg_replace('/[^a-z0-9]/', '-', $projectName);

        $setup = static::newSelf(
            (string) $projectName,
            (string) $appPath,
            $event,
        );

        $setup->run(new Answers());
    }

    /**
     * Returns a new Setup instance
     */
    public static function newSelf(
        string $projectName,
        string $appPath,
        Event $event
    ): self {
        return new self($projectName, $appPath, $event, new Filesystem(), new Finder());
    }
}
