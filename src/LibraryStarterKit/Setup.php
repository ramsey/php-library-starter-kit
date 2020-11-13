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
use Ramsey\Dev\LibraryStarterKit\Task\Build;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Twig\Environment as TwigEnvironment;
use Twig\Loader\FilesystemLoader;

class Setup
{
    private Event $event;
    private Filesystem $filesystem;
    private Finder $finder;
    private Project $project;

    public function __construct(
        Project $project,
        Event $event,
        Filesystem $filesystem,
        Finder $finder
    ) {
        $this->project = $project;
        $this->event = $event;
        $this->filesystem = $filesystem;
        $this->finder = $finder;
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
     * Returns the IO object from the event triggering this action
     */
    public function getIO(): IOInterface
    {
        return $this->event->getIO();
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
     * Returns a Build object used to process all the user inputs
     */
    public function getBuild(Answers $answers): Build
    {
        $build = new Build(
            $this->getProject()->getPath(),
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
            new FilesystemLoader($this->getProject()->getPath() . '/resources/templates'),
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
    public function run(SymfonyStyle $console, Answers $answers): void
    {
        $this->getBuild($answers)->run();
    }
}
