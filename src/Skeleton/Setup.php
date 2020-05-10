<?php

/**
 * This file is part of ramsey/php-library-skeleton
 *
 * ramsey/php-library-skeleton is open source software: you can
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

namespace Ramsey\Skeleton;

use Composer\IO\IOInterface;
use Composer\Script\Event;
use Ramsey\Skeleton\Task\Answers;
use Ramsey\Skeleton\Task\Build;
use Ramsey\Skeleton\Task\InstallQuestions;
use Ramsey\Skeleton\Task\Prompt;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Twig\Environment as TwigEnvironment;
use Twig\Loader\FilesystemLoader;

use const PHP_EOL;

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

    public function getAppPath(): string
    {
        return $this->appPath;
    }

    public function getEvent(): Event
    {
        return $this->event;
    }

    public function getIO(): IOInterface
    {
        return $this->io;
    }

    public function getFilesystem(): Filesystem
    {
        return $this->filesystem;
    }

    public function getFinder(): Finder
    {
        return $this->finder;
    }

    public function getProjectName(): string
    {
        return $this->projectName;
    }

    public function getPrompt(Answers $answers): Prompt
    {
        $prompt = new Prompt(
            $this->getAppPath(),
            $this->getIO(),
            $this->getFilesystem(),
            $this->getFinder()
        );

        $prompt->setQuestions(new InstallQuestions([
            'projectName' => $this->getProjectName(),
        ]));

        $prompt->setAnswers($answers);

        return $prompt;
    }

    public function getBuild(Answers $answers): Build
    {
        $build = new Build(
            $this->getAppPath(),
            $this->getIO(),
            $this->getFilesystem(),
            $this->getFinder()
        );

        $build->setAnswers($answers);
        $build->setTwigEnvironment($this->getTwigEnvironment());

        return $build;
    }

    public function getTwigEnvironment(): TwigEnvironment
    {
        $twig = new TwigEnvironment(
            new FilesystemLoader($this->getAppPath() . '/resources/templates'),
            [
                'debug' => true,
                'strict_variables' => true,
                'autoescape' => false,
            ]
        );

        return $twig;
    }

    public function run(Answers $answers): void
    {
        $this->getIO()->write('');
        $this->getIO()->write(
            '<info>Welcome to the ramsey/php-library-skeleton wizard!</info>'
        );
        $this->getIO()->write('');
        $this->getIO()->write(
            '<comment>'
            . 'This wizard will take you through a series of questions' . PHP_EOL
            . 'about the library you are creating. When it is finished,' . PHP_EOL
            . 'it will set up a repository with an initial set of files' . PHP_EOL
            . 'that you may customize to suit your needs.'
            . '</comment>'
        );

        $this->getPrompt($answers)->run();
        $this->getBuild($answers)->run();

        $successMessage = sprintf(
            '<info>Congratulations! Your project, %s, is ready!</info>',
            (string) $answers->packageName
        );

        $locationMessage = sprintf(
            '<comment>Your project is available at %s.</comment>',
            $this->getAppPath()
        );

        $this->getIO()->write('');
        $this->getIO()->write($successMessage);
        $this->getIO()->write($locationMessage);
        $this->getIO()->write('');
    }

    public static function wizard(Event $event): void
    {
        $appPath = dirname((string) $event->getComposer()->getConfig()->get('vendor-dir'));

        $projectName = strtolower(basename((string) realpath($appPath)));
        $projectName = (string) preg_replace('/[^a-z0-9]/', '-', $projectName);

        $setup = static::newSelf(
            (string) $projectName,
            (string) $appPath,
            $event
        );

        $setup->run(new Answers());
    }

    public static function newSelf(
        string $projectName,
        string $appPath,
        Event $event
    ): self {
        return new self($projectName, $appPath, $event, new Filesystem(), new Finder());
    }
}
