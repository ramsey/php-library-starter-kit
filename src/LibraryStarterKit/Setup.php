<?php

/**
 * This file is part of ramsey/php-library-starter-kit
 *
 * @copyright Copyright (c) Ben Ramsey <ben@benramsey.com>
 * @license https://opensource.org/licenses/MIT MIT License
 */

declare(strict_types=1);

namespace Ramsey\Dev\LibraryStarterKit;

use Composer\Script\Event;
use Ramsey\Dev\LibraryStarterKit\Task\Build;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Process\Process;
use Twig\Environment as TwigEnvironment;
use Twig\Loader\FilesystemLoader;
use Twig\TwigFilter;

use function preg_replace;

use const DIRECTORY_SEPARATOR;

class Setup
{
    private Event $event;
    private Filesystem $filesystem;
    private Finder $finder;
    private Project $project;
    private int $verbosity;

    public function __construct(
        Project $project,
        Event $event,
        Filesystem $filesystem,
        Finder $finder,
        int $verbosity,
    ) {
        $this->project = $project;
        $this->event = $event;
        $this->filesystem = $filesystem;
        $this->finder = $finder;
        $this->verbosity = $verbosity;
    }

    /**
     * Returns the absolute path to the directory for the application
     */
    public function getAppPath(): string
    {
        return $this->project->getPath();
    }

    /**
     * Returns the Composer event that triggered this action
     */
    public function getEvent(): Event
    {
        return $this->event;
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
        return $this->finder::create();
    }

    /**
     * Returns the project name
     */
    public function getProjectName(): string
    {
        return $this->project->getName();
    }

    /**
     * Returns the project we are setting up
     */
    public function getProject(): Project
    {
        return $this->project;
    }

    /**
     * Returns the verbosity level
     */
    public function getVerbosity(): int
    {
        return $this->verbosity;
    }

    /**
     * Returns an instance used for executing a system command
     *
     * @param string[] $command
     */
    public function getProcess(array $command): Process
    {
        return new Process($command, $this->getProject()->getPath());
    }

    /**
     * Given a project-relative directory or filename, constructs an absolute path
     */
    public function path(string $fileName): string
    {
        return $this->getProject()->getPath() . DIRECTORY_SEPARATOR . $fileName;
    }

    /**
     * Returns a Build object used to process all the user inputs
     */
    public function getBuild(SymfonyStyle $console, Answers $answers): Build
    {
        return new Build(
            $this,
            $console,
            $answers,
        );
    }

    /**
     * Returns a Twig environment object for rendering Twig templates
     */
    public function getTwigEnvironment(): TwigEnvironment
    {
        $pregReplaceFilter = new TwigFilter(
            'preg_replace',
            /**
             * @param non-empty-string $p
             */
            fn (string $s, string $p, string $r): string => (string) preg_replace($p, $r, $s),
        );

        $twig = new TwigEnvironment(
            new FilesystemLoader($this->getProject()->getPath() . '/resources/templates'),
            [
                'debug' => true,
                'strict_variables' => true,
                'autoescape' => false,
            ],
        );

        $twig->addFilter($pregReplaceFilter);

        return $twig;
    }

    /**
     * Runs the steps to set up the project
     */
    public function run(SymfonyStyle $console, Answers $answers): void
    {
        $this->getBuild($console, $answers)->run();
    }
}
